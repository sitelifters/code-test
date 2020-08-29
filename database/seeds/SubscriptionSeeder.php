<?php

use Illuminate\Database\Seeder;
use App\User;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Create a subscription plan
    	$subscription = factory(App\Subscription::class)->create();

    	// Attach subscription to 3 random users
    	$users = User::inRandomOrder()->limit(3)->get();
    	foreach ($users as $user) {
    		$user->subscriptions()->attach($subscription->id);
    	}

    }
}
