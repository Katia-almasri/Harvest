<?php

namespace App\Http\Controllers\Customer\RealEstate;

use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\General\ApiController;
use App\Http\Resources\Investment\InvestmentResource;
use App\Models\BusinessLogic\Investment;
use Illuminate\Http\Request;

class InvestmentController extends ApiController
{
    public function __construct(){}

    public function index(Request $request){

        $investments = Investment::query();

        if($request->is_minted){
            $is_minted = $request->is_minted=='true' ? 1:0;
            $investments = $investments->where('is_minted', $is_minted);
        }

        if($request->search){
            // real estate unique number
            $investments = $investments->whereHas('realEstate', function($query) use($request){
                $query->where('unique_number', 'like', '%'. $request->search . '%');
            });
        }

        if($request->order){
            $investments = $investments->orderBy('created_at', $request->order)->paginate($request->per_page ?? env('PAGINATE'));
        }
        else if (!$request->order)
            $investments = $investments->orderByDesc('created_at')->paginate($request->per_page ?? env('PAGINATE'));

        $paginate_info = $this->formatPaginateData($investments);
        return $this->apiResponse(InvestmentResource::collection($investments), StatusCodeEnum::STATUS_OK, null , $paginate_info);
    }

    public function show(Investment $investment){
        return $this->apiResponse(new InvestmentResource($investment));
    }

}
