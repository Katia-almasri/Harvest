<?php
namespace App\Services\Customer;
use App\Models\Customer;

class CustomerService{
    public function index(){}
    public function store($data){
        return Customer::create($data);
    }
    public function show(){}
    public function update(){}
    public function delete(){}
}
