<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignHasWppGroup;
use App\Http\Requests\Campaign\StoreCampaign;
use App\Services\ResponseService;
use App\Transformers\Campaign\CampaignResource;
use App\Transformers\Campaign\CampaignResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{

    private $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new CampaignResourceCollection($this->campaign->index());
    }

    public function store(StoreCampaign $request)
    {
        if (strtotime($request->input('start')) >= strtotime($request->input('end'))) {
            return ResponseService::alert('warning', 'Atenção! A data de início deve ser menor que a de término da campanha');
        }

        try {
            $isExists = $this->campaign->getBySlug($request->input('slug'));

            if (count($isExists) > 0) {
                return ResponseService::alert('warning', 'Atenção! Este slug já existe');
            }

            $data = $this
                ->campaign
                ->store($request->all());
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('campaign.store', null, $e);
        }

        return new CampaignResource($data, array('type' => 'store', 'route' => 'campaign.store'));
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
                ->campaign
                ->show($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('campaign.show', $id, $e);
        }

        return new CampaignResource($data, array('type' => 'show', 'route' => 'campaign.show'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Campaign  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this
                ->campaign
                ->destroyCampaign($id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('campaign.destroy', $id, $e);
        }
        return new CampaignResource($data, array('type' => 'destroy', 'route' => 'campaign.destroy'));
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $this
                ->campaign
                ->updateCampaign($request->all(), $id);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('campaign.update', $id, $e);
        }

        return new CampaignResource($data, array('type' => 'update', 'route' => 'campaign.update'));
    }

    public function redirect($slug)
    {

        $campaign = Campaign::where('slug', $slug)->first();

        if (!empty($campaign)) {
            $group = CampaignHasWppGroup::where('campaign_id', $campaign->id)->get();

            foreach ($group as $obj) {
                if ($obj->actually_click < $obj->max_click && ($campaign->start <= date('Y-m-d')) && $campaign->end >= date('Y-m-d')) {
                    $groupWpp = CampaignHasWppGroup::find($obj->id);
                    $groupWpp->actually_click = $groupWpp->actually_click + 1;
                    $groupWpp->update();
                    return redirect($groupWpp->redirect_link);
                }
            }
            abort(403);
        }
        abort(404);
    }

    public function redirectParceria($slug)
    {
        try {

            $campaign = Campaign::where('slug', $slug)->first();

            if (!empty($campaign)) {
                $group = CampaignHasWppGroup::where('campaign_id', $campaign->id)->get();
                foreach ($group as $obj) {
                    // Se existe o link e ele ainda pode receber cliques, soma mais um e redireciona para esse mesmo link
                    if ($obj->actually_click < $obj->max_click && ($campaign->start <= date('Y-m-d')) && $campaign->end >= date('Y-m-d')) {
                        $groupWpp = CampaignHasWppGroup::find($obj->id);
                        $groupWpp->actually_click = $groupWpp->actually_click + 1;
                        $groupWpp->update();
                        return redirect($groupWpp->redirect_link);
                    }
                }
                // // Se nao for redirecionado, zera todos os cliques para comecar novamente
                CampaignHasWppGroup::where('campaign_id', $campaign->id)->update(['actually_click' => 0]);

                if (!File::isDirectory(storage_path() . '/app/public/parceria-bkp')) {
                    Storage::makeDirectory(storage_path() . '/app/public/parceria-bkp');
                }

                file_put_contents(public_path('storage/parceria-bkp/' . date('Ymd') . '_' . time() . '.json'), $group);

                return $this->redirectParceria($slug);
                die();
            }
        } catch (\Throwable $th) {
            abort(403);
        }
        abort(404);
    }
}
