<?php

namespace App\General;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

interface MediaInterface
{
    public function index($model, $mediaCollectionType);
    public function create($model, $file, $mediaCollectionType);
    public function update();
    public function delete($model, $media);
    public function show($model, Media $media);
}
