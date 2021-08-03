<?php

namespace App\Http\Controllers;

use App\Http\Requests\Live\StoreLive;
use App\Live;
use App\LiveHasCourse;
use App\Services\ResponseService;
use App\Transformers\Live\LiveResource;
use App\Transformers\Live\LiveResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
        try {
            $path = 'lives/thumbnail/';

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
                $data = $this->live->insert($request->all());
            }

        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('lives.store', null, $e);
        }

        return new LiveResource($data, array('type' => 'store', 'route' => 'lives.store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this
                ->live
                ->show($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('lives.show', $id, $e);
        }
        return new LiveResource($data, array('type' => 'show', 'route' => 'lives.show'));
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
        try {
            $data = $this
                ->live
                ->updateLive($request->all(), $id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('lives.update', $id, $e);
        }

        return new LiveResource($data, array('type' => 'update', 'route' => 'lives.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this
                ->lives
                ->destroyLive($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('lives.destroy', $id, $e);
        }
        return new LiveResource($data, array('type' => 'destroy', 'route' => 'lives.destroy'));
    }

    public function uploadThumbnail(Request $request, $id)
    {
        try {
            $path = 'lives/thumbnail/';

            if (!File::isDirectory(storage_path() . '/app/public/' . $path)) {
                Storage::makeDirectory(storage_path() . '/app/public/' . $path);
            }

            $image = $request->get('thumbnail');

            if (empty($image)) {
                return ResponseService::alert('warning', 'Oops! Você precisa inserir uma imagem.');
            }

            $live = $this
                ->live
                ->show($id);

            if (file_exists(storage_path() . '/app/public/' . $live->thumbnail)) {
                unlink(storage_path() . '/app/public/' . $live->thumbnail);
            }

            $fileExtension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            $fileNameStorage = time() . '_' . date('Ymd-His') . '.' . $fileExtension;

            $store = Storage::disk('public')->put($path . $fileNameStorage, file_get_contents($image));

            if ($store) {
                $request->merge(
                    array('thumbnail' => $path . $fileNameStorage),
                );
                $data = $this
                    ->live
                    ->updateLive($request->all(), $id);
            }

        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('lives.uploadThumbnail', $id, $e);
        }

        return new LiveResource($data, array('type' => 'update', 'route' => 'lives.uploadThumbnail'));
    }

    public function updateLiveHasCourses(Request $request, $id)
    {
        try {
            LiveHasCourse::where('live_id', '=', $id)->delete();

            foreach ($request->all() as $value) {
                $data[] = [
                    'live_id' => $id,
                    'course_id' => $value,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            LiveHasCourse::insert($data);

            $data = $this->live->show($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('lives.updateLiveHasCourses', $id, $e);
        }

        return new LiveResource($data, array('type' => 'update', 'route' => 'lives.updateLiveHasCourses'));
    }
}
