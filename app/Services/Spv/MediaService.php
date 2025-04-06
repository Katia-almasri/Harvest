<?php
namespace App\Services\Spv;
use App\Enums\Media\MediaCollectionType;
use App\General\MediaInterface;
use App\Helpers\MediaHelper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService implements MediaInterface{

    public function index($model, $mediaCollectionType)
    {
        // TODO: Implement index() method.
    }

    public function create($model, $file, $mediaCollectionType=MediaCollectionType::SPV_LEGAL_DOCUMENT)
    {
        return MediaHelper::addMedia($model, $file, $mediaCollectionType);
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete($model, $media=null)
    {
        $legalDocument = $this->show($model);
        $legalDocument->delete();
        return $legalDocument;
    }

    public function show($model, Media $media=null)
    {
        return $model->legalDocument();
    }
}
