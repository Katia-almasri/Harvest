<?php

namespace App\Services\Spv;
use App\Models\BusinessLogic\SPV;
use App\Models\RealEstate\RealEstate;
use Exception;
use Illuminate\Support\Facades\DB;

class SpvService{
    public function __construct(private MediaService $mediaService){}

    public function store($data){
        $realEstate = RealEstate::find($data['real_estate_id']);
        if($this->checkHasRealEstateSpv($realEstate))
            throw new Exception(__("messages.real_estate_already_has_spv"));

        DB::beginTransaction();
        $spv =  new SPV();
        $spv->name = $data['name'];
        $spv->registration_number = $data['registration_number'];
        $spv->real_estate_id = $data['real_estate_id'];
        $spv->save();
        DB::commit();
        return $spv;
    }

    /**
     * This service is to check if the input real estate already has spv
     * @param RealEstate $realEstate
     * @return boolean
     */
    public function checkHasRealEstateSpv($realEstate){
        if($realEstate->spv_id != null){
            return true;
        }
        return false;
    }

    public function delete(SPV $spv){
        $this->mediaService->delete($spv);
        $spv->delete();
        return $spv;
    }
}
