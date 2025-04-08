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

    public function updated(RealEstate $realEstate){
        if($realEstate->shares_sold== $realEstate->total_shares){
            // TODO should make the share certificate and distribute the spv on the subscribed customers
        }
    }
}
