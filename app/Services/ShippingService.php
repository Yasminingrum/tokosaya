<?php

namespace App\Services;

use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\ShippingRate;
use App\Models\Order;
use App\Models\CustomerAddress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * ShippingService - Advanced shipping calculation and management
 *
 * Features:
 * - Zone-based shipping rate calculation
 * - Multiple carrier integration (JNE, J&T, SiCepat, POS)
 * - Real-time shipping cost calculation
 * - Delivery time estimation
 * - Free shipping threshold management
 * - Bulk shipping calculations
 * - Address validation
 * - Shipping tracking integration
 */
class ShippingService
{
    /**
     * Cache TTL for shipping calculations (in seconds)
     */
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Default shipping method if none specified
     */
    const DEFAULT_SHIPPING_METHOD = 'regular';

    /**
     * Maximum package weight in grams
     */
    const MAX_WEIGHT_GRAMS = 30000; // 30kg

    /**
     * Supported shipping carriers with their API endpoints
     */
    const CARRIERS = [
        'jne' => [
            'name' => 'JNE',
            'api_url' => 'https://api.jne.co.id',
            'logo' => '/images/carriers/jne.png'
        ],
        'jnt' => [
            'name' => 'J&T Express',
            'api_url' => 'https://api.jet.co.id',
            'logo' => '/images/carriers/jnt.png'
        ],
        'sicepat' => [
            'name' => 'SiCepat',
            'api_url' => 'https://api.sicepat.com',
            'logo' => '/images/carriers/sicepat.png'
        ],
        'pos' => [
            'name' => 'Pos Indonesia',
            'api_url' => 'https://api.posindonesia.co.id',
            'logo' => '/images/carriers/pos.png'
        ]
    ];

    /**
     * Calculate shipping costs for given parameters
     *
     * @param  CustomerAddress  $destination
     * @param  array  $items  Array of cart items or order items
     * @param  int|null  $methodId  Specific shipping method ID
     * @return Collection
     */
    public function calculateShippingCosts(CustomerAddress $destination, array $items, ?int $methodId = null): Collection
    {
        $cacheKey = $this->generateCacheKey($destination, $items, $methodId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($destination, $items, $methodId) {
            return $this->performShippingCalculation($destination, $items, $methodId);
        });
    }

    /**
     * Perform actual shipping cost calculation
     *
     * @param  CustomerAddress  $destination
     * @param  array  $items
     * @param  int|null  $methodId
     * @return Collection
     */
    private function performShippingCalculation(CustomerAddress $destination, array $items, ?int $methodId): Collection
    {
        // Calculate package details
        $packageDetails = $this->calculatePackageDetails($items);

        // Find applicable shipping zone
        $zone = $this->findShippingZone($destination);

        if (!$zone) {
            Log::warning('No shipping zone found for destination', [
                'destination' => $destination->toArray()
            ]);
            return collect();
        }

        // Get shipping methods
        $methods = $this->getShippingMethods($methodId);

        // Calculate rates for each method
        $shippingOptions = collect();

        foreach ($methods as $method) {
            $rate = $this->calculateMethodRate($method, $zone, $packageDetails);

            if ($rate) {
                $shippingOptions->push($rate);
            }
        }

        return $shippingOptions->sortBy('cost_cents');
    }

    /**
     * Calculate package weight and dimensions from items
     *
     * @param  array  $items
     * @return array
     */
    private function calculatePackageDetails(array $items): array
    {
        $totalWeight = 0;
        $totalValue = 0;
        $dimensions = ['length' => 0, 'width' => 0, 'height' => 0];
        $itemCount = 0;
        $hasDigitalOnly = true;

        foreach ($items as $item) {
            $product = $item['product'] ?? $item->product;
            $quantity = $item['quantity'] ?? $item->quantity;

            // Skip digital products for shipping
            if ($product->digital) {
                continue;
            }

            $hasDigitalOnly = false;

            // Calculate weight
            $itemWeight = $product->weight_grams * $quantity;
            $totalWeight += $itemWeight;

            // Calculate value for insurance
            $itemValue = ($item['unit_price_cents'] ?? $item->unit_price_cents) * $quantity;
            $totalValue += $itemValue;

            // Calculate dimensions (simplified - assuming stackable)
            $dimensions['length'] = max($dimensions['length'], $product->length_mm ?? 0);
            $dimensions['width'] = max($dimensions['width'], $product->width_mm ?? 0);
            $dimensions['height'] += ($product->height_mm ?? 0) * $quantity;

            $itemCount += $quantity;
        }

        // Apply minimum weight for very light packages
        if ($totalWeight < 100 && !$hasDigitalOnly) {
            $totalWeight = 100; // Minimum 100g
        }

        return [
            'weight_grams' => $totalWeight,
            'total_value_cents' => $totalValue,
            'dimensions' => $dimensions,
            'item_count' => $itemCount,
            'digital_only' => $hasDigitalOnly
        ];
    }

    /**
     * Find shipping zone for destination address
     *
     * @param  CustomerAddress  $destination
     * @return ShippingZone|null
     */
    private function findShippingZone(CustomerAddress $destination): ?ShippingZone
    {
        return ShippingZone::where('is_active', true)
            ->where(function ($query) use ($destination) {
                // Check country
                $query->whereJsonContains('countries', $destination->country)
                      ->orWhereJsonContains('countries', '*');
            })
            ->where(function ($query) use ($destination) {
                // Check state/province
                $query->whereNull('states')
                      ->orWhereJsonContains('states', $destination->state)
                      ->orWhereJsonContains('states', '*');
            })
            ->where(function ($query) use ($destination) {
                // Check city
                $query->whereNull('cities')
                      ->orWhereJsonContains('cities', $destination->city)
                      ->orWhereJsonContains('cities', '*');
            })
            ->where(function ($query) use ($destination) {
                // Check postal codes
                $query->whereNull('postal_codes')
                      ->orWhereJsonContains('postal_codes', $destination->postal_code)
                      ->orWhereJsonContains('postal_codes', substr($destination->postal_code, 0, 3));
            })
            ->orderBy('id')
            ->first();
    }

    /**
     * Get available shipping methods
     *
     * @param  int|null  $methodId
     * @return Collection
     */
    private function getShippingMethods(?int $methodId): Collection
    {
        $query = ShippingMethod::where('is_active', true);

        if ($methodId) {
            $query->where('id', $methodId);
        }

        return $query->orderBy('sort_order')->get();
    }

    /**
     * Calculate shipping rate for specific method and zone
     *
     * @param  ShippingMethod  $method
     * @param  ShippingZone  $zone
     * @param  array  $packageDetails
     * @return array|null
     */
    private function calculateMethodRate(ShippingMethod $method, ShippingZone $zone, array $packageDetails): ?array
    {
        // Skip if package is digital only and method doesn't support digital
        if ($packageDetails['digital_only'] && !$method->supports_digital) {
            return null;
        }

        // Find applicable rate
        $rate = ShippingRate::where('shipping_method_id', $method->id)
            ->where('zone_id', $zone->id)
            ->where('is_active', true)
            ->where('min_weight_grams', '<=', $packageDetails['weight_grams'])
            ->where('max_weight_grams', '>=', $packageDetails['weight_grams'])
            ->where(function ($query) use ($packageDetails) {
                $query->where('min_amount_cents', 0)
                      ->orWhere('min_amount_cents', '<=', $packageDetails['total_value_cents']);
            })
            ->where(function ($query) use ($packageDetails) {
                $query->where('max_amount_cents', 0)
                      ->orWhere('max_amount_cents', '>=', $packageDetails['total_value_cents']);
            })
            ->first();

        if (!$rate) {
            return null;
        }

        // Calculate final cost
        $baseCost = $rate->rate_cents;
        $finalCost = $baseCost;

        // Apply weight-based adjustments if needed
        if ($packageDetails['weight_grams'] > 1000) {
            $extraWeight = ceil(($packageDetails['weight_grams'] - 1000) / 1000);
            $finalCost += $extraWeight * ($baseCost * 0.1); // 10% per additional kg
        }

        // Check for free shipping threshold
        $isFreeShipping = $rate->free_shipping_threshold_cents > 0 &&
                         $packageDetails['total_value_cents'] >= $rate->free_shipping_threshold_cents;

        if ($isFreeShipping) {
            $finalCost = 0;
        }

        // Calculate estimated delivery time
        $estimatedDelivery = $this->calculateDeliveryEstimate($method, $zone, $packageDetails);

        return [
            'method_id' => $method->id,
            'method_name' => $method->name,
            'method_code' => $method->code,
            'method_logo' => $method->logo,
            'cost_cents' => (int) $finalCost,
            'original_cost_cents' => $baseCost,
            'is_free_shipping' => $isFreeShipping,
            'free_shipping_threshold_cents' => $rate->free_shipping_threshold_cents,
            'estimated_min_days' => $estimatedDelivery['min_days'],
            'estimated_max_days' => $estimatedDelivery['max_days'],
            'estimated_delivery_date' => $estimatedDelivery['delivery_date'],
            'description' => $this->buildShippingDescription($method, $estimatedDelivery, $isFreeShipping),
            'rate_id' => $rate->id,
            'zone_id' => $zone->id,
            'carrier_info' => $this->getCarrierInfo($method->code),
            'supports_tracking' => $method->supports_tracking ?? true,
            'supports_insurance' => $method->supports_insurance ?? false,
            'max_insurance_value' => $method->max_insurance_value ?? 0
        ];
    }

    /**
     * Calculate delivery time estimate
     *
     * @param  ShippingMethod  $method
     * @param  ShippingZone  $zone
     * @param  array  $packageDetails
     * @return array
     */
    private function calculateDeliveryEstimate(ShippingMethod $method, ShippingZone $zone, array $packageDetails): array
    {
        $minDays = $method->estimated_min_days ?? 1;
        $maxDays = $method->estimated_max_days ?? 3;

        // Adjust for package weight (heavier packages may take longer)
        if ($packageDetails['weight_grams'] > 10000) { // > 10kg
            $minDays++;
            $maxDays++;
        }

        // Adjust for remote zones (simplified logic)
        if (str_contains(strtolower($zone->name), 'remote') ||
            str_contains(strtolower($zone->name), 'outer')) {
            $minDays += 2;
            $maxDays += 3;
        }

        // Calculate delivery date (excluding weekends for regular methods)
        $deliveryDate = now();
        $daysToAdd = $maxDays;

        while ($daysToAdd > 0) {
            $deliveryDate->addDay();
            // Skip Sundays for regular delivery
            if ($deliveryDate->dayOfWeek !== 0 || $method->delivers_sunday ?? false) {
                $daysToAdd--;
            }
        }

        return [
            'min_days' => $minDays,
            'max_days' => $maxDays,
            'delivery_date' => $deliveryDate->format('Y-m-d')
        ];
    }

    /**
     * Build shipping description text
     *
     * @param  ShippingMethod  $method
     * @param  array  $estimatedDelivery
     * @param  bool  $isFreeShipping
     * @return string
     */
    private function buildShippingDescription(ShippingMethod $method, array $estimatedDelivery, bool $isFreeShipping): string
    {
        $description = $method->description ?? '';

        // Add delivery estimate
        if ($estimatedDelivery['min_days'] === $estimatedDelivery['max_days']) {
            $description .= " Estimasi {$estimatedDelivery['min_days']} hari kerja.";
        } else {
            $description .= " Estimasi {$estimatedDelivery['min_days']}-{$estimatedDelivery['max_days']} hari kerja.";
        }

        // Add free shipping note
        if ($isFreeShipping) {
            $description .= " GRATIS ONGKIR!";
        }

        return trim($description);
    }

    /**
     * Get carrier information
     *
     * @param  string  $methodCode
     * @return array
     */
    private function getCarrierInfo(string $methodCode): array
    {
        $carrierCode = explode('_', $methodCode)[0] ?? $methodCode;

        return self::CARRIERS[$carrierCode] ?? [
            'name' => ucfirst($carrierCode),
            'api_url' => null,
            'logo' => '/images/carriers/default.png'
        ];
    }

    /**
     * Generate cache key for shipping calculation
     *
     * @param  CustomerAddress  $destination
     * @param  array  $items
     * @param  int|null  $methodId
     * @return string
     */
    private function generateCacheKey(CustomerAddress $destination, array $items, ?int $methodId): string
    {
        $itemsHash = md5(serialize(collect($items)->map(function ($item) {
            return [
                'product_id' => $item['product']->id ?? $item->product->id,
                'quantity' => $item['quantity'] ?? $item->quantity,
                'weight' => $item['product']->weight_grams ?? $item->product->weight_grams
            ];
        })->toArray()));

        return "shipping_costs:{$destination->country}:{$destination->state}:{$destination->city}:{$destination->postal_code}:{$itemsHash}:{$methodId}";
    }

    /**
     * Get real-time shipping rates from carrier APIs
     *
     * @param  string  $carrierCode
     * @param  CustomerAddress  $destination
     * @param  array  $packageDetails
     * @return array
     */
    public function getRealTimeRates(string $carrierCode, CustomerAddress $destination, array $packageDetails): array
    {
        if (!isset(self::CARRIERS[$carrierCode])) {
            return [];
        }

        $carrier = self::CARRIERS[$carrierCode];

        try {
            // This is a simplified example - actual implementation would depend on carrier API
            $response = Http::timeout(10)->post($carrier['api_url'] . '/shipping/calculate', [
                'origin' => config('tokosaya.warehouse.default_address'),
                'destination' => [
                    'city' => $destination->city,
                    'state' => $destination->state,
                    'postal_code' => $destination->postal_code,
                    'country' => $destination->country
                ],
                'package' => [
                    'weight' => $packageDetails['weight_grams'],
                    'value' => $packageDetails['total_value_cents'],
                    'dimensions' => $packageDetails['dimensions']
                ]
            ]);

            if ($response->successful()) {
                return $response->json()['rates'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error("Failed to get real-time rates from {$carrierCode}", [
                'error' => $e->getMessage(),
                'destination' => $destination->toArray(),
                'package' => $packageDetails
            ]);
        }

        return [];
    }

    /**
     * Validate shipping address
     *
     * @param  CustomerAddress  $address
     * @return array
     */
    public function validateShippingAddress(CustomerAddress $address): array
    {
        $errors = [];

        // Check if address is complete
        $requiredFields = ['recipient_name', 'phone', 'address_line1', 'city', 'state', 'postal_code'];

        foreach ($requiredFields as $field) {
            if (empty($address->$field)) {
                $errors[] = "Field {$field} is required";
            }
        }

        // Validate phone number format
        if ($address->phone && !preg_match('/^(\+62|62|0)[\d\-\s]{8,15}$/', $address->phone)) {
            $errors[] = 'Invalid phone number format';
        }

        // Validate postal code format for Indonesia
        if ($address->country === 'ID' && $address->postal_code) {
            if (!preg_match('/^\d{5}$/', $address->postal_code)) {
                $errors[] = 'Postal code must be 5 digits for Indonesia';
            }
        }

        // Check if we can deliver to this area
        $zone = $this->findShippingZone($address);
        if (!$zone) {
            $errors[] = 'Delivery not available to this area';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'zone' => $zone
        ];
    }

    /**
     * Create shipping label for order
     *
     * @param  Order  $order
     * @param  array  $options
     * @return array
     */
    public function createShippingLabel(Order $order, array $options = []): array
    {
        try {
            $carrierCode = $options['carrier'] ?? $this->getCarrierFromMethod($order->shipping_method_id);

            if (!$carrierCode) {
                throw new \Exception('No carrier found for shipping method');
            }

            $labelData = $this->generateLabelData($order, $options);

            // Call carrier API to create label
            $response = $this->callCarrierAPI($carrierCode, 'create_label', $labelData);

            if ($response['success']) {
                // Update order with tracking information
                $order->update([
                    'tracking_number' => $response['tracking_number'],
                    'shipping_label_url' => $response['label_url'],
                    'status' => 'processing'
                ]);

                // Log shipping label creation
                Log::channel('business')->info('Shipping label created', [
                    'order_id' => $order->id,
                    'tracking_number' => $response['tracking_number'],
                    'carrier' => $carrierCode
                ]);

                return [
                    'success' => true,
                    'tracking_number' => $response['tracking_number'],
                    'label_url' => $response['label_url'],
                    'estimated_delivery' => $response['estimated_delivery'] ?? null
                ];
            }

            throw new \Exception($response['error'] ?? 'Failed to create shipping label');

        } catch (\Exception $e) {
            Log::error('Shipping label creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'options' => $options
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Track shipment status
     *
     * @param  string  $trackingNumber
     * @param  string|null  $carrierCode
     * @return array
     */
    public function trackShipment(string $trackingNumber, ?string $carrierCode = null): array
    {
        $cacheKey = "tracking:{$trackingNumber}";

        return Cache::remember($cacheKey, 300, function () use ($trackingNumber, $carrierCode) {
            try {
                if (!$carrierCode) {
                    $carrierCode = $this->detectCarrierFromTracking($trackingNumber);
                }

                if (!$carrierCode) {
                    throw new \Exception('Cannot detect carrier from tracking number');
                }

                $response = $this->callCarrierAPI($carrierCode, 'track', [
                    'tracking_number' => $trackingNumber
                ]);

                if ($response['success']) {
                    return [
                        'success' => true,
                        'status' => $response['status'],
                        'status_description' => $response['status_description'],
                        'location' => $response['current_location'] ?? null,
                        'estimated_delivery' => $response['estimated_delivery'] ?? null,
                        'history' => $response['tracking_history'] ?? [],
                        'last_updated' => $response['last_updated'] ?? now()->toISOString()
                    ];
                }

                throw new \Exception($response['error'] ?? 'Tracking failed');

            } catch (\Exception $e) {
                Log::error('Shipment tracking failed', [
                    'tracking_number' => $trackingNumber,
                    'carrier' => $carrierCode,
                    'error' => $e->getMessage()
                ]);

                return [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Calculate bulk shipping for multiple orders
     *
     * @param  Collection  $orders
     * @return array
     */
    public function calculateBulkShipping(Collection $orders): array
    {
        $results = [];
        $totalCost = 0;
        $errors = [];

        foreach ($orders as $order) {
            try {
                $destination = $order->shippingAddress ?? $order->user->defaultAddress;

                if (!$destination) {
                    $errors[] = "No shipping address for order {$order->id}";
                    continue;
                }

                $items = $order->items->map(function ($item) {
                    return [
                        'product' => $item->product,
                        'quantity' => $item->quantity,
                        'unit_price_cents' => $item->unit_price_cents
                    ];
                })->toArray();

                $shippingOptions = $this->calculateShippingCosts($destination, $items);

                if ($shippingOptions->isNotEmpty()) {
                    $cheapestOption = $shippingOptions->first();
                    $results[$order->id] = $cheapestOption;
                    $totalCost += $cheapestOption['cost_cents'];
                } else {
                    $errors[] = "No shipping options available for order {$order->id}";
                }

            } catch (\Exception $e) {
                $errors[] = "Error calculating shipping for order {$order->id}: {$e->getMessage()}";
            }
        }

        return [
            'results' => $results,
            'total_cost_cents' => $totalCost,
            'total_orders' => $orders->count(),
            'successful_calculations' => count($results),
            'errors' => $errors
        ];
    }

    /**
     * Get available pickup points near address
     *
     * @param  CustomerAddress  $address
     * @param  int  $radius  Radius in kilometers
     * @return Collection
     */
    public function getPickupPoints(CustomerAddress $address, int $radius = 5): Collection
    {
        $cacheKey = "pickup_points:{$address->city}:{$address->postal_code}:{$radius}";

        return Cache::remember($cacheKey, 3600, function () use ($address, $radius) {
            // This would integrate with carrier APIs to get actual pickup points
            // For now, return mock data structure
            return collect([
                [
                    'id' => 'pickup_001',
                    'name' => 'Alfamart ' . $address->city . ' Center',
                    'address' => 'Jl. Utama No. 123, ' . $address->city,
                    'phone' => '021-12345678',
                    'hours' => '08:00-22:00',
                    'carrier' => 'jne',
                    'distance_km' => 1.2,
                    'coordinates' => [
                        'lat' => -6.175392,
                        'lng' => 106.827153
                    ]
                ],
                [
                    'id' => 'pickup_002',
                    'name' => 'Indomaret ' . $address->city . ' Plaza',
                    'address' => 'Jl. Raya No. 456, ' . $address->city,
                    'phone' => '021-87654321',
                    'hours' => '07:00-23:00',
                    'carrier' => 'jnt',
                    'distance_km' => 2.1,
                    'coordinates' => [
                        'lat' => -6.185392,
                        'lng' => 106.837153
                    ]
                ]
            ]);
        });
    }

    /**
     * Estimate delivery carbon footprint
     *
     * @param  string  $shippingMethod
     * @param  float  $distanceKm
     * @param  int  $weightGrams
     * @return array
     */
    public function calculateCarbonFootprint(string $shippingMethod, float $distanceKm, int $weightGrams): array
    {
        // Carbon emission factors (kg CO2 per km per kg of package)
        $emissionFactors = [
            'motorcycle' => 0.12,
            'van' => 0.25,
            'truck' => 0.15,
            'air' => 1.2,
            'ship' => 0.05
        ];

        // Determine transport mode based on shipping method
        $transportMode = $this->getTransportMode($shippingMethod);
        $factor = $emissionFactors[$transportMode] ?? $emissionFactors['van'];

        // Calculate carbon footprint
        $carbonKg = ($distanceKm * ($weightGrams / 1000) * $factor) / 1000;

        return [
            'carbon_footprint_kg' => round($carbonKg, 3),
            'transport_mode' => $transportMode,
            'distance_km' => $distanceKm,
            'weight_kg' => $weightGrams / 1000,
            'equivalent_trees' => round($carbonKg / 22, 2) // 1 tree absorbs ~22kg CO2/year
        ];
    }

    /**
     * Get shipping analytics for dashboard
     *
     * @param  array  $filters
     * @return array
     */
    public function getShippingAnalytics(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->subDays(30);
        $endDate = $filters['end_date'] ?? now();

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('shipping_method_id')
            ->with(['shippingMethod']);

        // Apply additional filters
        if (isset($filters['shipping_method_id'])) {
            $orders->where('shipping_method_id', $filters['shipping_method_id']);
        }

        $orderData = $orders->get();

        // Calculate metrics
        $totalOrders = $orderData->count();
        $totalShippingCost = $orderData->sum('shipping_cents');
        $averageShippingCost = $totalOrders > 0 ? $totalShippingCost / $totalOrders : 0;

        // Group by shipping method
        $methodBreakdown = $orderData->groupBy('shipping_method_id')
            ->map(function ($orders, $methodId) {
                $method = $orders->first()->shippingMethod;
                return [
                    'method_name' => $method->name ?? 'Unknown',
                    'order_count' => $orders->count(),
                    'total_cost_cents' => $orders->sum('shipping_cents'),
                    'average_cost_cents' => $orders->avg('shipping_cents'),
                    'percentage' => 0 // Will be calculated below
                ];
            });

        // Calculate percentages
        $methodBreakdown = $methodBreakdown->map(function ($data) use ($totalOrders) {
            $data['percentage'] = $totalOrders > 0 ?
                round(($data['order_count'] / $totalOrders) * 100, 1) : 0;
            return $data;
        });

        // Free shipping analysis
        $freeShippingOrders = $orderData->where('shipping_cents', 0)->count();
        $freeShippingPercentage = $totalOrders > 0 ?
            round(($freeShippingOrders / $totalOrders) * 100, 1) : 0;

        return [
            'summary' => [
                'total_orders' => $totalOrders,
                'total_shipping_cost_cents' => $totalShippingCost,
                'average_shipping_cost_cents' => round($averageShippingCost),
                'free_shipping_orders' => $freeShippingOrders,
                'free_shipping_percentage' => $freeShippingPercentage
            ],
            'method_breakdown' => $methodBreakdown->values(),
            'daily_trends' => $this->getShippingTrends($orderData),
            'cost_distribution' => $this->getShippingCostDistribution($orderData)
        ];
    }

    /**
     * Private helper methods
     */

    private function getCarrierFromMethod(int $methodId): ?string
    {
        $method = ShippingMethod::find($methodId);
        return $method ? explode('_', $method->code)[0] : null;
    }

    private function generateLabelData(Order $order, array $options): array
    {
        return [
            'order_id' => $order->id,
            'sender' => config('tokosaya.warehouse.default_address'),
            'recipient' => [
                'name' => $order->shipping_name,
                'phone' => $order->shipping_phone,
                'address' => $order->shipping_address,
                'city' => $order->shipping_city,
                'state' => $order->shipping_state,
                'postal_code' => $order->shipping_postal_code,
                'country' => $order->shipping_country
            ],
            'package' => [
                'weight' => $order->items->sum(function ($item) {
                    return $item->product->weight_grams * $item->quantity;
                }),
                'value' => $order->total_cents,
                'description' => 'E-commerce order #' . $order->order_number
            ],
            'service_type' => $options['service_type'] ?? 'regular',
            'insurance' => $options['insurance'] ?? false
        ];
    }

    private function callCarrierAPI(string $carrierCode, string $endpoint, array $data): array
    {
        // Mock implementation - replace with actual carrier API calls
        switch ($endpoint) {
            case 'create_label':
                return [
                    'success' => true,
                    'tracking_number' => strtoupper($carrierCode) . date('YmdHis') . rand(1000, 9999),
                    'label_url' => "https://api.{$carrierCode}.com/labels/" . uniqid(),
                    'estimated_delivery' => now()->addDays(3)->format('Y-m-d')
                ];

            case 'track':
                return [
                    'success' => true,
                    'status' => 'in_transit',
                    'status_description' => 'Package is on the way',
                    'current_location' => 'Jakarta Distribution Center',
                    'tracking_history' => [
                        [
                            'timestamp' => now()->subHours(2)->toISOString(),
                            'location' => 'Jakarta Distribution Center',
                            'description' => 'Package departed facility'
                        ]
                    ]
                ];

            default:
                return ['success' => false, 'error' => 'Unknown endpoint'];
        }
    }

    private function detectCarrierFromTracking(string $trackingNumber): ?string
    {
        // Simple pattern matching - enhance based on actual carrier formats
        if (preg_match('/^JNE/', $trackingNumber)) return 'jne';
        if (preg_match('/^JP/', $trackingNumber)) return 'jnt';
        if (preg_match('/^000/', $trackingNumber)) return 'sicepat';
        if (preg_match('/^[A-Z]{2}\d{9}ID$/', $trackingNumber)) return 'pos';

        return null;
    }

    private function getTransportMode(string $shippingMethod): string
    {
        $method = strtolower($shippingMethod);

        if (str_contains($method, 'express') || str_contains($method, 'same_day')) {
            return 'motorcycle';
        }
        if (str_contains($method, 'air') || str_contains($method, 'overnight')) {
            return 'air';
        }
        if (str_contains($method, 'sea') || str_contains($method, 'economy')) {
            return 'ship';
        }

        return 'van'; // Default
    }

    private function getShippingTrends(Collection $orders): array
    {
        return $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($dayOrders) {
            return [
                'order_count' => $dayOrders->count(),
                'total_shipping_cost' => $dayOrders->sum('shipping_cents'),
                'average_shipping_cost' => $dayOrders->avg('shipping_cents')
            ];
        })->toArray();
    }

    private function getShippingCostDistribution(Collection $orders): array
    {
        $ranges = [
            '0' => 0,
            '1-10000' => 0,
            '10001-25000' => 0,
            '25001-50000' => 0,
            '50000+' => 0
        ];

        foreach ($orders as $order) {
            $cost = $order->shipping_cents;

            if ($cost == 0) {
                $ranges['0']++;
            } elseif ($cost <= 10000) {
                $ranges['1-10000']++;
            } elseif ($cost <= 25000) {
                $ranges['10001-25000']++;
            } elseif ($cost <= 50000) {
                $ranges['25001-50000']++;
            } else {
                $ranges['50000+']++;
            }
        }

        return $ranges;
    }
}
