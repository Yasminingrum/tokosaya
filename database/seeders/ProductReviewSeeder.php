<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;

class ProductReviewsTableSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        $customers = User::where('role_id', 4)->get(); // Get all customers

        foreach ($products as $product) {
            // Create 0-5 reviews per product
            $reviewCount = rand(0, 5);

            for ($i = 0; $i < $reviewCount; $i++) {
                $customer = $customers->random();
                $order = Order::where('user_id', $customer->id)
                    ->where('status', 'delivered')
                    ->inRandomOrder()
                    ->first();

                $orderItem = $order ? $order->items()->where('product_id', $product->id)->first() : null;

                $rating = rand(3, 5); // Mostly positive reviews
                if (rand(1, 10) === 1) $rating = rand(1, 2); // 10% chance of negative review

                $reviewDate = Carbon::now()->subDays(rand(0, 90));

                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $customer->id,
                    'order_item_id' => $orderItem ? $orderItem->id : null,
                    'rating' => $rating,
                    'title' => fake()->sentence(),
                    'review' => fake()->paragraphs(rand(1, 3), true),
                    'images' => rand(0, 1) ? json_encode([
                        ['url' => 'reviews/' . $product->id . '/image1.jpg', 'alt' => 'Review image 1'],
                        ['url' => 'reviews/' . $product->id . '/image2.jpg', 'alt' => 'Review image 2']
                    ]) : null,
                    'helpful_count' => rand(0, 50),
                    'is_verified' => $orderItem ? true : false,
                    'is_approved' => true,
                    'approved_by' => rand(1, 4), // Random admin/manager
                    'approved_at' => $reviewDate->addHours(rand(1, 24)),
                    'created_at' => $reviewDate,
                    'updated_at' => $reviewDate
                ]);
            }

            // Update product rating
            $this->updateProductRating($product);
        }
    }

    protected function updateProductRating($product)
    {
        $reviews = $product->reviews()->where('is_approved', true)->get();

        if ($reviews->count() > 0) {
            $averageRating = $reviews->avg('rating');
            $product->update([
                'rating_average' => round($averageRating, 2),
                'rating_count' => $reviews->count()
            ]);
        }
    }
}
