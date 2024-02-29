<?php

namespace App\Console\Commands;

use App\DTO\Products\FakeStoreProductData;
use App\Http\Integrations\FakeStore\FakeStoreConnector;
use App\Http\Integrations\FakeStore\Requests\GetProductsRequest;
use App\Models\Product;
use App\Models\ProductCategory;
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

    public function handle(FakeStoreConnector $connector): void
    {
        $response = $connector->send(new GetProductsRequest);

        /** @var FakeStoreProductData[] $products */
        $products = $response->dtoOrFail();

        $this->syncCategories($products);

        $categoryIds = ProductCategory::query()->pluck('id', 'name');

        collect($products)->chunk(1000)->each(fn ($products) => Product::upsert(
            $products->map(fn (FakeStoreProductData $product) => [
                'fake_store_id' => $product->id,
                'title' => $product->title,
                'price' => $product->price * 100,
                'description' => $product->description,
                'category_id' => $categoryIds[$product->category],
                'image' => $product->image,
                'rating_rate' => $product->rating->rate,
                'rating_count' => $product->rating->count,
            ])->toArray(),
            ['fake_store_id']),
        );
    }

    /**
     * @param  FakeStoreProductData[]  $products
     */
    private function syncCategories(array $products): void
    {
        $categoryNames = collect($products)
            ->map(fn (FakeStoreProductData $product) => ['name' => $product->category])
            ->unique(fn (array $category) => $category['name']);

        ProductCategory::query()->insertOrIgnore($categoryNames->toArray());
    }
}
