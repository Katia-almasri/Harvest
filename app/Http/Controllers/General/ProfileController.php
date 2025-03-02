<?php

namespace App\Http\Controllers\General;

use App\Enums\General\StatusCodeEnum;
use App\Enums\Media\MediaCollectionType;
use App\Enums\Media\MediaType;
use App\Helpers\MediaHelper;
use App\Http\Requests\General\Media\StoreImageRequest;
use App\Http\Resources\General\MediaResource;
use App\Services\User\MediaService;

class ProfileController extends ApiController
{
    public function __construct(private MediaService $mediaService){}
    /**
     * Upload Image
     * @param StoreImageRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function uploadImage(StoreImageRequest $request){
        $user = auth()->user();
        $image = $this->mediaService->createFromRequest($user);
        //2. add the new image
        return $this->apiResponse(new MediaResource($image), StatusCodeEnum::STATUS_OK, __('messages.successfully_image_updated'));

    }

    /**
     * Delete Profile Image
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function deleteImage(){
        $user = auth()->user();
        $image = $this->mediaService->delete($user);
        return $this->apiResponse(new MediaResource($image), StatusCodeEnum::STATUS_OK, __('messages.successfully_deleted_image'));
    }
}
