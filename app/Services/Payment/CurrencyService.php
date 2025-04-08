<?php
namespace App\Services\Payment;
use App\Enums\General\CurrencyType;
use App\Enums\General\Payment\PaymentMethod;
use App\Models\Currency;

class CurrencyService{
    /**
     * This service converts the given price in a given currency to the convenient one for the give payment method
     * i.e: for stripe in USD, the price should be in cents, so * 100 and so on
     * @param $paymentMethod
     * @param $price
     * @param Currency $currency
     * @return mixed
     */
    public function currencyConvertorByPaymentMethod($paymentMethod, $price, $currency){
        switch ($paymentMethod) {
            case PaymentMethod::STRIPE:
                if($currency == CurrencyType::USD){
                    $price = $price * 100;
                }
                return $price;
            default:
                return $price;
        }

    }
}
