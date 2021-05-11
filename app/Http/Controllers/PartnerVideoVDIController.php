<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerVideoVDI\StorePartnerVideoVDI;
use App\PartnerVideoVDI;
use App\Services\ResponseService;
use App\Transformers\PartnerVideoVDI\PartnerVideoVDIResource;
use App\Transformers\PartnerVideoVDI\PartnerVideoVDIResourceCollection;
use Illuminate\Http\Request;

class PartnerVideoVDIController extends Controller
{
    private $partnerVideoVDI;

    public function __construct(PartnerVideoVDI $partnerVideoVDI){
        $this->partnerVideoVDI = $partnerVideoVDI;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new PartnerVideoVDIResourceCollection($this->partnerVideoVDI->index());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePartnerVideoVDI $request)
    {
        try{        
            $data = $this
            ->partnerVideoVDI
            ->store($request->all());
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('partnervideovdi.store',null,$e);
        }

        return new PartnerVideoVDIResource($data,array('type' => 'store','route' => 'partnervideovdi.store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PartnerVideoVDI  $partnerVideoVDI
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{        
            $data = $this
            ->PartnerVideoVDI
            ->show($id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('partnervideovdi.show',$id,$e);
        }

        return new PartnerVideoVDIResource($data,array('type' => 'show','route' => 'partnervideovdi.show'));
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
            ->PartnerVideoVDI
            ->updatePartnerVideoVDI($request->all(), $id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('partnervideovdi.update',$id,$e);
        }

        return new PartnerVideoVDIResource($data,array('type' => 'update','route' => 'partnervideovdi.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PartnerVideoVDI  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = $this
            ->PartnerVideoVDI
            ->destroyPartnerVideoVDI($id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('partnervideovdi.destroy',$id,$e);
        }
        return new PartnerVideoVDIResource($data,array('type' => 'destroy','route' => 'partnervideovdi.destroy')); 
    }
}
