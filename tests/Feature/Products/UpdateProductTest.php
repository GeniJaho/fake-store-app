<?php

use App\Actions\Products\StoreProductImageAction;
use App\Exceptions\CannotStoreImageException;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\patchJson;

beforeEach(function () {
    Storage::fake('public');
});

function getValidProductData(array $overrides = []): array
{
    return [
        'title' => 'New Name',
        'price' => 25.12,
        'description' => 'New Description',
        'image' => UploadedFile::fake()->image('image.jpg'),
        ...$overrides,
    ];
}

test('unauthenticated users can not update products', function () {
    $product = Product::factory()->create();

    $response = patchJson("/api/products/{$product->id}", getValidProductData());

    $response->assertUnauthorized();
});

it('updates a product', function () {
    $user = User::factory()->create();
    $image = UploadedFile::fake()->image('image.jpg');
    $product = Product::factory()->create([
        'fake_store_id' => 1,
        'title' => 'Original Name',
        'price' => 1000,
        'description' => 'Original Description',
        'category' => 'Original Category',
        'image' => 'Old Image URL',
        'rating_rate' => 4.5,
        'rating_count' => 100
    ]);

    $response = actingAs($user)->patchJson("/api/products/{$product->id}", getValidProductData([
        'image' => $image,
    ]));

    $response->assertOk();
    $response->assertJson([
        'title' => 'New Name',
        'price' => 25.12,
        'description' => 'New Description',
        'category' => 'Original Category',
        'image' => Storage::disk('public')->url("products/{$image->hashName()}"),
        'rating_rate' => 4.5,
        'rating_count' => 100,
    ]);
    expect($product->fresh())
        ->title->toBe('New Name')
        ->price->toBe(25.12)
        ->description->toBe('New Description')
        ->category->toBe('Original Category')
        ->image->toBe(Storage::disk('public')->url("products/{$image->hashName()}"))
        ->rating_rate->toBe(4.5)
        ->rating_count->toBe(100);

    Storage::disk('public')->assertExists("/products/{$image->hashName()}");
});

it('only updates valid product properties', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'category' => 'Original Category',
        'rating_rate' => 4.5,
        'rating_count' => 100
    ]);

    $response = actingAs($user)->patchJson("/api/products/{$product->id}", getValidProductData([
        'category' => 'New Category',
        'rating_rate' => 4.5,
        'rating_count' => 100
    ]));

    $response->assertOk();
    expect($product->fresh())
        ->category->toBe('Original Category')
        ->rating_rate->toBe(4.5)
        ->rating_count->toBe(100);
});

it('does not change the image if not given in the request', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'image' => 'Old Image URL',
    ]);

    $response = actingAs($user)->patchJson("/api/products/{$product->id}", getValidProductData([
        'image' => null,
    ]));

    $response->assertOk();
    expect($product->fresh())->image->toBe('Old Image URL');
});

it('does not update the product if the image can not be saved', function () {
    $this->mock(StoreProductImageAction::class)
        ->shouldReceive('run')
        ->andThrow(new CannotStoreImageException('Could not store the image.'));

    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = actingAs($user)->patchJson("/api/products/{$product->id}", getValidProductData());

    $response->assertServerError();
    $response->assertJson(['message' => 'Could not store the image.']);
    expect($product->fresh()->toArray())->toEqualCanonicalizing($product->toArray());
});

it('validates the request', function ($data, $error) {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = actingAs($user)->patchJson("/api/products/{$product->id}", $data);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors($error);
    expect($product->toArray())->toEqualCanonicalizing($product->fresh()->toArray());
})->with([
    'title required' => [getValidProductData(['title' => '']), 'title'],
    'title max 255' => [getValidProductData(['title' => str_repeat('a', 256)]), 'title'],
    'price required' => [getValidProductData(['price' => '']), 'price'],
    'price min 1' => [getValidProductData(['price' => 0]), 'price'],
    'price max 100000' => [getValidProductData(['price' => 100001]), 'price'],
    'description required' => [getValidProductData(['description' => '']), 'description'],
    'description max 65535' => [getValidProductData(['description' => str_repeat('a', 65536)]), 'description'],
    'image valid image' => [getValidProductData(['image' => 'not-an-image']), 'image'],
    'image max 2048' => [getValidProductData(['image' => UploadedFile::fake()->create('image.jpg', 2049)]), 'image'],
    'image dimensions min width' => [getValidProductData(['image' => UploadedFile::fake()->image('image.jpg', 1)]), 'image'],
    'image dimensions min height' => [getValidProductData(['image' => UploadedFile::fake()->image('image.jpg', 100, 1)]), 'image'],
]);

