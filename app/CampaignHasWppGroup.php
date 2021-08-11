<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignHasWppGroup extends Model
{
    protected $fillable = [
        'campaign_id', 'name_group', 'redirect_link', 'order', 'max_click', 'actually_click',
    ];

    public function index()
    {
        return $this->get();
    }

    public function getByCampaignID($campaign_id)
    {
        return $this->where('campaign_id', '=', $campaign_id)->get();
    }

    public function store($fields)
    {
        return $this->create($fields);
    }

    public function show($id)
    {
        $show = $this->find($id);

        if (!$show) {
            throw new \Exception('Nada Encontrado', -404);
        }

        return $show;
    }

    public function updateCampaignGroup($fields, $id)
    {
        $campaign = $this->show($id);
        $campaign->update($fields);
        return $campaign;
    }

    public function destroyCampaignGroup($id)
    {
        $campaign = $this->find($id);
        return $campaign->delete();
    }
}
