<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveComment extends Model
{
    protected $fillable = ['user_id','comment','live_id'];

    public function index(){
        return  $this->get();
    }

    public function getByUserId($user_id){        
        return  $this->where('user_id', '=',$user_id)->get();
    }

    public function store($fields)
    {
        return $this->create($fields); 
    }

    public function show($id){
        $show = $this->find($id);
 
        if (!$show) {
            throw new \Exception('Nada Encontrado', -404);
        }

        return $show;
    }

    public function getCommentByLive($liveId){
        $liveComments = $this->where('live_id', '=', $liveId)->get();

        return $liveComments;
    }

    public function closeTask($id){
        $task = $this->show($id);
        $task->update(['status' => 1]);
        
        $list = $this->find($task['list_id']);

        $taskOpen = $this->where('list_id', '=', $task['list_id'])
        ->where('status', 0)
        ->get();
        
        if(count($taskOpen) === 0){
            $list->update(['status' => 1]);
        }
        return $task;
    }

    public function updateTask($fields, $id)
    {
        $task = $this->show($id);

        $task->update($fields);
        return $task;
    }

    public function destroyTask($id)
    {
        $task = $this->show($id);
        $task->delete();

        $list = $this->find($task['list_id']);

        $taskOpen = $this->where('list_id', '=', $task['list_id'])
        ->where('status', 0)
        ->get();
        
        if(count($taskOpen) === 0){
            $list->update(['status' => 1]);
        }

        return $task;
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
    public function livecomment()
    {
        return $this->belongsToMany('App\LiveComment', 'live_id', 'user_id');
        // return $this->belongsTo('App\Tasks', 'list_id', 'id');
    }
}
