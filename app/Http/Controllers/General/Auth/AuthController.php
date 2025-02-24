<?php

namespace App\Http\Controllers\General\Auth;

use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\General\Auth\ChangePasswordRequest;
use App\Http\Requests\General\Auth\ForgetPasswordRequest;
use App\Http\Requests\General\Auth\LoginRequest;
use App\Http\Requests\General\Auth\ResetPasswordRequest;
use App\Http\Requests\General\Auth\VerifyOTPRequest;
use App\Http\Resources\General\UserResource;
use App\Mail\OTPMail;
use App\Models\User;
use App\Services\General\OTPService;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthController extends ApiController
{
    private OTPService  $otpService;
    private UserService  $userService;
    public function __construct()
    {
        $this->otpService = new OTPService();
        $this->userService = new UserService();
    }

    /**
     *login.
     * */
    function login(LoginRequest  $request)
    {
        $validateData = $request->validated();
        $user = User::query()->where('email', $validateData['email'])->first();
        if (!$user || !Hash::check($validateData['password'], $user->password)) {
            return $this->apiResponse(null, StatusCodeEnum::STATUS_NOT_FOUND, __('messages.user_not_found'));
        }

        if ($user->status == false) {
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, __('messages.user_blocked_account'));
        }

        $token = $user->createToken('user-token')->plainTextToken;
        return $this->apiResponse(['user'=>new UserResource($user),'token' => $token], StatusCodeEnum::STATUS_OK, __('messages.successfully_logged_in'));
    }

    /**
     * logout.
     * */
    public function logout(){
        auth("sanctum")->user()->tokens()->delete();
        return $this->apiResponse(null, StatusCodeEnum::STATUS_OK, __('messages.logout'));
    }

    /**
     * Forget password
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $user = $this->userService->getByEmail($request->email);

        $otp = $this->otpService->generate();
        $this->otpService->store($otp, $user->id);

        Mail::to($user->email)->queue(new OtpMail($otp));
        return $this->apiResponse($otp, StatusCodeEnum::STATUS_OK, 'OTP sent successfully');
    }

    /**
     * Verify OTP
     * @param VerifyOTPRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function verifyOTP(VerifyOTPRequest $request)
    {
        $user = $this->userService->getByEmail($request->email);
        $otpRecord = $this->otpService->findByUserId($user->id);
        if(!Hash::check($request->otp, $otpRecord->otp)){
            throw new NotFoundHttpException();
        }
        // check the expiration
        if($otpRecord->expiration_date < now())
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, 'OTP expired!');
        $user->email_verified_at = now();
        $user->save();
        return $this->apiResponse(null, StatusCodeEnum::STATUS_OK, 'OTP verified successfully');
    }

    /**
     * Reset Password
     * @param ResetPasswordRequest $request
     * @return mixed
     */
    public function resetPassword(ResetPasswordRequest $request){
        $user = $this->userService->getByEmail($request->email);
        $user = $this->userService->update($user, ['password'=> $request->get('new_password')]);
        return $this->apiResponse(new UserResource($user), StatusCodeEnum::STATUS_OK, 'Password reset successfully');
    }

    /**
     * Change Password
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest $request){
        $user = auth()->user();
        // check the old password
        if(!Hash::check($request->old_password, $user->password))
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, 'Old password is wrong!');
        if($request->old_password == $request->new_password)
            return $this->apiResponse(null, StatusCodeEnum::STATUS_BAD_REQUEST, 'Enter New Password!');

        $user = $this->userService->update($user, ['password'=> $request->get('new_password')]);
        return $this->apiResponse(new UserResource($user), StatusCodeEnum::STATUS_OK, 'Password reset successfully');

    }

}
