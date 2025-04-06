<?php

namespace App\Http\Controllers\Admin\RealEstate;

use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\SPV\CreateSpvRequest;
use App\Http\Resources\General\MediaResource;
use App\Http\Resources\Spv\SpvResource;
use App\Models\BusinessLogic\SPV;
use App\Models\RealEstate\RealEstate;
use App\Services\Spv\MediaService;
use App\Services\Spv\SpvService;
use Exception;
use Illuminate\Http\Request;

class SpvController extends ApiController
{
    public function __construct(private SpvService $spvService, private MediaService $mediaService){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSpvRequest $request, RealEstate $realEstate)
    {
        try {

            $data = $request->validated();
            $data['real_estate_id'] = $realEstate->id;

            $spv = $this->spvService->store($data);
            $this->mediaService->create($spv, $data['legal_document']);

            $realEstate->spv_id = $spv->id;
            $realEstate->save();

            return $this->apiResponse(new SpvResource($spv), StatusCodeEnum::STATUS_OK, __('messages.spv.created'));
        }catch (Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, __($exception->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SPV $spv)
    {
        return $this->apiResponse(new SpvResource($spv), StatusCodeEnum::STATUS_OK, __('messages.spv.show'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SPV $spv)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SPV $spv)
    {
        try {
            $destroyedSpv = $this->spvService->delete($spv);
            return $this->apiResponse(new SpvResource($destroyedSpv), StatusCodeEnum::STATUS_OK, __("messages.spv.destroyed"));
        }catch (\Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, $exception->getMessage());
        }
    }
}

