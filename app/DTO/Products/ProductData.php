<?php

namespace App\DTO\Products;

use App\Models\Product;
use Spatie\LaravelData\Data;

class ProductData extends Data
{
    public function __construct(
        public int $id,
        public int $fake_store_id,
        public string $title,
        public ProductCategoryData $category,
        public float $price,
        public string $description,
        public string $image,
        public ?float $rating_rate,
        public int $rating_count,
    ) {}

    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->id,
            fake_store_id: $product->fake_store_id,
            title: $product->title,
            category: ProductCategoryData::from($product->category),
            price: $product->price,
            description: $product->description,
            image: $product->image,
            rating_rate: $product->rating_rate,
            rating_count: $product->rating_count,
        );
    }
}