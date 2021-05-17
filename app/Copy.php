<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Copy extends Model
{
    protected $fillable = ['title','important_text'];

    public function index(){
        return  $this->get();
    }

    public function storeCopy($fields)
    {
        return $this->create($fields); 
    }

    public function show($id){
        $show = $this->find($id);
 
        if (!$show) {
            throw new \Exception('Nada Encontrado', -404);
        }

        return $show;
    }

    public function getCopy($id){
        $copy = $this->where('live_id', '=', $id)->get();

        return $copy;
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
        $copy->delete();
        return $copy->delete();
    }

    public function copy()
    {
        return $this->belongsTo('App\Copy', 'id');
    }
    
}
