<?php

use Illuminate\Database\Seeder;

class CopySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\Copy')->create();
    }
}
