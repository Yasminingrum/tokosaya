<?php
// File: app/Services/ProductService.php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Create new product
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure unique slug
            $data['slug'] = $this->ensureUniqueSlug($data['slug']);

            // Create product
            $product = Product::create($data);

            // Handle images
            if (isset($data['images'])) {
                $this->handleProductImages($product, $data['images']);
            }

            // Handle variants
            if (isset($data['variants'])) {
                $this->handleProductVariants($product, $data['variants']);
            }

            // Handle attributes
            if (isset($data['attributes'])) {
                $this->handleProductAttributes($product, $data['attributes']);
            }

            return $product;
        });
    }

    /**
     * Update product
     */
    public function update(Product $product, array $data)
    {
        return DB::transaction(function () use ($product, $data) {
            // Generate slug if name changed
            if (isset($data['name']) && $data['name'] !== $product->name && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure unique slug
            if (isset($data['slug']) && $data['slug'] !== $product->slug) {
                $data['slug'] = $this->ensureUniqueSlug($data['slug'], $product->id);
            }

            // Update product
            $product->update($data);

            // Handle images if provided
            if (isset($data['images'])) {
                $this->handleProductImages($product, $data['images']);
            }

            // Handle variants if provided
            if (isset($data['variants'])) {
                $this->handleProductVariants($product, $data['variants']);
            }

            // Handle attributes if provided
            if (isset($data['attributes'])) {
                $this->handleProductAttributes($product, $data['attributes']);
            }

            return $product;
        });
    }

    /**
     * Delete product
     */
    public function delete(Product $product)
    {
        return DB::transaction(function () use ($product) {
            // Delete images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_url);
                $image->delete();
            }

            // Delete variants
            $product->variants()->delete();

            // Delete attribute values
            $product->attributeValues()->delete();

            // Delete reviews
            $product->reviews()->delete();

            // Soft delete product
            $product->delete();

            return true;
        });
    }

    /**
     * Bulk update products
     */
    public function bulkUpdate(array $productIds, $action, $value = null)
    {
        $products = Product::whereIn('id', $productIds);

        switch ($action) {
            case 'activate':
                $products->update(['status' => 'active']);
                break;
            case 'deactivate':
                $products->update(['status' => 'inactive']);
                break;
            case 'delete':
                $products->delete();
                break;
            case 'update_stock':
                if ($value !== null) {
                    $products->update(['stock_quantity' => $value]);
                }
                break;
        }

        return $products->count();
    }

    /**
     * Import products from CSV
     */
    public function import($file)
    {
        $csvData = array_map('str_getcsv', file($file->getPathname()));
        $headers = array_shift($csvData);

        $success = 0;
        $failed = 0;

        foreach ($csvData as $row) {
            try {
                $data = array_combine($headers, $row);

                // Validate required fields
                if (empty($data['name']) || empty($data['sku']) || empty($data['price'])) {
                    $failed++;
                    continue;
                }

                // Convert price to cents
                $data['price_cents'] = (int) round($data['price'] * 100);

                $this->create($data);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                continue;
            }
        }

        return ['success' => $success, 'failed' => $failed];
    }

    /**
     * Export products to CSV
     */
    public function export(array $filters = [])
    {
        $query = Product::with(['category', 'brand']);

        // Apply filters
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $products = $query->get();

        $csvData = [];
        $csvData[] = ['ID', 'Name', 'SKU', 'Price', 'Stock', 'Category', 'Brand', 'Status'];

        foreach ($products as $product) {
            $csvData[] = [
                $product->id,
                $product->name,
                $product->sku,
                $product->price_cents / 100,
                $product->stock_quantity,
                $product->category ? $product->category->name : '',
                $product->brand ? $product->brand->name : '',
                $product->status
            ];
        }

        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filePath = storage_path('app/temp/' . $filename);

        $file = fopen($filePath, 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    /**
     * Duplicate product
     */
    public function duplicate(Product $product)
    {
        return DB::transaction(function () use ($product) {
            $newProduct = $product->replicate();
            $newProduct->name = $product->name . ' (Copy)';
            $newProduct->slug = $this->ensureUniqueSlug($product->slug . '-copy');
            $newProduct->sku = $this->ensureUniqueSku($product->sku . '-copy');
            $newProduct->save();

            // Duplicate images
            foreach ($product->images as $image) {
                ProductImage::create([
                    'product_id' => $newProduct->id,
                    'image_url' => $image->image_url,
                    'alt_text' => $image->alt_text,
                    'sort_order' => $image->sort_order,
                    'is_primary' => $image->is_primary
                ]);
            }

            // Duplicate variants
            foreach ($product->variants as $variant) {
                ProductVariant::create([
                    'product_id' => $newProduct->id,
                    'variant_name' => $variant->variant_name,
                    'variant_value' => $variant->variant_value,
                    'price_adjustment_cents' => $variant->price_adjustment_cents,
                    'stock_quantity' => 0, // Reset stock for new product
                    'sku' => $this->ensureUniqueSku($variant->sku . '-copy')
                ]);
            }

            return $newProduct;
        });
    }

    /**
     * Update product stock
     */
    public function updateStock(Product $product, $newStock, $reason = null)
    {
        $oldStock = $product->stock_quantity;
        $product->update(['stock_quantity' => $newStock]);

        // Log stock movement (if you have stock movement tracking)
        // StockMovement::create([
        //     'product_id' => $product->id,
        //     'type' => $newStock > $oldStock ? 'increase' : 'decrease',
        //     'quantity' => abs($newStock - $oldStock),
        //     'reason' => $reason,
        //     'new_stock' => $newStock
        // ]);

        return $product;
    }

    /**
     * Handle product images upload
     */
    protected function handleProductImages(Product $product, array $images)
    {
        foreach ($images as $index => $image) {
            if ($image && $image->isValid()) {
                $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $filename, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path,
                    'alt_text' => $product->name,
                    'sort_order' => $index,
                    'is_primary' => $index === 0
                ]);
            }
        }
    }

    /**
     * Handle product variants
     */
    protected function handleProductVariants(Product $product, array $variants)
    {
        foreach ($variants as $variantData) {
            ProductVariant::create([
                'product_id' => $product->id,
                'variant_name' => $variantData['name'],
                'variant_value' => $variantData['value'],
                'price_adjustment_cents' => isset($variantData['price_adjustment'])
                    ? (int) round($variantData['price_adjustment'] * 100)
                    : 0,
                'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                'sku' => $variantData['sku'] ?? $this->generateVariantSku($product, $variantData)
            ]);
        }
    }

    /**
     * Handle product attributes
     */
    protected function handleProductAttributes(Product $product, array $attributes)
    {
        // Delete existing attribute values
        $product->attributeValues()->delete();

        foreach ($attributes as $attributeId => $value) {
            if ($value !== null && $value !== '') {
                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attributeId,
                    'value_text' => is_string($value) ? $value : null,
                    'value_number' => is_numeric($value) ? $value : null,
                    'value_boolean' => is_bool($value) ? $value : null
                ]);
            }
        }
    }

    /**
     * Ensure unique slug
     */
    protected function ensureUniqueSlug($slug, $excludeId = null)
    {
        $baseSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Product::where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Ensure unique SKU
     */
    protected function ensureUniqueSku($sku, $excludeId = null)
    {
        $baseSku = $sku;
        $counter = 1;

        while (true) {
            $query = Product::where('sku', $sku);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $sku = $baseSku . '-' . $counter;
            $counter++;
        }

        return $sku;
    }

    /**
     * Generate variant SKU
     */
    protected function generateVariantSku(Product $product, array $variantData)
    {
        $sku = $product->sku . '-' . strtoupper(substr($variantData['name'], 0, 2)) . substr($variantData['value'], 0, 2);
        return $this->ensureUniqueSku($sku);
    }
}

// File: app/Services/ShippingService.php

namespace App\Services;

use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\ShippingRate;

class ShippingService
{
    /**
     * Calculate shipping rates for given destination
     */
    public function calculateRates(array $destination, $weight = 0, $amount = 0)
    {
        // Find matching shipping zone
        $zone = $this->findShippingZone($destination);

        if (!$zone) {
            return [];
        }

        // Get available shipping methods
        $methods = ShippingMethod::active()->orderBy('sort_order')->get();
        $rates = [];

        foreach ($methods as $method) {
            $rate = $this->calculateMethodRate($method, $zone, $weight, $amount);

            if ($rate !== null) {
                $rates[] = [
                    'method_id' => $method->id,
                    'method_name' => $method->name,
                    'method_code' => $method->code,
                    'rate_cents' => $rate['rate_cents'],
                    'is_free' => $rate['is_free'],
                    'estimated_days' => [
                        'min' => $method->estimated_min_days,
                        'max' => $method->estimated_max_days
                    ],
                    'description' => $method->description
                ];
            }
        }

        return $rates;
    }

    /**
     * Get shipping cost for specific method
     */
    public function getShippingCost($methodId, array $destination, $weight = 0, $amount = 0)
    {
        $zone = $this->findShippingZone($destination);
        $method = ShippingMethod::find($methodId);

        if (!$zone || !$method) {
            return 0;
        }

        $rate = $this->calculateMethodRate($method, $zone, $weight, $amount);

        return $rate ? $rate['rate_cents'] : 0;
    }

    /**
     * Find shipping zone for destination
     */
    protected function findShippingZone(array $destination)
    {
        $country = $destination['shipping_country'] ?? $destination['country'] ?? 'ID';
        $state = $destination['shipping_state'] ?? $destination['state'] ?? '';
        $city = $destination['shipping_city'] ?? $destination['city'] ?? '';

        return ShippingZone::active()
            ->where(function($query) use ($country, $state, $city) {
                // Check country
                $query->where(function($q) use ($country) {
                    $q->whereJsonContains('countries', $country)
                      ->orWhereJsonContains('countries', '*');
                });

                // Check state if specified
                if (!empty($state)) {
                    $query->where(function($q) use ($state) {
                        $q->whereNull('states')
                          ->orWhereJsonContains('states', $state)
                          ->orWhereJsonContains('states', '*');
                    });
                }

                // Check city if specified
                if (!empty($city)) {
                    $query->where(function($q) use ($city) {
                        $q->whereNull('cities')
                          ->orWhereJsonContains('cities', $city)
                          ->orWhereJsonContains('cities', '*');
                    });
                }
            })
            ->first();
    }

    /**
     * Calculate rate for specific method and zone
     */
    protected function calculateMethodRate(ShippingMethod $method, ShippingZone $zone, $weight, $amount)
    {
        $rate = ShippingRate::where('shipping_method_id', $method->id)
            ->where('zone_id', $zone->id)
            ->where('is_active', true)
            ->where('min_weight_grams', '<=', $weight)
            ->where('max_weight_grams', '>=', $weight)
            ->where(function($query) use ($amount) {
                $query->where('min_amount_cents', '<=', $amount)
                      ->orWhere('min_amount_cents', 0);
            })
            ->where(function($query) use ($amount) {
                $query->where('max_amount_cents', '>=', $amount)
                      ->orWhere('max_amount_cents', 0);
            })
            ->first();

        if (!$rate) {
            return null;
        }

        // Check for free shipping
        $isFree = $rate->free_shipping_threshold_cents > 0 &&
                  $amount >= $rate->free_shipping_threshold_cents;

        return [
            'rate_cents' => $isFree ? 0 : $rate->rate_cents,
            'is_free' => $isFree
        ];
    }
}
