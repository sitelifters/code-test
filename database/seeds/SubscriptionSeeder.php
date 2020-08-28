<?php

use Illuminate\Database\Seeder;
use App\Subscription;
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
    	// Create some subscription plans.
    	$subscription = Subscription::updateOrCreate(['name' => 'Subscription Plan A']);

    	// Attach subscription to 3 random users.
    	$users = User::inRandomOrder()->limit(3)->get();
    	foreach ($users as $user) {
    		$user->subscriptions()->attach($subscription->id);
    	}

    }
}
