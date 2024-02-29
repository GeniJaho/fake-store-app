<?php

namespace App\Http\Integrations\FakeStore\Requests;

use App\DTO\Products\FakeStoreProductData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

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

    public function createDtoFromResponse(Response $response): mixed
    {
        return FakeStoreProductData::collect($response->array());
    }
}
