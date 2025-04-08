<?php
namespace App\Services\Payment;


use App\Enums\General\Payment\Payable;
use App\Models\Payment;
use App\Models\RealEstate\RealEstate;

class PaymentService{
    public function index(){}

    public function create(array $data){
        $payment = new Payment($data);
        switch ($data['payable_type']) {
            case Payable::REALESTATE:
                $realEstate = RealEstate::find($data['payable_id']);
                $realEstate->payments()->save($payment);
                break;
        }

        return $payment->save();
    }

    public function show(){}
    public function update(){}
    public function delete(){}

    public function calculateTokensPrice($tokens, RealEstate $realEstate){
        // know the token price
        $tokenPrice = (int) $realEstate->purchase_price / $realEstate->total_shares;
        return ['total_price'=>$tokens * $tokenPrice, 'token_price'=>$tokenPrice];
    }
}
