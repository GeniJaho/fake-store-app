<?php

namespace App\Http\Integrations\FakeStore\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetProductsRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/products';
    }
}
