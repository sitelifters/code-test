<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 5 users and assign API tokens to them
	    factory(App\User::class, 5)->create()->each(function ($user) {
	    	$user->createToken('api-token');
	    });
    }
}
