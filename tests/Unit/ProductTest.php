<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use App\Product;
use Laravel\Sanctum\Sanctum;
use Faker;

class ProductTest extends TestCase
{

	/**
	 * Pass the test if authenticated user can generate an API token successfully.
	 */
	public function testAuthenticatedUserCanGenerateToken()
	{
	    $user = User::first();
		$response = $this->actingAs($user)->get(route('user.generateApiToken'));
		$response->assertOk();
	}

	/**
	 * Pass the test if unauthenticated user cannot generate an API token.
	 */
	public function testUnauthenticatedUserCannotGenerateToken()
	{
		$response = $this->get(route('user.generateApiToken'));
		$response->assertRedirect('/login'); // Should redirect to login page
	}


	/**
	 * Pass the test if an authenticated user with a valid API token can access the API.
	 */
	public function testAuthenticatedUserWithTokenCanAccessApi()
	{
		$user = Sanctum::actingAs(
		    factory(User::class)->create(),
		    ['*']
		);

		$response = $this->actingAs($user, 'api')->getJson(route('api.getIndex'));
		$response->assertOk();
	}


	/**
	 * Pass the test if an authenticated user without a valid API token cannot access the API.
	 */
	public function testAuthenticatedUserWithoutTokenCannotAccessApi()
	{
		$user = User::first();
		$response = $this->actingAs($user, 'api')->getJson(route('api.getIndex'));
		$response->assertUnauthorized();
	}


	/**
	 * Pass the test if an unauthenticated user cannot access the API.
	 */
	public function testUnauthenticatedUserCannotAccessApi()
	{
		$response = $this->getJson(route('api.getIndex'));
		$response->assertUnauthorized();
	}


	/**
	 * Pass the test if a user with a subscription can attach a product via the API.
	 */
	public function testUserWithSubscriptionCanAttachProduct()
	{
		$user = Sanctum::actingAs(
		    User::whereHas('subscriptions')->first(),
		    ['*']
		);

		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->postJson(route('api.attachProduct', $product));
		$response->assertOk();
	}


	/**
	 * Pass the test if a user without a subscription cannot attach a product via the API.
	 */
	public function testUserWithoutSubscriptionCannotAttachProduct()
	{
		$user = Sanctum::actingAs(
		    User::doesntHave('subscriptions')->first(),
		    ['*']
		);

		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->postJson(route('api.attachProduct', $product));
		$response->assertForbidden(); // 403
	}

}
