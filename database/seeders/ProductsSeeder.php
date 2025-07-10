<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Get valid users (admin/super_admin)
        $admin = User::whereHas('role', function($query) {
            $query->whereIn('name', ['admin', 'super_admin']);
        })->first();

        if (!$admin) {
            $this->command->error('No admin user found! Please run UsersSeeder first.');
            return;
        }

        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command->error('No categories or brands found! Please run CategorySeeder and BrandSeeder first.');
            return;
        }

        // Define 30 products (6 per main category)
        $productsData = [
            'Elektronik' => [
                [
                    'name' => 'Smartphone Galaxy Pro 5G',
                    'description' => 'Smartphone terbaru dengan layar AMOLED 6.7 inci, kamera 108MP, dan baterai 5000mAh untuk performa maksimal.',
                    'short_description' => 'Smartphone 5G dengan kamera canggih dan baterai tahan lama.',
                ],
                [
                    'name' => 'Laptop Ultrabook 14 Inci',
                    'description' => 'Laptop ringan dengan prosesor Intel Core i7, RAM 16GB, dan SSD 512GB untuk produktivitas tinggi.',
                    'short_description' => 'Laptop ringkas dengan performa tinggi.',
                ],
                [
                    'name' => 'TWS Earbuds Noise Cancelling',
                    'description' => 'Earbuds nirkabel dengan teknologi noise cancelling, kualitas suara jernih, dan daya tahan baterai hingga 24 jam.',
                    'short_description' => 'Earbuds nirkabel dengan suara jernih.',
                ],
                [
                    'name' => 'Smart TV OLED 55 Inci',
                    'description' => 'Televisi OLED 55 inci dengan resolusi 4K, HDR, dan sistem operasi pintar untuk hiburan rumah.',
                    'short_description' => 'Smart TV 4K untuk pengalaman menonton terbaik.',
                ],
                [
                    'name' => 'Kamera Mirrorless 24MP',
                    'description' => 'Kamera mirrorless dengan sensor 24MP, lensa interchangeable, dan fitur perekaman video 4K.',
                    'short_description' => 'Kamera mirrorless untuk fotografi profesional.',
                ],
                [
                    'name' => 'Smartwatch Fitness Tracker',
                    'description' => 'Jam pintar dengan fitur pelacakan kebugaran, detak jantung, dan notifikasi smartphone.',
                    'short_description' => 'Smartwatch untuk gaya hidup aktif.',
                ],
            ],
            'Fashion Pria' => [
                [
                    'name' => 'Kemeja Slim Fit Katun',
                    'description' => 'Kemeja pria slim fit berbahan katun premium, cocok untuk acara formal maupun kasual.',
                    'short_description' => 'Kemeja pria elegan untuk berbagai kesempatan.',
                ],
                [
                    'name' => 'Sepatu Sneaker Kulit',
                    'description' => 'Sneaker pria berbahan kulit asli dengan desain modern, nyaman untuk penggunaan sehari-hari.',
                    'short_description' => 'Sneaker kulit pria yang stylish dan nyaman.',
                ],
                [
                    'name' => 'Jaket Denim Klasik',
                    'description' => 'Jaket denim pria dengan potongan klasik, cocok untuk gaya kasual sehari-hari.',
                    'short_description' => 'Jaket denim pria untuk tampilan kasual.',
                ],
                [
                    'name' => 'Celana Chino Slim Fit',
                    'description' => 'Celana chino pria dengan potongan slim fit, terbuat dari bahan katun berkualitas tinggi.',
                    'short_description' => 'Celana chino pria untuk gaya santai.',
                ],
                [
                    'name' => 'Jam Tangan Analog Klasik',
                    'description' => 'Jam tangan pria dengan desain analog klasik, tahan air, dan tali kulit asli.',
                    'short_description' => 'Jam tangan pria elegan dan tahan lama.',
                ],
                [
                    'name' => 'Ikat Pinggang Kulit',
                    'description' => 'Ikat pinggang pria berbahan kulit asli dengan gesper logam, cocok untuk pakaian formal.',
                    'short_description' => 'Ikat pinggang kulit untuk tampilan formal.',
                ],
            ],
            'Fashion Wanita' => [
                [
                    'name' => 'Dress Midi Floral',
                    'description' => 'Dress midi wanita dengan motif bunga, terbuat dari bahan katun ringan, ideal untuk musim panas.',
                    'short_description' => 'Dress floral wanita yang elegan dan nyaman.',
                ],
                [
                    'name' => 'Tas Tote Kulit Sintetis',
                    'description' => 'Tas tote wanita berbahan kulit sintetis berkualitas tinggi, cocok untuk kerja atau acara santai.',
                    'short_description' => 'Tas tote wanita yang stylish dan fungsional.',
                ],
                [
                    'name' => 'Sepatu Hak Tinggi Elegan',
                    'description' => 'Sepatu hak tinggi wanita dengan desain elegan, nyaman untuk acara formal.',
                    'short_description' => 'Sepatu hak tinggi untuk tampilan elegan.',
                ],
                [
                    'name' => 'Blouse Katun Lengan Panjang',
                    'description' => 'Blouse wanita berbahan katun dengan lengan panjang, cocok untuk kerja atau acara kasual.',
                    'short_description' => 'Blouse wanita untuk gaya profesional.',
                ],
                [
                    'name' => 'Rok Midi A-Line',
                    'description' => 'Rok midi wanita dengan potongan A-line, terbuat dari bahan berkualitas untuk tampilan elegan.',
                    'short_description' => 'Rok midi untuk gaya feminin.',
                ],
                [
                    'name' => 'Syal Sutera Motif',
                    'description' => 'Syal wanita berbahan sutra dengan motif elegan, cocok untuk melengkapi pakaian formal.',
                    'short_description' => 'Syal sutra untuk tampilan mewah.',
                ],
            ],
            'Kesehatan & Kecantikan' => [
                [
                    'name' => 'Serum Wajah Vitamin C',
                    'description' => 'Serum wajah dengan kandungan vitamin C untuk mencerahkan kulit dan mengurangi tanda penuaan.',
                    'short_description' => 'Serum wajah untuk kulit cerah dan sehat.',
                ],
                [
                    'name' => 'Sikat Gigi Elektrik',
                    'description' => 'Sikat gigi elektrik dengan teknologi getar sonik untuk pembersihan menyeluruh.',
                    'short_description' => 'Sikat gigi elektrik untuk kebersihan maksimal.',
                ],
                [
                    'name' => 'Masker Wajah Organik',
                    'description' => 'Masker wajah berbahan organik untuk hidrasi dan peremajaan kulit.',
                    'short_description' => 'Masker wajah organik untuk kulit sehat.',
                ],
                [
                    'name' => 'Krim Pelembap Anti-Aging',
                    'description' => 'Krim pelembap dengan formula anti-aging untuk menjaga kulit tetap lembap dan elastis.',
                    'short_description' => 'Krim anti-aging untuk kulit lembap.',
                ],
                [
                    'name' => 'Sunscreen SPF 50',
                    'description' => 'Tabir surya dengan SPF 50 untuk perlindungan maksimal dari sinar UV.',
                    'short_description' => 'Sunscreen untuk perlindungan kulit.',
                ],
                [
                    'name' => 'Alat Pijat Wajah',
                    'description' => 'Alat pijat wajah elektrik untuk relaksasi dan meningkatkan sirkulasi darah.',
                    'short_description' => 'Alat pijat wajah untuk relaksasi kulit.',
                ],
            ],
            'Rumah Tangga' => [
                [
                    'name' => 'Panci Anti Lengket 24cm',
                    'description' => 'Panci anti lengket dengan diameter 24cm, ideal untuk memasak sehari-hari.',
                    'short_description' => 'Panci anti lengket untuk masak mudah.',
                ],
                [
                    'name' => 'Set Pisau Dapur 5 Buah',
                    'description' => 'Set pisau dapur berbahan stainless steel, tajam dan tahan lama untuk berbagai kebutuhan memasak.',
                    'short_description' => 'Set pisau dapur berkualitas tinggi.',
                ],
                [
                    'name' => 'Vacuum Cleaner Robot',
                    'description' => 'Robot penyedot debu dengan teknologi pintar, cocok untuk menjaga kebersihan rumah.',
                    'short_description' => 'Robot vacuum untuk rumah bersih.',
                ],
                [
                    'name' => 'Set Peralatan Makan Keramik',
                    'description' => 'Set peralatan makan keramik untuk 4 orang, elegan dan tahan lama.',
                    'short_description' => 'Set makan keramik untuk keluarga.',
                ],
                [
                    'name' => 'Lampu Meja LED Dimmable',
                    'description' => 'Lampu meja LED dengan fitur dimmable, hemat energi, dan desain modern.',
                    'short_description' => 'Lampu meja LED untuk penerangan fleksibel.',
                ],
                [
                    'name' => 'Rak Penyimpanan Dapur',
                    'description' => 'Rak penyimpanan dapur berbahan stainless steel, kokoh dan mudah dipasang.',
                    'short_description' => 'Rak dapur untuk organisasi maksimal.',
                ],
            ],
        ];

        // Create products
        foreach ($productsData as $categoryName => $products) {
            $category = $categories->where('name', $categoryName)->first();
            if (!$category) {
                $this->command->warn("Category '$categoryName' not found. Skipping products.");
                continue;
            }

            // Get subcategories for this main category
            $subcategories = $categories->where('parent_id', $category->id);

            foreach ($products as $productData) {
                $brand = $brands->random();
                $subcategory = $subcategories->isNotEmpty() ? $subcategories->random() : $category;

                $product = Product::create([
                    'category_id' => $subcategory->id,
                    'brand_id' => $brand->id,
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']) . '-' . Str::random(6),
                    'description' => $productData['description'],
                    'short_description' => $productData['short_description'],
                    'sku' => 'SKU-' . Str::upper(Str::random(8)),
                    'barcode' => 'BC-' . rand(1000000000, 9999999999),
                    'price_cents' => rand(50000, 5000000),
                    'compare_price_cents' => rand(60000, 5500000),
                    'cost_price_cents' => rand(40000, 4000000),
                    'stock_quantity' => rand(0, 500),
                    'min_stock_level' => 5,
                    'max_stock_level' => 1000,
                    'weight_grams' => rand(50, 5000),
                    'length_mm' => rand(100, 500),
                    'width_mm' => rand(100, 500),
                    'height_mm' => rand(100, 500),
                    'status' => fake()->randomElement(['draft', 'active', 'active', 'active', 'inactive']),
                    'featured' => rand(0, 1),
                    'digital' => rand(0, 1),
                    'track_stock' => true,
                    'allow_backorder' => rand(0, 1),
                    'meta_title' => $productData['name'] . ' | TokoSaya',
                    'meta_description' => 'Beli ' . $productData['name'] . ' dengan harga terbaik di TokoSaya',
                    'created_by' => $admin->id,
                ]);

                // Update category product count
                $subcategory->increment('product_count');

                // Create product images
                $this->createProductImages($product, $productData['name']);

                // Create product variants for 30% of products
                if (rand(1, 100) <= 30) {
                    $this->createVariants($product);
                }
            }
        }
    }

    protected function createProductImages($product, $productName)
    {
        $imageCount = rand(1, 3);
        for ($i = 0; $i < $imageCount; $i++) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => fake()->imageUrl(640, 480, 'products', true, $productName),
                'alt_text' => $productName . ' Image ' . ($i + 1),
                'sort_order' => $i,
                'is_primary' => $i === 0,
                'width' => 640,
                'height' => 480,
                'file_size' => rand(100000, 500000),
                'created_at' => now(),
            ]);
        }
    }

    protected function createVariants($product)
    {
        $variantTypes = ['Warna', 'Ukuran', 'Kapasitas'];
        $variantType = $variantTypes[array_rand($variantTypes)];

        $variants = [
            [
                'variant_name' => $variantType,
                'variant_value' => 'Option 1',
                'price_adjustment_cents' => rand(-50000, 50000),
                'stock_quantity' => rand(0, 100),
                'sku' => $product->sku . '-V1',
                'barcode' => $product->barcode . '-V1',
                'is_active' => true,
            ],
            [
                'variant_name' => $variantType,
                'variant_value' => 'Option 2',
                'price_adjustment_cents' => rand(-50000, 50000),
                'stock_quantity' => rand(0, 100),
                'sku' => $product->sku . '-V2',
                'barcode' => $product->barcode . '-V2',
                'is_active' => true,
            ],
            [
                'variant_name' => $variantType,
                'variant_value' => 'Option 3',
                'price_adjustment_cents' => rand(-50000, 50000),
                'stock_quantity' => rand(0, 100),
                'sku' => $product->sku . '-V3',
                'barcode' => $product->barcode . '-V3',
                'is_active' => true,
            ],
        ];

        foreach ($variants as $variant) {
            $product->variants()->create($variant);
        }
    }
}
