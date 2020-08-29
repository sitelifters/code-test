<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];


    /**
     * The users that are subscribed to this subscription.
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'users_subscriptions');
    }

}
