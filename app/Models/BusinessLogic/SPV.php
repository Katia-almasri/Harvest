<?php

namespace App\Models\BusinessLogic;

use App\Enums\Media\MediaCollectionType;
use App\Models\RealEstate\RealEstate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SPV extends Model implements HasMedia
{
    use HasFactory, softDeletes, InteractsWithMedia;
    protected $table = 'spv';
    protected $fillable = ['name', 'registration_number', 'legal_document', 'real_estate_id'];

    public function realEstate()
    {
        return $this->BelongsTo(RealEstate::class);
    }

    public function legalDocument()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', MediaCollectionType::SPV_LEGAL_DOCUMENT);
    }

}
