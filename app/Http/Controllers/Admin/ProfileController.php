<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Media\MediaCollectionType;
use App\Enums\Media\MediaType;
use App\Enums\General\StatusCodeEnum;
use App\Helpers\MediaHelper;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Admin\UpdateRequest;
use App\Http\Requests\General\Media\StoreImageRequest;
use App\Http\Requests\General\Profile\ResetEmailRequest;
use App\Http\Requests\General\Profile\UpdateEmailRequest;
use App\Http\Requests\General\Profile\VerifyEmailRequest;
use App\Http\Resources\General\MediaResource;
use App\Http\Resources\General\UserResource;
use App\Mail\OTPMail;
use App\Services\General\OTPService;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfileController extends ApiController
{

    public function __construct(private UserService $userService, private OTPService $otpService)
    {
    }
    /**
     * Admin Profile
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function show(){
        $admin = auth()->user();
        return $this->apiResponse(new UserResource($admin), StatusCodeEnum::STATUS_OK, __('admin profile'));
    }

    /**
     * Update Admin Profile
     * @param UpdateRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function update(UpdateRequest $request){
        $admin = $this->userService->update(auth()->user(), ['name'=>$request->name]);
        return $this->apiResponse(new UserResource($admin), StatusCodeEnum::STATUS_OK, __('messages.successfully_updated'));
    }

    /**
     * Upload Image
     * @param StoreImageRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function uploadImage(StoreImageRequest $request){
        $admin = auth()->user();
        MediaHelper::deleteMedia($admin, MediaCollectionType::PROFILE_IMAGE);
        $image = MediaHelper::addMediaFromRequest($admin, MediaType::IMAGE, MediaCollectionType::PROFILE_IMAGE);
        //2. add the new image
        return $this->apiResponse(new MediaResource($image), StatusCodeEnum::STATUS_OK, __('messages.successfully_image_updated'));

    }

    /**
     * Update Email
     * @param UpdateEmailRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function updateEmail(UpdateEmailRequest $request){
        $admin = auth()->user();

        $otp = $this->otpService->generate();
        $this->otpService->store($otp, $admin->id);

        Mail::to($request->email)->queue(new OtpMail($otp));
        return $this->apiResponse($otp, StatusCodeEnum::STATUS_OK, 'We have sent you an OTP to the new email, Please verify it!');
    }

    /**
     * Verify Email
     * @param VerifyEmailRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function verifyEmail(VerifyEmailRequest $request){
        $admin = auth()->user();
        $otpRecord = $this->otpService->findByUserId($admin->id);
        if(!Hash::check($request->otp, $otpRecord->otp)){
            throw new NotFoundHttpException();
        }
        // check the expiration
        if($otpRecord->expiration_date < now())
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, 'OTP expired!');
        return $this->apiResponse(null, StatusCodeEnum::STATUS_OK, 'OTP verified successfully');
    }

    /**
     * Reset Email
     * @param ResetEmailRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function resetEmail(ResetEmailRequest $request){
        $admin = auth()->user();
        $this->userService->update($admin, ['email'=> $request->email]);
        return $this->apiResponse(new UserResource($admin), StatusCodeEnum::STATUS_OK, 'Email changed!');
    }

}
