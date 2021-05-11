<?php

namespace App\Http\Controllers;

use App\Copy;
use App\Http\Requests\Copy\StoreCopy;
use App\Services\ResponseService;
use App\Transformers\Copy\CopyResource;
use App\Transformers\Copy\CopyResourceCollection;
use Illuminate\Http\Request;

class CopyController extends Controller
{
    private $copy;

    public function __construct(Copy $copy){
        $this->copy = $copy;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new CopyResourceCollection($this->copy->index());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCopy $request)
    {
        try{        
            $data = $this
            ->copy
            ->store($request->all());
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('copy.store',null,$e);
        }

        return new CopyResource($data,array('type' => 'store','route' => 'copy.store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Copy  $copy
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{        
            $data = $this
            ->copy
            ->show($id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('copy.show',$id,$e);
        }

        return new CopyResource($data,array('type' => 'show','route' => 'copy.show'));
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
            ->copy
            ->updateCopy($request->all(), $id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('copy.update',$id,$e);
        }

        return new CopyResource($data,array('type' => 'update','route' => 'copy.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Copys  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = $this
            ->copy
            ->destroyTask($id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('copy.destroy',$id,$e);
        }
        return new CopyResource($data,array('type' => 'destroy','route' => 'copy.destroy')); 
    }
}
