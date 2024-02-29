<?php

namespace App\Console\Commands;

use App\Http\Integrations\FakeStore\FakeStoreConnector;
use App\Http\Integrations\FakeStore\Requests\GetProductsRequest;
use App\Models\Product;
use Illuminate\Console\Command;

class SyncFakeStoreProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-fake-store-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs Fake Store products with the database.';

    public function handle(): void
    {
        $con = new FakeStoreConnector;

        $response = $con->send(new GetProductsRequest);

        collect($response->array())->chunk(1000)->each(function ($products) {
            Product::upsert(
                $products->map(fn ($product) => [
                    'fake_store_id' => $product['id'],
                    'title' => $product['title'],
                    'price' => $product['price'],
                    'description' => $product['description'],
                    'category' => $product['category'],
                    'image' => $product['image'],
                    'rating_rate' => $product['rating']['rate'],
                    'rating_count' => $product['rating']['count'],
                ])->toArray(),
                ['fake_store_id'],
            );
        });
    }
}
