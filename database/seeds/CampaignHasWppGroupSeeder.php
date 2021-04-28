<?php

use App\CampaignHasWppGroup;
use Illuminate\Database\Seeder;

class CampaignHasWppGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CampaignHasWppGroup::create(['campaign_id' => 1,'name_group'=>'Google','redirect_link'=>'https://www.google.com.br/','order'=>'1', 'max_click'=>'250']);
    }
}
