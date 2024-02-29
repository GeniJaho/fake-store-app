<?php

namespace App\DTO\Products;

use Spatie\LaravelData\Data;

class FakeStoreProductRatingData extends Data
{
    public function __construct(
        public float $rate,
        public int $count,
    ) {
    }
}
