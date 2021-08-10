<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name', 'slug', 'start', 'end', 'campaign_type_id',
    ];

    public function index()
    {
        return $this->get();
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

    public function updateCampaign($fields, $id)
    {
        $campaign = $this->show($id);
        $campaign->update($fields);
        return $campaign;
    }

    public function destroyCampaign($id)
    {
        $campaign = $this->show($id);
        $campaign->delete();
        return $campaign->delete();
    }
}
