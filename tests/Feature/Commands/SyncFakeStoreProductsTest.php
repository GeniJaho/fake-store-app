<?php

use App\Console\Commands\SyncFakeStoreProducts;
use App\Http\Integrations\FakeStore\Requests\GetProductsRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

use function Pest\Laravel\artisan;

it('syncs fake store products with the database', function () {
    MockClient::global([
        GetProductsRequest::class => MockResponse::make(body: [
            [
                'id' => 1,
                'title' => 'Backpack, Fits 15 Laptops',
                'price' => 109.95,
                'description' => 'Your perfect pack for everyday use.',
                'category' => "men's clothing",
                'image' => 'https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg',
                'rating' => [
                    'rate' => 3.9,
                    'count' => 120,
                ],
            ],
        ]),
    ]);

    expect(Product::query()->count())->toBe(0)
        ->and(ProductCategory::query()->count())->toBe(0);

    artisan(SyncFakeStoreProducts::class);

    expect(ProductCategory::query()->count())->toBe(1)
        ->and(ProductCategory::query()->first())
        ->name->toBe("men's clothing");

    expect(Product::query()->count())->toBe(1)
        ->and(Product::query()->first())
        ->fake_store_id->toBe(1)
        ->title->toBe('Backpack, Fits 15 Laptops')
        ->price->toBe(109.95)
        ->description->toBe('Your perfect pack for everyday use.')
        ->category_id->toBe(ProductCategory::query()->first()->id)
        ->image->toBe('https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg')
        ->rating_rate->toBe(3.9)
        ->rating_count->toBe(120);
});

it('does not store a record twice', function () {
    MockClient::global([
        GetProductsRequest::class => MockResponse::make(body: [
            [
                'id' => 1,
                'title' => 'Backpack, Fits 15 Laptops',
                'price' => 109.95,
                'description' => 'Your perfect pack for everyday use.',
                'category' => "men's clothing",
                'image' => 'https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg',
                'rating' => [
                    'rate' => 3.9,
                    'count' => 120,
                ],
            ],
        ]),
    ]);

    expect(Product::query()->count())->toBe(0);

    artisan(SyncFakeStoreProducts::class);
    artisan(SyncFakeStoreProducts::class);

    expect(Product::query()->count())->toBe(1);
});

it('updates the record if it has changed from the api', function () {
    MockClient::global([
        GetProductsRequest::class => MockResponse::make(body: [
            [
                'id' => 1,
                'title' => 'Backpack, Fits 15 Laptops',
                'price' => 109.95,
                'description' => 'Your perfect pack for everyday use.',
                'category' => "men's clothing",
                'image' => 'https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg',
                'rating' => [
                    'rate' => 3.9,
                    'count' => 120,
                ],
            ],
        ]),
    ]);

    $oldCategory = ProductCategory::factory()->create(['name' => 'Old category']);
    $product = Product::factory()->create([
        'fake_store_id' => 1,
        'title' => 'Old title',
        'price' => 100,
        'description' => 'Old description',
        'category_id' => $oldCategory->id,
        'image' => 'Old image',
        'rating_rate' => 1.0,
        'rating_count' => 1,
    ]);

    artisan(SyncFakeStoreProducts::class);

    expect($product->fresh())
        ->fake_store_id->toBe(1)
        ->title->toBe('Backpack, Fits 15 Laptops')
        ->price->toBe(109.95)
        ->description->toBe('Your perfect pack for everyday use.')
        ->category_id->toBe(ProductCategory::query()->firstWhere('name', "men's clothing")->id)
        ->image->toBe('https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg')
        ->rating_rate->toBe(3.9)
        ->rating_count->toBe(120);
});
