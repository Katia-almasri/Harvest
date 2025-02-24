<?php
namespace App\Services\General;
use App\Models\General\OTP;
use Illuminate\Support\Facades\Hash;

class OTPService{
    public function generate($length = 6) {
        $characters = '0123456789';
        return substr(str_shuffle($characters), 0, $length);
    }
    public function store($otp, $userId) {
        OTP::updateOrCreate(
            ['user_id' => $userId],
            [
                'user_id' => $userId,
                'otp' => Hash::make($otp),
                'expiration_date' => now()->addMinutes(5)
            ]
        );
    }


    public function findByUserId($userId) {
        return OTP::where('user_id', $userId)->first();
    }
}
