<?php

namespace App\Http\Controllers\Products;

use App\Actions\Products\StoreProductImageAction;
use App\DTO\Products\ProductData;
use App\DTO\Products\UpdateProductData;
use App\Exceptions\CannotStoreImageException;
use App\Http\Controllers\Controller;
use App\Models\Product;

class UpdateProductController extends Controller
{
    /**
     * @throws CannotStoreImageException
     */
    public function __invoke(
        Product $product,
        UpdateProductData $data,
        StoreProductImageAction $storeProductImageAction,
    ): ProductData {
        $imageUrl = $storeProductImageAction->run($data->image) ?? $product->image;

        $product->update([
            ...$data->except('image')->all(),
            'image' => $imageUrl,
        ]);

        return ProductData::from($product);
    }
}
