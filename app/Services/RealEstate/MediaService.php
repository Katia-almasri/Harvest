<?php
namespace App\Services\RealEstate;
use App\Enums\Media\MediaCollectionType;
use App\General\MediaInterface;
use App\Helpers\MediaHelper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService implements MediaInterface{
    public function index($model, $mediaCollectionType=MediaCollectionType::REAL_ESTATE_IMAGE)
    {
        switch ($mediaCollectionType) {
            case MediaCollectionType::REAL_ESTATE_IMAGE:
                return $model->images()->get();

            case MediaCollectionType::REAL_ESTATE_DOCUMENT:
                return $model->documents()->get();

            default:
                return null;
        }
    }

    public function create($model, $file, $mediaCollectionType=MediaCollectionType::REAL_ESTATE_IMAGE)
    {
        return MediaHelper::addMedia($model, $file, $mediaCollectionType);
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete($model, $media){
        // check if the media type is images and it is the only image in the real estate, then dont delete
        $media = $this->show($model, $media);
        if($media->collection_name == MediaCollectionType::REAL_ESTATE_IMAGE){
            $images = $model->images()->get();
            if (!$images->contains('id', $media->id)) {
                throw new \Exception("Image not found or does not belong to this Real Estate", 404);
            }

            if($images->count() <=1 ){
                throw new \Exception("Cant delete The Image, The Real Estate Should Have At Least One Image");
            }
        }
        $model->medias->find($media->id)->delete();
        return $media;
    }
    public function show($model, Media $media){
        return $model->medias()->find($media->id);
    }
}
