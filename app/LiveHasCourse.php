<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveHasCourse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'live_id', 'course_id',
    ];

    public function index()
    {
        return $this->join('courses', 'courses.id','=','live_has_courses.course_id')->get();
    }

    public function getCoursesByLive($liveID)
    {
        return $this->where('live_has_courses.live_id', '=', $liveID)->join('courses', 'courses.id', '=', 'live_has_courses.course_id')->get();
    }
}
