<?php

namespace App\Http\Controllers;

use App\Http\Requests\LiveComment\StoreLiveComment;
use App\LiveComment;
use App\Services\ResponseService;
use App\Transformers\LiveComment\LiveCommentResource;
use App\Transformers\LiveComment\LiveCommentResourceCollection;
use Illuminate\Http\Request;

class LiveCommentController extends Controller
{
    private $liveComment;

    public function __construct(LiveComment $liveComment)
    {
        $this->liveComment = $liveComment;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new LiveCommentResourceCollection($this->liveComment->getByUserId($request->input('user_id')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLiveComment $request)
    {       
        try {
            $dataCount =  $this->liveComment->getByUserId($request->input('user_id'));
            if(count($dataCount) == 5){
                return json_encode(array('msg' => 'Você atingiu seu limite de perguntas', 'status' => 401));
            }
            $data = $this
                ->liveComment
                ->store($request->all());
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('live-comment.store', null, $e);
        }
        return json_encode(array('data' => $data, 'status' => 200));
        return new LiveCommentResource($data, array('type' => 'store', 'route' => 'live-comment.store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this
                ->tasks
                ->show($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('live-comment.show', $id, $e);
        }

        return new LiveCommentResource($data, array('type' => 'show', 'route' => 'live-comment.show'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function getCommentByLive($id)
    {
        try {
            $data = $this
                ->liveComment
                ->getCommentByLive($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('live-comment.commentByLive', $id, $e);
        }

        return new LiveCommentResourceCollection($data);
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
                ->tasks
                ->updateTask($request->all(), $id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('live-comment.update', $id, $e);
        }

        return new LiveCommentResource($data, array('type' => 'update', 'route' => 'live-comment.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tasks  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this
                ->tasks
                ->destroyTask($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('live-comment.destroy', $id, $e);
        }
        return new LiveCommentResource($data, array('type' => 'destroy', 'route' => 'live-comment.destroy'));
    }
}
