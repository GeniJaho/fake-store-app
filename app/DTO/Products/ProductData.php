<?php

namespace App\DTO\Products;

use Spatie\LaravelData\Data;

class ProductData extends Data
{
    public function __construct(
        public string $title,
        public float $price,
        public string $description,
        public string $image,
        public string $category,
        public ?float $rating_rate,
        public int $rating_count,
    ) {}
}