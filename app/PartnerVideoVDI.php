<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerVideoVDI extends Model
{
    protected $fillable = ['title','path'];

    public function index(){
        return  $this->get();
    }

    public function store($fields)
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

    public function updatePartnerVideoVDI($fields, $id)
    {
        $partnerVideoVDI = $this->show($id);

        $partnerVideoVDI->update($fields);
        return $partnerVideoVDI;
    }

    public function destroyPartnerVideoVDI($id)
    {
        $partnerVideoVDI = $this->show($id);
        $partnerVideoVDI->delete();
        return $partnerVideoVDI->delete();
    }

    public function partnerVideoVDI()
    {
        return $this->belongsTo('App\PartnerVideoVDI', 'id');
    }
}
