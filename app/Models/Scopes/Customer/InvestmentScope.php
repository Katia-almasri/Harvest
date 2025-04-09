<?php

namespace App\Models\Scopes\Customer;

use App\Enums\General\RoleType;
use App\Models\Customer\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class InvestmentScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth("sanctum")->user();
        if($user != null){

            if($user->hasRole(RoleType::CUSTOMER)){
                $customer = Customer::where('user_id', auth()->user()->id)->first();
                  $builder->where("customer_id" , $customer->id);
            }
        }
    }
}
