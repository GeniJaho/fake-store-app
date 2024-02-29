<?php

namespace App\Actions\Products;

use App\Exceptions\CannotStoreImageException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoreProductImageAction
{
    /**
     * @throws CannotStoreImageException
     */
    public function run(?UploadedFile $image): ?string
    {
        if ($image === null) {
            return null;
        }

        $path = $image->store('products', 'public');

        if ($path === false) {
            throw new CannotStoreImageException('Could not store the image.');
        }

        return Storage::disk('public')->url($path);
    }
}