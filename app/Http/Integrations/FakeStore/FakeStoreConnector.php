<?php

namespace App\Http\Integrations\FakeStore;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class FakeStoreConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://fakestoreapi.com';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
