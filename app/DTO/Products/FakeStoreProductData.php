<?php

namespace App\DTO\Products;

use Spatie\LaravelData\Data;

class FakeStoreProductData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public float $price,
        public string $description,
        public string $image,
        public string $category,
        public FakeStoreProductRatingData $rating,
    ) {
    }
}
