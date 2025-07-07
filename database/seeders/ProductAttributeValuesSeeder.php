<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;

class ProductAttributeValuesSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        $attributes = ProductAttribute::all();

        foreach ($products as $product) {
            // Assign 1-3 attributes to each product
            $selectedAttributes = $attributes->random(rand(1, 3));

            foreach ($selectedAttributes as $attribute) {
                $value = $this->getRandomAttributeValue($attribute);

                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                    'value_text' => $attribute->type === 'text' ? fake()->word() : null,
                    'value_number' => $attribute->type === 'number' ? rand(1, 100) : null,
                    'value_boolean' => $attribute->type === 'boolean' ? (bool)rand(0, 1) : null
                ]);
            }
        }
    }

    protected function getRandomAttributeValue($attribute)
    {
        if ($attribute->type === 'select' || $attribute->type === 'multiselect' || $attribute->type === 'color') {
            $options = json_decode($attribute->options, true);
            $randomOption = $options[array_rand($options)];
            return $randomOption['value'];
        }

        return null;
    }
}
