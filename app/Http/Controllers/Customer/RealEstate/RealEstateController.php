<?php

namespace App\Http\Controllers\Customer\RealEstate;

use App\Enums\General\StatusCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\ApiController;
use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\RealEstate\RealEstateResource;
use App\Models\RealEstate\RealEstate;
use Illuminate\Http\Request;

class RealEstateController extends ApiController
{
    public function getNearestRealEstates(){
        $customer = auth()->user();
            if($customer->longitude != null && $customer->latitude != null){
                $radius = 10;
                $latitude = $customer->latitude;
                $longitude = $customer->longitude;
                $realEstates = RealEstate::selectRaw("
            *,
            ( 6371 * acos( cos( radians(?) ) *
                       cos( radians( latitude ) )
                       * cos( radians( real_estates.longitude ) - radians(?)) + sin( radians(?) ) *sin( radians( latitude ) ) )
                     )  AS distance
        " , [$latitude , $longitude ,$latitude ])
                    ->having('distance', '<=', $radius)
                    ->orderBy('distance')
                    ->get();
                return $this->apiResponse(RealEstateResource::collection($realEstates), StatusCodeEnum::STATUS_OK, "");
            }
            else
                return $this->apiResponse([], StatusCodeEnum::STATUS_NOT_FOUND, __("messages.no_near_real_estates"));
    }

    public function index(Request $request){
        $realEstates = RealEstate::query();
        if($request->newest){
            $realEstates = $realEstates->orderBy('created_at','DESC');
        }
        if($request->status){
            $realEstates = $realEstates->where('status', $request->status);
        }
        if($request->search){
            $realEstates = $realEstates->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%')
                ->orWhere('category', 'like', '%'.$request->search.'%')
                ->orWhereHas('city', function($query) use($request){
                    $query->where('name', 'like', '%',$request->search.'%');
                });
        }

        $realEstates = $realEstates->orderByDesc('created_at')->paginate($request->per_page ?? env('PAGINATE'));
        $paginate_info = $this->formatPaginateData($realEstates);
        return $this->apiResponse(RealEstateResource::collection($realEstates), StatusCodeEnum::STATUS_OK, null , $paginate_info);
    }

}
