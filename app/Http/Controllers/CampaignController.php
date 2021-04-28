<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignHasWppGroup;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function redirect($slug){

        $campaign = Campaign::where('slug', $slug)->first();

        if(!empty($campaign)){
            $group = CampaignHasWppGroup::where('campaign_id', $campaign->id)->get();
            
            foreach($group as $obj){
                if($obj->actually_click < $obj->max_click && ($campaign->start <= date('Y-m-d')) && $campaign->end >= date('Y-m-d')){
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
}
