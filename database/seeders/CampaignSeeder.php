<?php

namespace Database\Seeders;

use App\Models\Campaign;
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
        Campaign::create(['name'=>'Aniversário Mister Ins','slug'=>'bday-2021','start'=>'2021-03-29', 'end'=>'2021-04-29']);
        Campaign::create(['name'=>'Aniversário Mister Ins','slug'=>'bday-2021B','start'=>'2021-03-29', 'end'=>'2021-04-29']);
    }
}
