<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaunchCPL extends Model
{
    protected $fillable = ['course_id', 'name', 'date', 'description'];

    public function index()
    {
        return $this->get();
    }
}
