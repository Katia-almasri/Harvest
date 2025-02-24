<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /** @use HasFactory<\Database\Factories\Common\CityFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    #################### Relations #########################
    public function country(){
        return $this->belongsTo(Country::class);
    }
}
