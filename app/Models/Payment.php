<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ["user_id" , "amount" ,
        "currency_id" , "payment_method" , "status",
        "imageable_id" , "imageable_type"];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
