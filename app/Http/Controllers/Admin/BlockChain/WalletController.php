<?php

namespace App\Http\Controllers\Admin\BlockChain;

use App\Enums\General\StatusCodeEnum;
use App\Enums\Wallet\WalletableType;
use App\Helpers\KeyHelper;
use App\Http\Controllers\General\ApiController;
use App\Http\Resources\Wallet\WalletResource;
use App\Models\BusinessLogic\SPV;
use App\Models\Customer\Wallet;
use App\Services\BlockChainInteraction\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;


class WalletController extends ApiController
{

    public function __construct(private readonly WalletService $walletService)
    {
    }

    /**
     * Display a listing of admin Wallets they have (Admin wallets only).
     */
    public function index(Request $request)
    {
        $adminWallets = Wallet::forAdmins();

        if($request->search){
            $adminWallets = $adminWallets->where('wallet_address', 'like', '%'.$request->search.'%');
        }
        if($request->newest){
            $adminWallets = $adminWallets->orderBy('created_at','DESC');
        }

        $adminWallets = $adminWallets->where('walletable_id', auth()->user()->id);

        if($request->order){
            $adminWallets = $adminWallets->orderBy('created_at', $request->order)->paginate($request->per_page ?? env('PAGINATE'));
        }
        else if (!$request->order)
            $adminWallets = $adminWallets->orderByDesc('created_at')->paginate($request->per_page ?? env('PAGINATE'));

        $paginate_info = $this->formatPaginateData($adminWallets);
        return $this->apiResponse(WalletResource::collection($adminWallets), StatusCodeEnum::STATUS_OK, null , $paginate_info);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SPV $spv)
    {
        try {
            DB::beginTransaction();
            $keys = KeyHelper::generateWalletKeys();
            //save this info in the database
            $spvWallet = $this->walletService->store(['private_key'=> Crypt::encryptString($keys['private_key']),
                'wallet_address' => $keys['public_address'],], $spv);
            DB::commit();
            return $this->apiResponse(new WalletResource($spvWallet), StatusCodeEnum::STATUS_OK, __('messages.success'));
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->apiResponse(null, StatusCodeEnum::INTERNAL_SERVER_ERROR, __($exception->getMessage()));
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
