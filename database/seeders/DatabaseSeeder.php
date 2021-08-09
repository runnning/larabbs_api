<?php

namespace Database\Seeders;

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
        $this->call(UsersTableSeeder::class);
		$this->call(RepliesTableSeeder::class);
        $this->call(TopicsTableSeeder::class);
        $this->call(LinkTableSeeder::class);
        // \App\Models\User::factory(10)->create();
    }
}