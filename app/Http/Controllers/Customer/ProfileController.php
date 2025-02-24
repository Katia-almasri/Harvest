<?php

namespace App\Http\Controllers\Customer;

use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Customer\RegisterCustomerAccountRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Models\Customer;
use App\Services\Customer\CustomerService;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    public function __construct(private CustomerService $customerService){}
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
            $customer = $this->customerService->store($data);
            return $this->apiResponse(new CustomerResource($customer), StatusCodeEnum::STATUS_OK, "Customer Info Added Successfully!");
        }catch (\Exception $exception){
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
