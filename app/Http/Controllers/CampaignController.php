<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignHasWppGroup;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
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
