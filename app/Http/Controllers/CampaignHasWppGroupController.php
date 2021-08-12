<?php

namespace App\Http\Controllers;

use App\CampaignHasWppGroup;
use App\Http\Requests\CampaignGroup\StoreCampaignGroup;
use App\Services\ResponseService;
use App\Transformers\CampaignGroup\CampaignGroupResource;
use App\Transformers\CampaignGroup\CampaignGroupResourceCollection;
use Illuminate\Http\Request;

class CampaignHasWppGroupController extends Controller
{

    private $campaignGroup;

    public function __construct(CampaignHasWppGroup $campaignGroup)
    {
        $this->campaignGroup = $campaignGroup;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new CampaignGroupResourceCollection($this->campaignGroup->index());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCampaignGroup $request)
    {         
        try {
            $data = $this
                ->campaignGroup
                ->store($request->all());
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('campaign.store', null, $e);
        }

        return new CampaignGroupResource($data, array('type' => 'store', 'route' => 'campaigngroup.store'));
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
                ->campaignGroup
                ->show($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('campaigngroup.show', $id, $e);
        }

        return new CampaignGroupResource($data, array('type' => 'show', 'route' => 'campaigngroup.show'));
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
                ->campaignGroup
                ->updateCampaignGroup($request->all(), $id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('campaigngroup.update', $id, $e);
        }

        return new CampaignGroupResource($data, array('type' => 'update', 'route' => 'campaigngroup.update'));
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
            $this
                ->campaignGroup
                ->destroyCampaignGroup($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('campaigngroup.destroy', $id, $e);
        }
        return ResponseService::default(array('type' => 'destroy', 'route' => 'campaigngroup.destroy'), $id);
    }

    public function getByCampaignID($id)
    {
        return new CampaignGroupResourceCollection($this->campaignGroup->getByCampaignID($id));
    }
}
