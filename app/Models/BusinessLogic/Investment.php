<?php

namespace App\Models\BusinessLogic;

use App\Models\Customer\Customer;
use App\Models\RealEstate\RealEstate;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }

    public function realEstate(){
        return $this->hasMany(RealEstate::class);
    }
}
