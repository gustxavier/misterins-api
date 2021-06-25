<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Live extends Model
{
    protected $fillable = ['title', 'url', 'description', 'course_id'];

    public function index()
    {
        return $this->leftjoin('courses', 'lives.course_id', '=', 'courses.id')->get();
    }

    public function insert($fields)
    {
        return $this->create($fields);
    }

    public function show($id)
    {
        $show = $this->select('lives.*','courses.hotmart_id')->leftjoin('courses', 'lives.course_id', '=', 'courses.id')
        ->find($id);
 
        if (!$show) {
            throw new \Exception('Nada Encontrado', -404);
        }

        return $show;
    }

    public function updateLive($fields, $id)
    {
        $live = $this->show($id);
        $live->update($fields);
        return $live;
    }

    public function destroyLive($id)
    {
        $lives = $this->show($id);

        $lives->delete();
        return $lives;
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'id');
    }

    public function liveComments()
    {
        return $this->hasMany('App\LiveComment', 'live_id', 'id');
    }
}
