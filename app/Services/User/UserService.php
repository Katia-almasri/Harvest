<?php
namespace App\Services\User;
use App\Models\User;

class UserService{

    public function store($data){
        return User::create($data);
    }
    public function getByEmail($email){
        return User::where('email', $email)->first();
    }

    public function find($userId){
        return User::find($userId);
    }

    public function update($user, $data){
        $user->update($data);
        return $user->fresh();
    }

}
