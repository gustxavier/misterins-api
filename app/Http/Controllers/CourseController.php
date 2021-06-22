<?php

namespace App\Http\Controllers;

use App\Course;
use App\Services\ResponseService;
use App\Transformers\Course\CourseResource;
use App\Transformers\Course\CourseResourceCollection;
use App\UserHasCourse;
use Illuminate\Http\Request;


class CourseController extends Controller
{
    private $course;

    public function __construct(Course $course){
        $this->course = $course;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new CourseResourceCollection($this->course->index());
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{        
            $data = $this
            ->course
            ->updateCourse($request->all(), $id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('courses.update',$id,$e);
        }

        return new CourseResource($data,array('type' => 'update','route' => 'courses.update'));
    }

    public function getCoursesByUser($userID){

        $userHasCourse = new UserHasCourse;

        $data = array(
            'all' => $this->course->index()->toArray(),
            'checked' => $userHasCourse->getCoursesByUser($userID)->toArray()
        );
        return $data;
    }
}
