<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaunchPhones extends Model
{
    protected $fillable = ['course_id', 'contact_phone_number'];

    public function index()
    {
        return $this->get();
    }
}
