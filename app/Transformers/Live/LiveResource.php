<?php

namespace App\Transformers\Live;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\ResponseService;

class LiveResource extends JsonResource
{
    /**
     * @var
     */
    private $config;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $config = array())
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);

        $this->config = $config;
    }
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,            
            'description' => $this->description,        
            'thumbnail' => $this->thumbnail,        
            'date' => $this->date,        
            'hour' => $this->hour,        
            'is_active' => $this->is_active,        
            'date_formated' => $this->date_formated,        
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return ResponseService::default($this->config, $this->id);
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request
     * @param  \Illuminate\Http\Response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->setStatusCode(200);
    }
}
