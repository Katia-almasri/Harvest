<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerWallet extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'customer_wallets';

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
