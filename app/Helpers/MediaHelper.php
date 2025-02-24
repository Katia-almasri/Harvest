<?php
namespace App\Helpers;

class MediaHelper{
    public static function addMediaFromRequest($model, $mediaType, $mediaCollectionType){
        return $model->addMediaFromRequest($mediaType)->toMediaCollection($mediaCollectionType);
    }

    public static function addMedia($model, $file, $mediaCollectionType){
        return $model->addMedia($file)->toMediaCollection($mediaCollectionType);
    }

    public static function deleteMedia($user, $mediaCollectionType){
        return $user->getMedia($mediaCollectionType)->sortByDesc('created_at')->first()?->delete();

    }
}
