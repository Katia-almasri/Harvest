<?php

namespace App\Models\RealEstate;

use App\Enums\Media\MediaCollectionType;
use App\Models\BusinessLogic\SPV;
use App\Models\Common\City;
use App\Models\Payment;
use App\Models\User;
use App\Observers\RealEstateObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[ObservedBy([RealEstateObserver::class])]
class RealEstate extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\RealEstate\RealEstateFactory> */
    use HasFactory, softDeletes, InteractsWithMedia;
    protected $guarded = ['id'];

    ################## Relations #######################
    public function admin(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id');
    }

    public function images(){
        return $this->morphMany(Media::class, 'model')->where('collection_name', MediaCollectionType::REAL_ESTATE_IMAGE);
    }

    public function documents(){
        return $this->morphMany(Media::class, 'model')->where('collection_name', MediaCollectionType::REAL_ESTATE_DOCUMENT);
    }

    public function medias(){
        return $this->morphMany(Media::class, 'model');
    }

    public function spv(){
        return $this->belongsTo(SPV::class, 'spv_id');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }


}
