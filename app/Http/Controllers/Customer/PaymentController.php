<?php

namespace App\Http\Controllers\Customer;


use App\Enums\General\InvestmentStatus;
use App\Enums\General\StatusCodeEnum;
use App\Enums\Payment\Payable;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Payment\PaymentStatus;
use App\General\Factories\PaymentFactory;
use App\Http\Controllers\General\ApiController;
use App\Http\Requests\Payment\PaymentRequest;
use App\Jobs\ProcessSuccessfulInvestment;
use App\Models\Customer\Customer;
use App\Models\RealEstate\RealEstate;
use App\Services\Investment\InvestmentService;
use App\Services\Payment\CurrencyService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class PaymentController extends ApiController
{
    public function __construct(private readonly PaymentService $paymentService,
                                private readonly InvestmentService $investmentService,
                                private readonly CurrencyService $currencyService){}
    public function pay(PaymentRequest $request, RealEstate $realEstate){
        try {
            DB::beginTransaction();
            //1. calculate the total amount
            $prices = $this->paymentService->calculateTokensPrice($request->tokens, $realEstate);
            $totalPrice = $prices['total_price'];
            $tokenPrice = $prices['token_price'];
            //2. store the order in DB (as investment)
            $investmentData = [
                'real_estate_id'=>$realEstate->id,
                'customer_id'=> Customer::where('user_id', auth()->id())->first()->id,
                'total_tokens'=> $request->tokens,
                'total_price'=> $totalPrice,
                'token_price'=> $tokenPrice,
            ];
            $investment = $this->investmentService->create($investmentData);

            //3. process the payment
            $paymentMethod = $request->payment_method;
            $totalPrice = $this->currencyService->currencyConvertorByPaymentMethod($request->payment_method, $totalPrice, $request->currency);
            $data = [
                'amount' => $totalPrice,
                'currency' => $request->currency,
                'payment_method_type' => [$request->payment_method_type],
                'confirmation_method' => 'manual',
                'confirm' => true,
                'metadata' => [
                    'investment_id' => $investment->id,
                    'user_id' => auth()->id(),
                    'real_estate_id'=> $realEstate->id,
                ]
            ];

            $payment = null;

            $paymentService = PaymentFactory::create($paymentMethod);
            if($paymentMethod == PaymentMethod::STRIPE){
                // call the stripe service
                $payment = $paymentService->processPayment($data);
            }
            // another payment methods...

            //4. create the payment record in the DB to save it
            $data = [
                'amount' => $totalPrice,
                'user_id' => auth()->id(),
                'currency'=> $request->currency,
                'payment_method' => $paymentMethod,
                'status'=> PaymentStatus::PENDING,
                'payable_type'=> Payable::REALESTATE,
                'payment_intent_id' => $payment->id,
                'payable_id' => $realEstate->id
            ];

            // store a copy the payment in the central DB
            $localPayment = $this->paymentService->create($data);
            //4.1  link the investment with payment
            $investment = $this->investmentService->update(['payment_id'=>$localPayment->id], $investment);
            //4.2 link the payment with the payment intent
            $this->paymentService->update(['payment_intent_id', $payment->id], $localPayment);

            //5. finishing up the paymentIntent and end it back to the front
            if ($payment->status === PaymentStatus::SUCCEEDED) {
                // ✅ Update investment
                $this->investmentService->update(['status' => InvestmentStatus::PENDING_PAYMENT], $investment);
                // Update the RealEstate sharesSold
            }

            DB::commit();
            return $this->apiResponse([
                'clientSecret' => $payment->client_secret,
                'payment_intent'=> $payment->id,
                'investment_id' => $investment->id
                ], StatusCodeEnum::STATUS_OK, __('messages.success'));

        }catch (\Exception $e){
            DB::rollBack();
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($e->getMessage()));
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $secret);
            $paymentIntent = $event->data->object;
            $realEstateId = $paymentIntent->metadata->real_estate_id;
            // handle successful payment
            if ($event->type === PaymentStatus::PAYMENT_INTENT_SUCCEEDED) {
                $paymentIntent = $event->data->object;
                // the rest of logic here using the job
                ProcessSuccessfulInvestment::dispatch(
                    $paymentIntent->id,
                    $realEstateId
                );
            }

            return response()->json(['status' => 'success']);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }

}
