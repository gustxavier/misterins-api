<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerVideo\StorePartnerVideo;
use App\PartnerVideo;
use App\Services\ResponseService;
use App\Transformers\PartnerVideo\PartnerVideoResource;
use App\Transformers\PartnerVideo\PartnerVideoResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class PartnerVideoController extends Controller
{
    private $partnerVideo;

    public function __construct(PartnerVideo $partnerVideo)
    {
        $this->partnerVideo = $partnerVideo;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new PartnerVideoResourceCollection($this->partnerVideo->index());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePartnerVideo $request)
    {
        try {
            $path = '/' . $request->input('type') . '/';

            if (!File::isDirectory(storage_path() . '/app/public/' . $path)) {
                Storage::makeDirectory(storage_path() . '/app/public/' . $path);
            }

            $video = $request->get('archive');
            $fileExtension = explode('/', explode(':', substr($video, 0, strpos($video, ';')))[1])[1];
            $fileNameStorage = time() . '_' . date('Ymd-His') . '.' . $fileExtension;
            
            $store = Storage::disk('public')->put($path . $fileNameStorage, file_get_contents($video));
            if($store){
                $request->merge(
                    array(
                        'path' => $path . $fileNameStorage,
                        'file_name' => str_replace(" ", "_",$request->input('title')) .'.'. $fileExtension,
                        'file_extension' => $fileExtension
                    )
                );
                $data = $this
                    ->partnerVideo
                    ->store($request->all());
            }                        
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.store', null, $e);
        }

        return new PartnerVideoResource($data, array('type' => 'store', 'route' => 'partnervideo.store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PartnerVideo  $partnerVideo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this
                ->partnerVideo
                ->show($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.show', $id, $e);
        }

        return new PartnerVideoResource($data, array('type' => 'show', 'route' => 'partnervideo.show'));
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\PartnerVideo  $partnerVideo
     * @return \Illuminate\Http\Response
     */
    public function getVideoByCourseID($id)
    {
        try {
            $data = $this
                ->partnerVideo
                ->getVideoByCourseID($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.getvideobycourseid', $id, $e);
        }

        return new PartnerVideoResource($data, array('type' => 'get', 'route' => 'partnervideo.getvideobycourseid'));
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
                ->partnerVideo
                ->updatePartnerVideo($request->all(), $id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.update', $id, $e);
        }

        return new PartnerVideoResource($data, array('type' => 'update', 'route' => 'partnervideo.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PartnerVideo  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this
                ->partnerVideo
                ->destroyPartnerVideo($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.destroy', $id, $e);
        }
        return new PartnerVideoResource($data, array('type' => 'destroy', 'route' => 'partnervideo.destroy'));
    }

    public function getVideos($courseID, $type)
    {
        try {
            $data = $this
                ->partnerVideo
                ->getVideos($courseID,$type);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.getvideos', $type, $e);
        }
        return new PartnerVideoResourceCollection($data);
    }

    /**
     * 
     */
    public function downloadVideo($id)
    {
        
        try {
            $data = $this
                ->partnerVideo
                ->show($id);
            return response()->download(public_path('storage/' . $data->path),null,array('Content-Type: application/mp4'));
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.downloadVideo', $id, $e);
        }
    }
}
