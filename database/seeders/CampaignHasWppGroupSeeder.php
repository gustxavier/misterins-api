<?php

namespace Database\Seeders;

use App\Models\CampaignHasWppGroup;
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
        CampaignHasWppGroup::create(['campaign_id' => 1,'name_group'=>'#1 Zap Ins','redirect_link'=>'https://chat.whatsapp.com/HkfOn9d8x4j0J8SCiKjcmc','order'=>'1', 'max_click'=>'3']);
        CampaignHasWppGroup::create(['campaign_id' => 1,'name_group'=>'#2 Zap Ins','redirect_link'=>'https://chat.whatsapp.com/GAdFGjy9nF8BwB0SUU9kw9','order'=>'2', 'max_click'=>'3']);
    }
}
