<?php

namespace App\Models;

use App\Enums\Media\MediaCollectionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Customer extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory, softDeletes, InteractsWithMedia;

    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function passportImage(){
        return $this->hasOne(Media::class, 'model_id', 'id')->where('collection_name', MediaCollectionType::CUSTOMER_PASSPORT)->latest()->first();
    }

    public function residentialCardImage(){
        return $this->hasOne(Media::class, 'model_id', 'id')->where('collection_name', MediaCollectionType::RESIDENTIAL_CARD)->latest()->first();
    }
}
