<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerVideo extends Model
{
    protected $fillable = ['title','path', 'type','file_name', 'file_extension', 'course_id'];

    public function index(){
        return  $this->get();
    }

    public function getVideos($courseID, $type){
        return $this->where('type', $type)->where('course_id', $courseID)->orderBy('created_at', 'DESC')->get();
    }

    public function getVideoByCourseID($couseID){
        return $this->where('course_id', $couseID)->orderBy('created_at', 'DESC')->get();
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

    public function updatePartnerVideo($fields, $id)
    {
        $partnerVideo = $this->show($id);

        $partnerVideo->update($fields);
        return $partnerVideo;
    }

    public function destroyPartnerVideo($id)
    {
        $partnerVideo = $this->show($id);
        $partnerVideo->delete();
        return $partnerVideo->delete();
    }
}
