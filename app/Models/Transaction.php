<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'tx_hash', 'from_address', 'to_address',
        'nonce', 'gas_limit', 'gas_price',
        'status', 'retries', 'payload', 'sent_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];
}
