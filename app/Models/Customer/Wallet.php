<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'customer_wallets';


    public function walletable() {
        return $this->morphTo();
    }
}
