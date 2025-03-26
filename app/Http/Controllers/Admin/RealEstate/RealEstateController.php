<?php

namespace App\Http\Controllers\Admin\RealEstate;

use App\Enums\General\StatusCodeEnum;
use App\Enums\Media\MediaCollectionType;
use App\Enums\RealEstate\RealEstateStatus;
use App\Exceptions\General\ServerException;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\RealEstate\StoreRealEstateRequest;
use App\Http\Requests\RealEstate\UpdateRealEstateRequest;
use App\Http\Requests\RealEstate\UpdateRealEstateStatusRequest;
use App\Http\Requests\RealEstate\UploadRealEstateDocuments;
use App\Http\Requests\RealEstate\UploadRealEstateImages;
use App\Http\Resources\General\MediaResource;
use App\Http\Resources\RealEstate\RealEstateDetailsResource;
use App\Http\Resources\RealEstate\RealEstateResource;
use App\Models\RealEstate\RealEstate;
use App\Services\FundService;
use App\Services\RealEstate\RealEstateService;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RealEstateController extends ApiController
{

    public function __construct(private RealEstateService $realEstateService, private FundService $fundService){}
    /**
     * Get All Real Estates
     */
    public function index()
    {
        $realEstates = RealEstate::query()->paginate($request->per_page ?? env('PAGINATE'));
        $paginationInfo = $this->formatPaginateData($realEstates);
        return $this->apiResponse(RealEstateResource::collection($realEstates), StatusCodeEnum::STATUS_OK, "Real Estates Fetched Successfully!", $paginationInfo);
    }

    /**
     * Store New Real Estate
     */
    public function store(StoreRealEstateRequest $request)
    {
        try {
            $data = $request->validated();
            $realEstate = $this->realEstateService->store($data);
            return $this->apiResponse(new RealEstateDetailsResource($realEstate), StatusCodeEnum::STATUS_OK, "Real Estate Created Successfully!");
        }catch (ServerException $exception){
            return $this->apiResponse(null, $exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * Display the specified Real Estate
     */
    public function show(RealEstate $realEstate)
    {
        return $this->apiResponse(new RealEstateDetailsResource($realEstate), StatusCodeEnum::STATUS_OK, "Real Estate Fetched Successfully!");
    }


    /**
     * Update the specified real estate.
     */
    public function update(UpdateRealEstateRequest $request, RealEstate $realEstate)
    {
        $data = $request->validated();
        $realEstate = $this->realEstateService->update($data, $realEstate);
        return $this->apiResponse(new RealEstateResource($realEstate), StatusCodeEnum::STATUS_OK, "Real Estate Updated Successfully!");
    }

    /**
     * Remove the specified Real Estate.
     */
    public function destroy(RealEstate $realEstate)
    {
        try {
           $realEstate = $this->realEstateService->delete($realEstate);
           return $this->apiResponse($realEstate, StatusCodeEnum::STATUS_OK, "Real Estate Deleted Successfully!");
        }catch (\Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, $exception->getMessage());
        }
    }

    /**
     * Upload The Real Estate Images
     */
    public function uploadImages(UploadRealEstateImages $request, RealEstate $realEstate)
    {
        try {
            foreach ($request->file('images') as $file) {
                $this->realEstateService->createMedia($realEstate, $file);
            }
            return $this->apiResponse(new RealEstateDetailsResource($realEstate));
        }catch (ServerException $exception){
            return $this->apiResponse(null, $exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * Upload The Real Estate Documents
     */
    public function uploadDocuments(UploadRealEstateDocuments $request, RealEstate $realEstate){
        try {
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $this->realEstateService->createMedia($realEstate, $file, MediaCollectionType::REAL_ESTATE_DOCUMENT);
                }
            }
            return $this->apiResponse(new RealEstateDetailsResource($realEstate));
        }catch (ServerException $exception){
            return $this->apiResponse(null, $exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * Delete Real Estate Document
     * @param RealEstate $realEstate
     * @param Media $media
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function deleteMedia(RealEstate $realEstate, Media $media){
        try {
            $deletedMedia = $this->realEstateService->deleteMedia($realEstate, $media);
            return $this->apiResponse(new MediaResource($deletedMedia), StatusCodeEnum::STATUS_OK, "Real Estate Image Deleted Successfully!");
        }catch (\Exception $exception){
            return $this->apiResponse(null, $exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * Change Real Estate Status
     * @param RealEstate $realEstate
     * @param UpdateRealEstateStatusRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function changeStatus(RealEstate $realEstate, UpdateRealEstateStatusRequest $request)
    {
        DB::beginTransaction();
        try {
            switch ($request->status) {
                case RealEstateStatus::INACTIVE:
                    if ($realEstate->status === RealEstateStatus::ACTIVE) {
                        // Optional: Add logic for refunding the funds if needed
                        $this->fundService->refundFundsToCustomers($realEstate);
                        $realEstate->status = RealEstateStatus::INACTIVE;
                    }
                    break;

                case RealEstateStatus::SOLD:
                    if ($realEstate->status === RealEstateStatus::ACTIVE) {
                        // Optional: Add logic for transferring funds to the seller
                        $this->fundService->transferFundsToSeller($realEstate);
                        $realEstate->status = RealEstateStatus::SOLD;
                    }
                    break;

                default:
                    throw new \Exception("Unsupported status change.");
            }
            $realEstate->save();
            DB::commit();

            return $this->apiResponse(new RealEstateDetailsResource($realEstate),StatusCodeEnum::STATUS_OK,"Real Estate Status Changed Successfully!");

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->apiResponse(null,StatusCodeEnum::STATUS_BAD_REQUEST,$exception->getMessage());
        }
    }

}
