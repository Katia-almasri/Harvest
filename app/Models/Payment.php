<?php

namespace App\Models;

use App\Enums\Payment\Payable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function payable(): BelongsTo
    {
        return $this->morphTo();
    }

    ####################### custom functions ###############
    public static function realEstates(){
        return self::where('payable_type', Payable::REALESTATE);
    }
}
