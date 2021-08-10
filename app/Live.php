<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Live extends Model
{
    protected $fillable = ['title', 'url', 'description', 'thumbnail', 'date', 'hour', 'is_active'];

    public function index()
    {
        return $this->select('*', DB::raw('DATE_FORMAT (date, "%d-%m-%Y") as date_formated'))->get();
    }

    public function insert($fields)
    {
        return $this->create($fields);
    }

    public function show($id)
    {
        // $show = $this->select('*', DB::raw("DATE_FORMAT (lives.date, '%d-%m-%Y') as date_formated"))->find($id);
        $show = $this->find($id);
 
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
