<?php
namespace App\Services\Investment;
use App\Models\BusinessLogic\Investment;

class InvestmentService{
    public function index(){}

    public function create(array $data){
        $investment = new Investment();
        $investment->fill($data);
        $investment->save();
        return $investment;
    }

    public function update(array $data, Investment $investment){
        $investment->update($data);
        return $investment;
    }

    public function delete(){}

}
