<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(TaskListSeeder::class);
        $this->call(TasksSeeder::class);
        $this->call(CampaignSeeder::class);
        $this->call(CampaignHasWppGroupSeeder::class);
        $this->call(CopySeeder::class);
        $this->call(CourseSeeder::class);
    }
}
