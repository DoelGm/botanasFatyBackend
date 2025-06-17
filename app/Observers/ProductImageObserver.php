<?php

namespace App\Observers;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageObserver
{
    /**
     * Handle the ProductImage "created" event.
     */
    public function created(ProductImage $productImage): void
    {
        //
    }

    /**
     * Handle the ProductImage "updated" event.
     */
    public function updated(ProductImage $productImage): void
    {
        //
    }

    /**
     * Handle the ProductImage "deleted" event.
     */
    public function deleted(ProductImage $image): void
    {
         if ($image->cloudinary_public_id) {
            Storage::disk('cloudinary')->delete($image->cloudinary_public_id);
        }
    }

    /**
     * Handle the ProductImage "restored" event.
     */
    public function restored(ProductImage $productImage): void
    {
        //
    }

    /**
     * Handle the ProductImage "force deleted" event.
     */
    public function forceDeleted(ProductImage $productImage): void
    {
        //
    }


    public function deleting(ProductImage $image)
    {
        // Elimina del almacenamiento en Cloudinary
        if ($image->cloudinary_public_id) {
            Storage::disk('cloudinary')->delete($image->cloudinary_public_id);
        }
    }


}
