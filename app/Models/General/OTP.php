<?php

namespace App\Models\General;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    protected $table = 'o_t_p_s';
    protected $guarded = [
        'id'
    ];

    ########################### Relations ###########################
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
