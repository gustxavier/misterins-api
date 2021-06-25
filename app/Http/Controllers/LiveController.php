<?php

namespace App\Http\Controllers;

use App\Http\Requests\Live\StoreLive;
use App\Live;
use App\Services\ResponseService;
use App\Transformers\Live\LiveResource;
use App\Transformers\Live\LiveResourceCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LiveController extends Controller
{

    private $live;

    public function __construct(Live $live)
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
        return new LiveResourceCollection($this->live->index());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLive $request)
    {

        return $request;

        try{        
            $data = $this->live->insert($request->all());           
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('lives.store',null,$e);
        }

        return new LiveResource($data,array('type' => 'store','route' => 'lives.store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{        
            $data = $this
            ->live
            ->show($id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('lives.show',$id,$e);
        }
        // return $data;
        return new LiveResource($data,array('type' => 'show','route' => 'lives.show'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{        
            $data = $this
            ->live
            ->updateLive($request->all(), $id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('lives.update',$id,$e);
        }

        return new LiveResource($data,array('type' => 'update','route' => 'lives.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = $this
            ->lives
            ->destroyLive($id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('lives.destroy',$id,$e);
        }
        return new LiveResource($data,array('type' => 'destroy','route' => 'lives.destroy')); 
    }
}
