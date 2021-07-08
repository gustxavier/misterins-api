<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserForgotPassword extends Model
{
    protected $fillable = [
        'user_id', 'status', 'hash'
    ];

    public function insertRequest($fields)
    {   
        return parent::create([
            'user_id' => $fields['user_id'],
            'hash' => $fields['hash'],
        ]);
    }

    public function findByUserID($userID){
        return $this->where('user_id' ,'=', $userID)
            ->where('status', '=', 'waiting')
            ->first();
    }

    public function findByHash($hash){
        return $this->where('hash' ,'=', $hash)
            ->first();
    }
}
