<?php

namespace App\Http\Resources\Customer;

use App\Http\Resources\General\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'father_name'=>$this->father_name,
            'user'=>[
                'email'=>$this->user->email,
                'name'=> $this->user->name,
                'image'=> new MediaResource($this->user->image()),
            ],
            'phone_number'=>$this->phone_number??null,
            'family_status'=>$this->family_status??null,
            'gender'=>$this->gender??null,
            'longitude'=>$this->longitude??null,
            'latitude'=>$this->latitude??null,
            'employment_status'=>$this->when($this->checkCustomerCompleteProfile(), $this->employment_status, null),
            'industry'=>$this->when($this->checkCustomerCompleteProfile(), $this->industry, null),
            'main_source_of_fund'=>$this->when($this->checkCustomerCompleteProfile(), $this->main_source_of_fund, null),
            'minimum_investment_amount'=>$this->when($this->checkCustomerCompleteProfile(), $this->minimum_investment_amount, null),
            'is_complete_profile' =>$this->checkCustomerCompleteProfile(),
            'can_invest' =>$this->canInvest(),
            'passport_image'=>$this->when($this->checkCustomerCompleteProfile(), new MediaResource($this->passportImage()), null),
            'residential_card'=>$this->when($this->checkCustomerCompleteProfile(), new MediaResource($this->residentialCardImage()), null),
        ];
    }

    public function checkCustomerCompleteProfile():bool{
        return $this->employment_status!=null?true:false;
    }

    public function canInvest(){
        return $this->passportImage()  && $this->residentialCardImage()?true:false;

    }
}
