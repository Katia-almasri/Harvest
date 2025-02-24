<?php

namespace App\Http\Controllers\Customer;

use App\Enums\General\RoleType;
use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Customer\RegisterCustomerAccountRequest;
use App\Mail\OTPMail;
use App\Services\General\OTPService;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;

class AuthController extends ApiController
{
    public function __construct(private OTPService $otpService, private UserService $userService){}

    public function register(RegisterCustomerAccountRequest $request){
        try {

            $data = $request->validated();
            $user = $this->userService->store($data);
            $user->assignRole(RoleType::CUSTOMER);

            $otp = $this->otpService->generate();
            $this->otpService->store($otp, $user->id);

            Mail::to($user->email)->queue(new OtpMail($otp));

            return $this->apiResponse(['otp'=>$otp], StatusCodeEnum::STATUS_OK, "User Created Successfully!, Please verify OTP");
        }catch (\Exception $exception){
            return $this->apiResponse(null,StatusCodeEnum::STATUS_BAD_REQUEST,$exception->getMessage());
        }
    }
}
