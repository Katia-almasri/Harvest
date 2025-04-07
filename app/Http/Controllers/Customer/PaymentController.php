<?php

namespace App\Http\Controllers\Customer;


use App\Enums\General\CurrencyType;
use App\Enums\General\Payment\PaymentMethod;
use App\Enums\General\StatusCodeEnum;
use App\General\Factories\PaymentFactory;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Payment\PaymentRequest;
use App\Models\Currency;

class PaymentController extends ApiController
{
    public function pay(PaymentRequest $request){
        $paymentMethod = $request->payment_method;
        $data = [
            'amount' => $request->amount,
            'currency' => $request->currency,
            'payment_method_type' => [$request->payment_method_type],
        ];

        $payment = null;

        try {
            $paymentService = PaymentFactory::create($paymentMethod);
            if($paymentMethod == PaymentMethod::STRIPE){
                // call the stripe service
                $payment = $paymentService->processPayment($data);
            }
            return $this->apiResponse(null, StatusCodeEnum::STATUS_OK, __('messages.success'));
        }catch (\Exception $e){
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($e->getMessage()));
        }
    }
}
