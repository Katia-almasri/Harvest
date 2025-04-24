<?php

namespace App\Observers;

use App\Enums\Payment\PaymentStatus;
use App\Enums\RealEstate\RealEstateStatus;
use App\Models\Payment;
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
        if($realEstate->shares_sold == $realEstate->total_shares){

            //1. update the real estate status
            $realEstate->update([
                'status'=>RealEstateStatus::SOLD,
                'funded_at'=>now()
            ]);
            // TODO should make the share certificate and distribute the spv on the subscribed customers
        }
    }
}
