<?php

namespace App\Http\Controllers;

use App\Launch;
use App\Transformers\Launch\LaunchResourceCollection;
use Illuminate\Http\Request;

class LaunchController extends Controller
{

    private $launch;

    function __construct(Launch $launch)
    {
        $this->launch = $launch;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new LaunchResourceCollection($this->launch->index());
    }
}
