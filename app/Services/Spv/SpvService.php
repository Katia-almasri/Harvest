<?php

namespace App\Services\Spv;
use App\Models\BusinessLogic\SPV;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpvService{

    public function store($data){
        try {
            DB::beginTransaction();
            $spv =  new SPV();
            $spv->name = $data['name'];
            $spv->registration_number = $data['registration_number'];
            $spv->real_estate_id = $data['real_estate_id'];
            $spv->save();
            DB::commit();
            return $spv;
        }catch (Exception $e){
            DB::rollBack();
            Log::info($e->getMessage());
            return null;
        }
    }
}
