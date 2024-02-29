<?php

namespace App\Http\Controllers\Products;

use App\Actions\Products\StoreProductImageAction;
use App\DTO\ErrorResponse;
use App\DTO\Products\ProductData;
use App\DTO\Products\UpdateProductData;
use App\Exceptions\CannotStoreImageException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class UpdateProductController extends Controller
{
    public function __invoke(
        Product $product,
        UpdateProductData $data,
        StoreProductImageAction $storeProductImageAction,
    ): ProductData|JsonResponse {
        try {
            $imageUrl = $storeProductImageAction->run($data->image) ?? $product->image;
        } catch (CannotStoreImageException $e) {
            return response()->json(new ErrorResponse($e->getMessage()), 400);
        }

        $product->update([
            ...$data->except('image')->all(),
            'image' => $imageUrl,
        ]);

        return ProductData::from($product);
    }
}
