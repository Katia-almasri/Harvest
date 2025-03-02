<?php
namespace App\Services\User;
use App\Enums\Media\MediaCollectionType;
use App\Enums\Media\MediaType;
use App\General\MediaInterface;
use App\Helpers\MediaHelper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService implements MediaInterface{
    public function index($model, $mediaCollectionType=MediaCollectionType::PROFILE_IMAGE)
    {
        return $model->image()->first();
    }

    public function create($model, $file, $mediaCollectionType=MediaCollectionType::REAL_ESTATE_IMAGE)
    {
        return MediaHelper::addMedia($model, $file, $mediaCollectionType);
    }

    public function createFromRequest($model, $mediaType = MediaType::IMAGE, $mediaCollectionType = MediaCollectionType::PROFILE_IMAGE){
        return MediaHelper::addMediaFromRequest($model, $mediaType, $mediaCollectionType);
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete($model, $media=null){
        $image = $this->show($model);
        $image->delete();
        return $image;
    }
    public function show($model, Media $media=null){
        return $model->image();
    }
}
