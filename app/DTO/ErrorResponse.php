<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class ErrorResponse extends Data
{
    public function __construct(
        public string $message,
    ) {
    }
}
