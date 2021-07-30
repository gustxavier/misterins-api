<?php

namespace App\Http\Controllers;

use App\LiveHasCourse;
use App\Transformers\LiveHasCourse\LiveHasCourseResourceCollection;
use Illuminate\Http\Request;

class LiveHasCourseController extends Controller
{
    private $live;

    public function __construct(LiveHasCourse $live)
    {
        $this->live = $live;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new LiveHasCourseResourceCollection($this->live->index());
    }
}
