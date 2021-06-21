<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHasCourse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'course_id'
    ];

    public function getCoursesByUser($userID)
    {
        return $this->where('user_id', '=', $userID)->leftJoin('courses', 'courses.id', '=','user_has_courses.course_id')->get();
    }
}
