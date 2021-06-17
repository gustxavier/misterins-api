<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'hotmart_id'];

    public function index(){
        return  $this->get();
    }

    public function updateCourse($fields, $id)
    {
        $course = $this->show($id);

        $course->update($fields);
        return $course;
    }
}
