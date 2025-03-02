<?php
namespace App\Services\Customer;
use App\Enums\Media\MediaCollectionType;
use App\Enums\Media\MediaType;
use App\General\MediaInterface;
use App\Helpers\MediaHelper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService implements MediaInterface{
    public function index($model, $mediaCollectionType)
    {
        // TODO: Implement index() method.
    }

    public function create($model, $file, $mediaCollectionType=MediaCollectionType::CUSTOMER_PASSPORT)
    {
        return MediaHelper::addMedia($model, $file, $mediaCollectionType);
    }

    public function createFromRequest($model, $mediaType = MediaType::IMAGE, $mediaCollectionType = MediaCollectionType::CUSTOMER_PASSPORT){
        return MediaHelper::addMediaFromRequest($model, $mediaType, $mediaCollectionType);
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete($model, $media)
    {
        $media = $this->show($model, $media);
        $model->medias->find($media->id)->delete();
        return $media;
    }

    public function show($model, Media $media)
    {
        // TODO: Implement show() method.
    }
}
