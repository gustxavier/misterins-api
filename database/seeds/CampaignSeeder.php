<?php

use App\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Campaign::create(['name'=>'Teste Google','slug'=>'teste-google','start'=>'2021-03-29', 'end'=>'2035-04-29']);
    }
}
