<?php
namespace App\Services\Customer;
use App\Models\Customer;

class CustomerService{
    public function index(){}
    public function store($data){
        $customer = new Customer();
        $customer->fill($data);
        $customer->user_id = auth()->user()->id;
        $customer->save();
        return $customer;
    }
    public function show(Customer $customer){
        return $customer;
    }

    public function showByUser($user){
        return Customer::where('user_id',$user->id)->first();
        // TODO convert it to query builder
    }
    public function update($customer, $data){
        $customer->update($data);
        return $customer->fresh();
    }

    //TODO try to change the type to generic for all services
    public function delete(){}
}
