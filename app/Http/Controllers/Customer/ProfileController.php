<?php

namespace App\Http\Controllers\Customer;

use App\Enums\General\StatusCodeEnum;
use App\Enums\Media\MediaCollectionType;
use App\Enums\Media\MediaType;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Customer\CompleteCustomerProfileRequest;
use App\Http\Requests\Customer\RegisterCustomerAccountRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Requests\General\Media\StoreImageRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\General\MediaResource;
use App\Models\Customer\Customer;
use App\Services\Customer\CustomerService;
use App\Services\Customer\MediaService;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProfileController extends ApiController
{
    public function __construct(private CustomerService $customerService, private MediaService $mediaService){}
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
    public function store(RegisterCustomerAccountRequest $request)
    {
        try {
            $data = $request->validated();
            if(auth()->user()->email_verified_at == null)
                return $this->apiResponse(null, StatusCodeEnum::STATUS_FORBIDDEN, "Please Verify You Email First!");
            $customer = $this->customerService->store($data);
            return $this->apiResponse(new CustomerResource($customer), StatusCodeEnum::STATUS_OK, "Customer Info Added Successfully!");
        }catch (\Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, $exception->getMessage());
        }
    }

    /**
     * Complete Profile
     * @param CompleteCustomerProfileRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function completeProfile(CompleteCustomerProfileRequest $request){
        try {
            $data = $request->validated();
            $customer = $this->customerService->showByUser(auth()->user());
            $customer = $this->customerService->update($customer, $data);
            return $this->apiResponse(new CustomerResource($customer), StatusCodeEnum::STATUS_OK, "Customer Profile Completed Successfully!");
        }catch (\Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, $exception->getMessage() );
        }
    }

    /**
     * Upload Image
     * @queryParam media_collection_type
     * @param StoreImageRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function uploadImage(StoreImageRequest $request){
        try {
            $customer = $this->customerService->showByUser(auth()->user());
            if(isset($request->media_collection_type)){
                if($request->media_collection_type == MediaCollectionType::RESIDENTIAL_CARD){
                    $image = $this->mediaService->createFromRequest($customer, MediaType::IMAGE, MediaCollectionType::RESIDENTIAL_CARD);
                    return $this->apiResponse(new MediaResource($image), StatusCodeEnum::STATUS_OK, "Residential Card Image Added Successfully!");
                }
            }
            $image = $this->mediaService->createFromRequest($customer);
            return $this->apiResponse(new MediaResource($image), StatusCodeEnum::STATUS_OK, "Your Passport Image Added Successfully!");
        }catch (\Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, $exception->getMessage());
        }
    }

    public function deleteImage(Request $request, Media $media){
        try {
            $customer = $this->customerService->showByUser(auth()->user());
            $image = $this->mediaService->delete($customer, $media);
            return $this->apiResponse(new MediaResource($image), StatusCodeEnum::STATUS_OK, "Residential Card Image Added Successfully!");
        }catch (\Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        try {
            $customer = $this->customerService->showByUser(auth()->user());
            return $this->apiResponse(new CustomerResource($customer), StatusCodeEnum::STATUS_OK, "Customer Info Fetched Successfully!");

        }catch (\Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_NOT_FOUND, "Customer Not Found!");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request)
    {
        try {
            $customer = $this->customerService->showByUser(auth()->user());
            $customer = $this->customerService->update($customer, $request->validated());
            return $this->apiResponse(new CustomerResource($customer), StatusCodeEnum::STATUS_OK, "Customer Info Updated Successfully!");
        }catch (\Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
