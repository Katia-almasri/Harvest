<?php

namespace App\Models\Customer;

use App\Models\BusinessLogic\SPV;
use App\Models\User;
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

    ########################## Custom functions ############################
    public static function forAdmins()
    {
        return self::where('walletable_type', User::class);
    }

    public static function forSpvs()
    {
        return self::where('walletable_type', Spv::class);
    }

    public static function forCustomers()
    {
        return self::where('walletable_type', Customer::class);
    }

}
