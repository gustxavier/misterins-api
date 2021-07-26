<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Copy extends Model
{
    protected $fillable = ['title', 'important_text', 'course_id', 'description'];

    public function index()
    {
        return $this->get();
    }

    public function storeCopy($fields)
    {
        return $this->create($fields);
    }

    public function show($id)
    {
        $show = $this->find($id);

        if (!$show) {
            throw new \Exception('Nada Encontrado', -404);
        }

        return $show;
    }

    public function getCopyByCourseID($id)
    {
        return $this->select('copies.*', DB::raw('courses.title as course_title'))->where('course_id', '=', $id)->join('courses', 'courses.id', '=', 'copies.course_id')->get();
    }

    public function getCopy($id)
    {
        return $this->where('live_id', '=', $id)->get();
    }

    public function updateCopy($fields, $id)
    {
        $copy = $this->show($id);

        $copy->update($fields);
        return $copy;
    }

    public function destroyCopy($id)
    {
        $copy = $this->show($id);
        return $copy->delete();
    }

    public function copy()
    {
        return $this->belongsTo('App\Copy', 'id');
    }

}
