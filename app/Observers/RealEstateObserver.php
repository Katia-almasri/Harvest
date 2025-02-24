<?php

namespace App\Observers;

use App\Models\RealEstate\RealEstate;

class RealEstateObserver
{
    public function deleted(RealEstate $realEstate)
    {
        // delete all the corresponding documents and images
        $medias = $realEstate->medias()->get();
        foreach ($medias as $media) {
            $media->delete();
        }
    }
}
