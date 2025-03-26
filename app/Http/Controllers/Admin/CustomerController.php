<?php

namespace App\Http\Controllers\Admin;


use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Resources\Customer\CustomerResource;
use App\Services\Customer\CustomerService;

class CustomerController extends ApiController
{
    public function __construct(private customerService $customerService){}
    public function index(){
        $customers = $this->customerService->index()->paginate($request->per_page ?? env('PAGINATE'));
        $paginate_info = $this->formatPaginateData($customers);
        return $this->apiResponse(CustomerResource::collection($customers), StatusCodeEnum::STATUS_OK, $paginate_info, "Customers retrieved successfully");
    }
}
