<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;

class ProductAttributesTableSeeder extends Seeder
{
    public function run()
    {
        $attributes = [
            [
                'name' => 'Warna',
                'type' => 'color',
                'options' => json_encode([
                    ['label' => 'Merah', 'value' => '#FF0000'],
                    ['label' => 'Biru', 'value' => '#0000FF'],
                    ['label' => 'Hijau', 'value' => '#00FF00'],
                    ['label' => 'Hitam', 'value' => '#000000'],
                    ['label' => 'Putih', 'value' => '#FFFFFF']
                ]),
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 1
            ],
            [
                'name' => 'Ukuran',
                'type' => 'select',
                'options' => json_encode([
                    ['label' => 'XS', 'value' => 'xs'],
                    ['label' => 'S', 'value' => 's'],
                    ['label' => 'M', 'value' => 'm'],
                    ['label' => 'L', 'value' => 'l'],
                    ['label' => 'XL', 'value' => 'xl'],
                    ['label' => 'XXL', 'value' => 'xxl']
                ]),
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2
            ],
            [
                'name' => 'Kapasitas',
                'type' => 'select',
                'options' => json_encode([
                    ['label' => '32GB', 'value' => '32gb'],
                    ['label' => '64GB', 'value' => '64gb'],
                    ['label' => '128GB', 'value' => '128gb'],
                    ['label' => '256GB', 'value' => '256gb'],
                    ['label' => '512GB', 'value' => '512gb'],
                    ['label' => '1TB', 'value' => '1tb']
                ]),
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Material',
                'type' => 'select',
                'options' => json_encode([
                    ['label' => 'Katun', 'value' => 'cotton'],
                    ['label' => 'Poliester', 'value' => 'polyester'],
                    ['label' => 'Sutra', 'value' => 'silk'],
                    ['label' => 'Denim', 'value' => 'denim'],
                    ['label' => 'Kulit', 'value' => 'leather']
                ]),
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 4
            ],
            [
                'name' => 'Garansi',
                'type' => 'boolean',
                'options' => null,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 5
            ]
        ];

        foreach ($attributes as $attribute) {
            ProductAttribute::create($attribute);
        }
    }
}
