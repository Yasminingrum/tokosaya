<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class ProductReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Simple ProductReviewsSeeder...');

        // Check data yang ada
        $products = Product::where('status', 'active')->get();
        $users = User::where('is_active', true)->get();
        $adminUser = User::first(); // Ambil user pertama sebagai approver

        if ($products->count() === 0 || $users->count() === 0) {
            $this->command->error('❌ Not enough data to create reviews');
            return;
        }

        $this->command->info("Found {$products->count()} products and {$users->count()} users");

        // Data review Indonesia
        $reviewData = [
            ['title' => 'Produk bagus!', 'review' => 'Sangat puas dengan pembelian ini. Kualitas produk sangat baik.', 'rating' => 5],
            ['title' => 'Recommended!', 'review' => 'Produk sesuai dengan deskripsi, kualitas bagus dan pengiriman cepat.', 'rating' => 4],
            ['title' => 'Worth it', 'review' => 'Packaging rapi, produk original, dan pelayanan memuaskan.', 'rating' => 5],
            ['title' => 'Kualitas oke', 'review' => 'Harga sebanding dengan kualitas. Akan beli lagi di masa depan.', 'rating' => 3],
            ['title' => 'Mantap jiwa', 'review' => 'Produk berkualitas tinggi, sesuai ekspektasi. Recommended seller!', 'rating' => 5],
            ['title' => 'Pelayanan cepat', 'review' => 'Pengiriman cepat, packaging aman, produk sesuai gambar.', 'rating' => 4],
            ['title' => 'Bagus banget', 'review' => 'Kualitas oke, harga terjangkau. Terima kasih seller!', 'rating' => 5],
            ['title' => 'Sesuai deskripsi', 'review' => 'Produk original, kondisi baik, tidak ada yang rusak.', 'rating' => 4],
            ['title' => 'Packaging rapi', 'review' => 'Fast response dari seller, barang sampai dengan selamat.', 'rating' => 3],
            ['title' => 'Top markotop', 'review' => 'Produk sesuai foto, kualitas bagus, recommended deh!', 'rating' => 5],
            ['title' => 'Gak nyesel beli', 'review' => 'Pelayanan ramah, pengiriman cepat, produk sesuai ekspektasi.', 'rating' => 4],
            ['title' => 'Barang original', 'review' => 'Barang original, packaging rapi, terima kasih seller.', 'rating' => 5],
        ];

        // Insert reviews satu per satu untuk menghindari masalah batch
        $insertedCount = 0;

        for ($i = 0; $i < 100; $i++) {
            try {
                $product = $products->random();
                $user = $users->random();
                $reviewTemplate = $reviewData[array_rand($reviewData)];

                $isApproved = fake()->boolean(85); // 85% approved
                $createdAt = fake()->dateTimeBetween('-6 months', 'now');

                // Insert single review
                $reviewId = DB::table('product_reviews')->insertGetId([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'order_item_id' => null,
                    'rating' => $reviewTemplate['rating'],
                    'title' => $reviewTemplate['title'],
                    'review' => $reviewTemplate['review'],
                    'images' => null, // No images untuk simplicity
                    'helpful_count' => fake()->numberBetween(0, 30),
                    'is_verified' => fake()->boolean(40),
                    'is_approved' => $isApproved,
                    'approved_by' => $isApproved ? $adminUser->id : null,
                    'approved_at' => $isApproved ? Carbon::parse($createdAt)->addHours(2) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                if ($reviewId) {
                    $insertedCount++;

                    // Update progress setiap 10 reviews
                    if ($insertedCount % 10 === 0) {
                        $this->command->info("✅ Inserted {$insertedCount} reviews...");
                    }
                }

            } catch (\Exception $e) {
                $this->command->error("❌ Error inserting review {$i}: " . $e->getMessage());
                // Continue dengan review berikutnya
                continue;
            }
        }

        $this->command->info("✅ Successfully inserted {$insertedCount} reviews");

        // Update product ratings dengan simple query
        $this->updateProductRatingsSimple();

        $this->command->info('🎉 ProductReviewsSeeder completed successfully!');
    }

    /**
     * Update product ratings dengan metode yang sangat simple
     */
    private function updateProductRatingsSimple(): void
    {
        $this->command->info('📊 Updating product ratings...');

        try {
            // Update menggunakan simple SQL tanpa JSON functions
            $sql = "
                UPDATE products p
                SET
                    rating_average = (
                        SELECT ROUND(AVG(rating), 2)
                        FROM product_reviews pr
                        WHERE pr.product_id = p.id AND pr.is_approved = 1
                    ),
                    rating_count = (
                        SELECT COUNT(*)
                        FROM product_reviews pr
                        WHERE pr.product_id = p.id AND pr.is_approved = 1
                    )
                WHERE EXISTS (
                    SELECT 1
                    FROM product_reviews pr
                    WHERE pr.product_id = p.id AND pr.is_approved = 1
                )
            ";

            $affectedRows = DB::update($sql);
            $this->command->info("✅ Updated ratings for {$affectedRows} products");

            // Show some statistics
            $stats = DB::select("
                SELECT
                    COUNT(*) as total_reviews,
                    COUNT(CASE WHEN is_approved = 1 THEN 1 END) as approved_reviews,
                    ROUND(AVG(rating), 2) as average_rating,
                    COUNT(DISTINCT product_id) as products_with_reviews
                FROM product_reviews
            ")[0];

            $this->command->info("📈 Review Statistics:");
            $this->command->info("   - Total Reviews: {$stats->total_reviews}");
            $this->command->info("   - Approved Reviews: {$stats->approved_reviews}");
            $this->command->info("   - Average Rating: {$stats->average_rating}");
            $this->command->info("   - Products with Reviews: {$stats->products_with_reviews}");

        } catch (\Exception $e) {
            $this->command->error('❌ Error updating product ratings: ' . $e->getMessage());
        }
    }
}
