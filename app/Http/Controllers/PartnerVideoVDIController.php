<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerVideoVDI\StorePartnerVideoVDI;
use App\PartnerVideoVDI;
use App\Services\ResponseService;
use App\Transformers\PartnerVideoVDI\PartnerVideoVDIResource;
use App\Transformers\PartnerVideoVDI\PartnerVideoVDIResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class PartnerVideoVDIController extends Controller
{
    private $partnerVideoVDI;

    public function __construct(PartnerVideoVDI $partnerVideoVDI)
    {
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

        try {
            $path = 'VDI/' . $request->input('type') . '/';

            if (!File::isDirectory(storage_path() . 'app/public/' . $path)) {
                Storage::makeDirectory(storage_path() . 'app/public/' . $path);
            }

            $video = $request->get('archive');
            $fileExtension = explode('/', explode(':', substr($video, 0, strpos($video, ';')))[1])[1];
            $fileNameStorage = time() . '_' . date('Ymd-His') . '.' . $fileExtension;
            
            Storage::disk('public')->put($path . $fileNameStorage, file_get_contents($video));
            
            $request->merge(
                array(
                    'path' => $path . $fileNameStorage,
                    'file_name' => str_replace(" ", "_",$request->input('title')) .'.'. $fileExtension,
                    'file_extension' => $fileExtension
                )
            );

            $data = $this
                ->partnerVideoVDI
                ->store($request->all());
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideovdi.store', null, $e);
        }

        return new PartnerVideoVDIResource($data, array('type' => 'store', 'route' => 'partnervideovdi.store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PartnerVideoVDI  $partnerVideoVDI
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this
                ->partnerVideoVDI
                ->show($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideovdi.show', $id, $e);
        }

        return new PartnerVideoVDIResource($data, array('type' => 'show', 'route' => 'partnervideovdi.show'));
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
                ->partnerVideoVDI
                ->updatePartnerVideoVDI($request->all(), $id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideovdi.update', $id, $e);
        }

        return new PartnerVideoVDIResource($data, array('type' => 'update', 'route' => 'partnervideovdi.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PartnerVideoVDI  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this
                ->partnerVideoVDI
                ->destroyPartnerVideoVDI($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideovdi.destroy', $id, $e);
        }
        return new PartnerVideoVDIResource($data, array('type' => 'destroy', 'route' => 'partnervideovdi.destroy'));
    }

    public function getByType($type)
    {
        try {
            $data = $this
                ->partnerVideoVDI
                ->getVideoByType($type);
            // return $data;
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideovdi.getByType', $type, $e);
        }
        return new PartnerVideoVDIResourceCollection($data);
    }

    /**
     * 
     */
    public function downloadVideo($id)
    {
        try {
            $data = $this
                ->partnerVideoVDI
                ->show($id);

            return response()->download(public_path('storage/' . $data->path));
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideovdi.downloadVideo', $id, $e);
        }
    }
}
