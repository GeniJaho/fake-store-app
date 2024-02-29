<?php

namespace App\DTO\Products;

use Spatie\LaravelData\Data;

class ProductCategoryData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}