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

    public function show($id){
        $show = $this->find($id);
 
        if (!$show) {
            throw new \Exception('Nada Encontrado', -404);
        }

        return $show;
    }
}
