<?php

namespace App\DTO\Products;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Dimensions;
use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class UpdateProductData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $title,
        #[Min(1), Max(100000)]
        public float $price,
        #[Max(65535)]
        public string $description,
        #[Image, Max(2048), Dimensions(minWidth: 2, minHeight: 2)]
        public ?UploadedFile $image,
    ) {}
}