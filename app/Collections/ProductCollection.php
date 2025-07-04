<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ProductCollection extends Collection
{
    /**
     * Get products grouped by category
     */
    public function groupByCategory()
    {
        return $this->groupBy(function ($product) {
            return $product->category ? $product->category->name : 'Uncategorized';
        });
    }

    /**
     * Get products within price range
     */
    public function inPriceRange($minPrice = null, $maxPrice = null)
    {
        return $this->filter(function ($product) use ($minPrice, $maxPrice) {
            if ($minPrice !== null && $product->price < $minPrice) {
                return false;
            }
            if ($maxPrice !== null && $product->price > $maxPrice) {
                return false;
            }
            return true;
        });
    }

    /**
     * Get expensive products (price > 100,000)
     */
    public function expensive()
    {
        return $this->filter(function ($product) {
            return $product->price > 100000;
        });
    }

    /**
     * Get affordable products (price <= 100,000)
     */
    public function affordable()
    {
        return $this->filter(function ($product) {
            return $product->price <= 100000;
        });
    }

    /**
     * Get products that are in stock
     */
    public function inStock()
    {
        return $this->filter(function ($product) {
            return $product->stock > 0;
        });
    }

    /**
     * Get products that are out of stock
     */
    public function outOfStock()
    {
        return $this->filter(function ($product) {
            return $product->stock == 0;
        });
    }

    /**
     * Get products with low stock
     */
    public function lowStock($threshold = 10)
    {
        return $this->filter(function ($product) use ($threshold) {
            return $product->stock > 0 && $product->stock < $threshold;
        });
    }

    /**
     * Search products by name or description
     */
    public function searchProducts($searchTerm)
    {
        return $this->filter(function ($product) use ($searchTerm) {
            return stripos($product->name, $searchTerm) !== false ||
                   stripos($product->description, $searchTerm) !== false;
        });
    }

    /**
     * Sort by name
     */
    public function sortByName($direction = 'asc')
    {
        return $direction === 'desc'
            ? $this->sortByDesc('name')
            : $this->sortBy('name');
    }

    /**
     * Sort by price
     */
    public function sortByPrice($direction = 'asc')
    {
        return $direction === 'desc'
            ? $this->sortByDesc('price')
            : $this->sortBy('price');
    }

    /**
     * Sort by stock quantity
     */
    public function sortByStock($direction = 'asc')
    {
        return $direction === 'desc'
            ? $this->sortByDesc('stock')
            : $this->sortBy('stock');
    }

    /**
     * Get the most expensive products
     */
    public function mostExpensive($limit = 5)
    {
        return $this->sortByDesc('price')->take($limit);
    }

    /**
     * Get the cheapest products
     */
    public function cheapest($limit = 5)
    {
        return $this->sortBy('price')->take($limit);
    }

    /**
     * Get products by specific category
     */
    public function byCategory($categoryId)
    {
        return $this->filter(function ($product) use ($categoryId) {
            return $product->category_id == $categoryId;
        });
    }

    /**
     * Get total inventory value
     */
    public function totalInventoryValue()
    {
        return $this->sum(function ($product) {
            return $product->price * $product->stock;
        });
    }

    /**
     * Get average price
     */
    public function averagePrice()
    {
        return $this->isEmpty() ? 0 : $this->avg('price');
    }

    /**
     * Get price statistics
     */
    public function priceStatistics()
    {
        if ($this->isEmpty()) {
            return [
                'min' => 0,
                'max' => 0,
                'average' => 0,
                'total_value' => 0,
                'median' => 0
            ];
        }

        $prices = $this->pluck('price')->sort()->values();
        $count = $prices->count();

        return [
            'min' => $this->min('price'),
            'max' => $this->max('price'),
            'average' => $this->avg('price'),
            'total_value' => $this->sum('price'),
            'median' => $count > 0 ? (
                $count % 2 === 0
                    ? ($prices->get($count / 2 - 1) + $prices->get($count / 2)) / 2
                    : $prices->get((int) floor($count / 2))
            ) : 0
        ];
    }

    /**
     * Get stock statistics
     */
    public function stockStatistics()
    {
        if ($this->isEmpty()) {
            return [
                'total_stock' => 0,
                'average_stock' => 0,
                'min_stock' => 0,
                'max_stock' => 0,
                'out_of_stock_count' => 0,
                'low_stock_count' => 0,
                'in_stock_count' => 0
            ];
        }

        return [
            'total_stock' => $this->sum('stock'),
            'average_stock' => $this->avg('stock'),
            'min_stock' => $this->min('stock'),
            'max_stock' => $this->max('stock'),
            'out_of_stock_count' => $this->outOfStock()->count(),
            'low_stock_count' => $this->lowStock()->count(),
            'in_stock_count' => $this->inStock()->count()
        ];
    }

    /**
     * Get products summary by category
     */
    public function categoryStatistics()
    {
        return $this->groupByCategory()->map(function ($products, $categoryName) {
            $productCollection = new static($products);

            return [
                'category' => $categoryName,
                'total_products' => $productCollection->count(),
                'total_value' => $productCollection->sum('price'),
                'average_price' => $productCollection->avg('price'),
                'total_stock' => $productCollection->sum('stock'),
                'inventory_value' => $productCollection->totalInventoryValue(),
                'most_expensive' => $productCollection->max('price'),
                'cheapest' => $productCollection->min('price'),
                'out_of_stock' => $productCollection->outOfStock()->count(),
                'low_stock' => $productCollection->lowStock()->count()
            ];
        });
    }

    /**
     * Get featured products
     */
    public function featured($limit = 6)
    {
        return $this->inStock()
                    ->expensive()
                    ->sortByDesc('price')
                    ->take($limit);
    }

    /**
     * Get products for homepage showcase
     */
    public function forHomepage()
    {
        return [
            'featured' => $this->featured(6),
            'new_arrivals' => $this->sortByDesc('created_at')->take(8),
            'best_sellers' => $this->inStock()->sortByDesc('price')->take(4),
            'on_sale' => $this->inStock()->sortBy('price')->take(4)
        ];
    }

    /**
     * Filter products with advanced criteria
     */
    public function advancedFilter(array $criteria)
    {
        $filtered = $this;

        // Search by keyword
        if (!empty($criteria['search'])) {
            $filtered = $filtered->searchProducts($criteria['search']);
        }

        // Filter by category
        if (!empty($criteria['category_id'])) {
            $filtered = $filtered->byCategory($criteria['category_id']);
        }

        // Filter by price range
        if (!empty($criteria['min_price']) || !empty($criteria['max_price'])) {
            $filtered = $filtered->inPriceRange(
                $criteria['min_price'] ?? null,
                $criteria['max_price'] ?? null
            );
        }

        // Filter by stock status
        if (!empty($criteria['stock_status'])) {
            switch ($criteria['stock_status']) {
                case 'in_stock':
                    $filtered = $filtered->inStock();
                    break;
                case 'out_of_stock':
                    $filtered = $filtered->outOfStock();
                    break;
                case 'low_stock':
                    $filtered = $filtered->lowStock();
                    break;
            }
        }

        // Apply sorting
        if (!empty($criteria['sort_by'])) {
            switch ($criteria['sort_by']) {
                case 'name_asc':
                    $filtered = $filtered->sortByName('asc');
                    break;
                case 'name_desc':
                    $filtered = $filtered->sortByName('desc');
                    break;
                case 'price_asc':
                    $filtered = $filtered->sortByPrice('asc');
                    break;
                case 'price_desc':
                    $filtered = $filtered->sortByPrice('desc');
                    break;
                case 'stock_asc':
                    $filtered = $filtered->sortByStock('asc');
                    break;
                case 'stock_desc':
                    $filtered = $filtered->sortByStock('desc');
                    break;
                default:
                    $filtered = $filtered->sortByDesc('created_at');
            }
        }

        return $filtered->values();
    }

    /**
     * Get products that need restock
     */
    public function needsRestock($threshold = 5)
    {
        return $this->filter(function ($product) use ($threshold) {
            return $product->stock <= $threshold;
        });
    }

    /**
     * Get products by price tier
     */
    public function byPriceTier()
    {
        return [
            'budget' => $this->filter(function ($product) {
                return $product->price < 50000;
            }),
            'mid_range' => $this->filter(function ($product) {
                return $product->price >= 50000 && $product->price < 200000;
            }),
            'premium' => $this->filter(function ($product) {
                return $product->price >= 200000 && $product->price < 1000000;
            }),
            'luxury' => $this->filter(function ($product) {
                return $product->price >= 1000000;
            })
        ];
    }

    /**
     * Get random products
     */
    public function randomProducts($count = 1)
    {
        return $this->shuffle()->take($count);
    }

    /**
     * Get products with pagination data
     */
    public function paginateCollection($perPage = 12, $page = 1)
    {
        $total = $this->count();
        $offset = ($page - 1) * $perPage;
        $items = $this->slice($offset, $perPage)->values();

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $total > 0 ? (int) ceil($total / $perPage) : 1,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
            'has_more' => $page < ceil($total / $perPage)
        ];
    }

    /**
     * Get quick analytics summary
     */
    public function quickStats()
    {
        return [
            'total_products' => $this->count(),
            'total_inventory_value' => $this->totalInventoryValue(),
            'average_price' => $this->averagePrice(),
            'in_stock_count' => $this->inStock()->count(),
            'out_of_stock_count' => $this->outOfStock()->count(),
            'low_stock_count' => $this->lowStock()->count(),
            'expensive_count' => $this->expensive()->count(),
            'affordable_count' => $this->affordable()->count()
        ];
    }

    /**
     * Get trending products
     */
    public function trending($limit = 6)
    {
        return $this->inStock()
                    ->filter(function ($product) {
                        return $product->created_at->diffInDays() <= 60 || $product->price > 100000;
                    })
                    ->sortByDesc(function ($product) {
                        $ageScore = max(0, 60 - $product->created_at->diffInDays());
                        $priceScore = $product->price / 100000;
                        return $ageScore + $priceScore;
                    })
                    ->take($limit);
    }

    /**
     * Get inventory insights for dashboard
     */
    public function inventoryInsights()
    {
        $totalValue = $this->totalInventoryValue();
        $totalProducts = $this->count();

        return [
            'total_inventory_value' => $totalValue,
            'total_products' => $totalProducts,
            'average_product_value' => $totalProducts > 0 ? $totalValue / $totalProducts : 0,
            'stock_distribution' => [
                'in_stock' => $this->inStock()->count(),
                'out_of_stock' => $this->outOfStock()->count(),
                'low_stock' => $this->lowStock()->count(),
                'good_stock' => $this->filter(function ($product) {
                    return $product->stock >= 10;
                })->count()
            ],
            'price_distribution' => $this->byPriceTier(),
            'category_breakdown' => $this->groupByCategory()->map->count(),
            'top_value_products' => $this->sortByDesc(function ($product) {
                return $product->price * $product->stock;
            })->take(5),
            'alerts' => [
                'critical_stock' => $this->filter(function ($product) {
                    return $product->stock <= 2;
                })->count(),
                'expired_soon' => 0,
                'overstock' => $this->filter(function ($product) {
                    return $product->stock > 100;
                })->count()
            ]
        ];
    }

    /**
     * Get performance metrics for dashboard
     */
    public function performanceMetrics()
    {
        return [
            'total_products' => $this->count(),
            'active_products' => $this->inStock()->count(),
            'inactive_products' => $this->outOfStock()->count(),
            'total_inventory_value' => $this->totalInventoryValue(),
            'average_price' => $this->averagePrice(),
            'price_range' => [
                'min' => $this->isEmpty() ? 0 : $this->min('price'),
                'max' => $this->isEmpty() ? 0 : $this->max('price')
            ],
            'stock_metrics' => [
                'total_stock' => $this->sum('stock'),
                'average_stock' => $this->isEmpty() ? 0 : $this->avg('stock'),
                'low_stock_items' => $this->lowStock()->count(),
                'out_of_stock_items' => $this->outOfStock()->count()
            ],
            'category_performance' => $this->categoryStatistics()
        ];
    }

    /**
     * Get pricing analysis
     */
    public function pricingAnalysis()
    {
        if ($this->isEmpty()) {
            return [
                'statistics' => $this->priceStatistics(),
                'distribution' => $this->byPriceTier(),
                'quartiles' => ['q1' => 0, 'q2' => 0, 'q3' => 0],
                'outliers' => ['expensive' => 0, 'cheap' => 0],
                'recommendations' => ['overpriced' => collect(), 'underpriced' => collect()]
            ];
        }

        $avgPrice = $this->avg('price');

        return [
            'statistics' => $this->priceStatistics(),
            'distribution' => $this->byPriceTier(),
            'quartiles' => [
                'q1' => $this->percentile(25),
                'q2' => $this->percentile(50),
                'q3' => $this->percentile(75)
            ],
            'outliers' => [
                'expensive' => $this->filter(function ($product) use ($avgPrice) {
                    return $product->price > ($avgPrice * 2);
                })->count(),
                'cheap' => $this->filter(function ($product) use ($avgPrice) {
                    return $product->price < ($avgPrice * 0.5);
                })->count()
            ],
            'recommendations' => [
                'overpriced' => $this->filter(function ($product) use ($avgPrice) {
                    return $product->price > ($avgPrice * 1.5);
                }),
                'underpriced' => $this->filter(function ($product) use ($avgPrice) {
                    return $product->price < ($avgPrice * 0.7);
                })
            ]
        ];
    }

    /**
     * Calculate percentile
     */
    public function percentile($percentile)
    {
        if ($this->isEmpty()) {
            return 0;
        }

        $prices = $this->pluck('price')->sort()->values();
        $count = $prices->count();
        $index = ($percentile / 100) * ($count - 1);

        $lower = (int) floor($index);
        $upper = (int) ceil($index);

        if ($lower === $upper) {
            return $prices->get($lower);
        }

        $weight = $index - $lower;
        return $prices->get($lower) * (1 - $weight) + $prices->get($upper) * $weight;
    }

    /**
     * Get product recommendations based on similarity
     */
    public function getRecommendations($product, $limit = 6)
    {
        return $this->filter(function ($p) use ($product) {
                return $p->id !== $product->id;
            })
            ->sortBy(function ($p) use ($product) {
                $score = 0;

                if ($p->category_id === $product->category_id) {
                    $score += 100;
                }

                $priceDiff = abs($p->price - $product->price);
                $score += max(0, 100 - ($priceDiff / 1000));

                return -$score;
            })
            ->take($limit);
    }

    /**
     * Smart search with suggestions
     */
    public function smartSearch($query)
    {
        $query = strtolower(trim($query));
        $words = explode(' ', $query);

        $directMatches = $this->filter(function ($product) use ($query) {
            return stripos($product->name, $query) !== false ||
                   stripos($product->description, $query) !== false;
        });

        $partialMatches = $this->filter(function ($product) use ($words) {
            foreach ($words as $word) {
                if (strlen($word) > 2 &&
                    (stripos($product->name, $word) !== false ||
                     stripos($product->description, $word) !== false)) {
                    return true;
                }
            }
            return false;
        })->diff($directMatches);

        $results = $directMatches->merge($partialMatches);

        return [
            'results' => $results,
            'total_found' => $results->count(),
            'suggestions' => [],
            'related_terms' => []
        ];
    }

    /**
     * Get seasonal products
     */
    public function seasonal()
    {
        return $this->filter(function ($product) {
            // Simple implementation - return recent products
            return $product->created_at->diffInDays() <= 30;
        });
    }

    /**
     * Export data in different formats
     */
    public function export($format = 'array')
    {
        $data = $this->map(function ($product) {
            return [
                'ID' => $product->id,
                'Name' => $product->name,
                'Description' => $product->description,
                'Price' => $product->price,
                'Category' => $product->category ? $product->category->name : 'Uncategorized',
                'Stock' => $product->stock,
                'Inventory Value' => $product->price * $product->stock,
                'Status' => $product->stock > 0 ? 'In Stock' : 'Out of Stock',
                'Created At' => $product->created_at->format('Y-m-d H:i:s'),
                'Updated At' => $product->updated_at->format('Y-m-d H:i:s')
            ];
        });

        switch ($format) {
            case 'csv':
                $csvData = [];
                if ($data->count() > 0) {
                    $csvData[] = array_keys($data->first());
                    foreach ($data as $row) {
                        $csvData[] = array_values($row);
                    }
                }
                return $csvData;

            case 'json':
                return $data->toArray();

            default:
                return $data;
        }
    }
}
