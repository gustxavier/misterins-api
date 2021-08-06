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
            if ($request->input('type') === null) {
                return ResponseService::alert('warning', 'Oops! Você precisa selecionar um tipo de postagem.');
            }

            $path = 'socio/' . $request->course_id . '/' . $request->input('type') . '/';

            if (!File::isDirectory(storage_path() . '/app/public/' . $path)) {
                Storage::makeDirectory(storage_path() . '/app/public/' . $path);
            }

            $image = $request->get('thumbnail');

            if (empty($image)) {
                return ResponseService::alert('warning', 'Oops! Você precisa inserir uma imagem.');
            }

            $fileExtension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            $fileNameStorage = time() . '_' . date('Ymd-His') . '.' . $fileExtension;

            $store = Storage::disk('public')->put($path . $fileNameStorage, file_get_contents($image));
            if ($store) {
                $request->merge(['thumbnail' => $path . $fileNameStorage]);
                $data = $this->partnerVideo->store($request->all());
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
    public function getVideosByCourse($id)
    {
        try {
            $data = $this
                ->partnerVideo
                ->getVideosByCourse($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.getvideosbycourse', $id, $e);
        }

        return new PartnerVideoResourceCollection($data);
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
                ->show($id);
            unlink(storage_path() . '/app/public/' . $data->thumbnail);
            $data->delete();
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
                ->getVideos($courseID, $type);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.getvideos', $type, $e);
        }
        return new PartnerVideoResourceCollection($data);
    }

    public function uploadThumbnail(Request $request, $id)
    {
        try {

            $video = $this
                ->partnerVideo
                ->show($id);

            $path = 'socio/' . $video->course_id . '/' . $video->type . '/';

            if (!File::isDirectory(storage_path() . '/app/public/' . $path)) {
                Storage::makeDirectory(storage_path() . '/app/public/' . $path);
            }

            $image = $request->get('thumbnail');

            if (empty($image)) {
                return ResponseService::alert('warning', 'Oops! Você precisa inserir uma imagem.');
            }

            if (file_exists(storage_path() . '/app/public/' . $video->thumbnail)) {
                unlink(storage_path() . '/app/public/' . $video->thumbnail);
            }

            $fileExtension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            $fileNameStorage = time() . '_' . date('Ymd-His') . '.' . $fileExtension;

            $store = Storage::disk('public')->put($path . $fileNameStorage, file_get_contents($image));

            if ($store) {
                $request->merge(
                    array('thumbnail' => $path . $fileNameStorage),
                );
                $data = $this
                    ->partnerVideo
                    ->updatePartnerVideo($request->all(), $id);
            }

        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.uploadThumbnail', $id, $e);
        }

        return new PartnerVideoResource($data, array('type' => 'update', 'route' => 'partnervideo.uploadThumbnail'));
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
            return response()->download(public_path('storage/' . $data->path), null, array('Content-Type: application/mp4'));
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('partnervideo.downloadVideo', $id, $e);
        }
    }
}
