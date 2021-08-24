<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Launch extends Model
{
    protected $fillable = ['name', 'course_id', 'mes', 'year','value','capture_start','cart_open','cart_reopen','goal','budget'];

    public function index()
    {
        return $this->get();
    }

    public function storeLaunch($fields)
    {
        return $this->create($fields);
    }
}
