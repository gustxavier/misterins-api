<?php

namespace App\Http\Controllers;

use App\Course;
use App\LiveHasCourse;
use App\Services\ResponseService;
use App\Transformers\Course\CourseResource;
use App\Transformers\Course\CourseResourceCollection;
use App\UserHasCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    private $course;

    public function __construct(Course $course)
    {
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
        try {
            $data = $this
                ->course
                ->updateCourse($request->all(), $id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('courses.update', $id, $e);
        }

        return new CourseResource($data, array('type' => 'update', 'route' => 'courses.update'));
    }

    public function show($id)
    {
        try {
            $data = $this
                ->course
                ->show($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('courses.show', $id, $e);
        }

        return new CourseResource($data, array('type' => 'show', 'route' => 'courses.show'));
    }

    public function getCoursesByUser($userID)
    {

        $userHasCourse = new UserHasCourse;

        $data = array(
            'status' => true,
            'data' => array(
                'all' => $this->course->index()->toArray(),
                'checked' => $userHasCourse->getCoursesByUser($userID)->toArray(),
            ),
            'permission' => Auth::user()->permission,
            'msg' => 'Listando dados',
            'url' => route('courses.getCoursesByUser', $userID),
        );
        return $data;
    }

    public function getCoursesByLive($liveID)
    {
        $liveHasCourse = new LiveHasCourse;

        $data = array(
            'status' => true,
            'data' => array(
                'all' => $this->course->index()->toArray(),
                'checked' => $liveHasCourse->getCoursesByLive($liveID)->toArray(),
            ),
            'permission' => Auth::user()->permission,
            'msg' => 'Listando dados',
            'url' => route('courses.getCoursesByLive', $liveID),
        );
        return $data;
    }
}
