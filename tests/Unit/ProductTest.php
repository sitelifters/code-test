<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use App\Product;
use Laravel\Sanctum\Sanctum;
use Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductTest extends TestCase
{

	/**
	 * Create a new Sanctum-authenticated API user.
	 */
	public function createFakeApiUser()
	{
		return Sanctum::actingAs(
		    factory(User::class)->create(),
		    ['*']
		);
	}


	/**
	 * Generate data for a product, omitting the specified attribute.
	 */
	public function makeProductArrayWithMissingAttribute($omit_attribute = null)
	{
		$faker = Faker\Factory::create();
		
		$product = [
	        'name' => $omit_attribute == 'name' ? null : $faker->word,
	        'description' => $omit_attribute == 'description' ? null : $faker->sentence,
	        'price' => $omit_attribute == 'price' ? null : $faker->numberBetween(1, 100),
		];

		return $product;
	}


	/**
	 * Pass the test if authenticated user can generate an API token successfully.
	 */
	public function testAuthenticatedUserCanGenerateToken()
	{
		$user = factory(User::class)->create();

		// Try to generate an API token as a logged in user
		$response = $this->actingAs($user)->get(route('user.generateApiToken'));
		$response->assertOk();

		// Cleanup
		$user->delete();
	}

	/**
	 * Pass the test if unauthenticated user cannot generate an API token.
	 */
	public function testUnauthenticatedUserCannotGenerateToken()
	{
		// Try to generate an API token as a guest
		$response = $this->get(route('user.generateApiToken'));
		$response->assertRedirect('/login'); // Should redirect to login page
	}


	/**
	 * Pass the test if an authenticated user with a valid API token can access the API.
	 */
	public function testAuthenticatedUserWithTokenCanAccessApi()
	{
		$user = $this->createFakeApiUser();

		// Try to access the API as a logged in user with a valid API token
		$response = $this->actingAs($user, 'api')->getJson(route('api.getIndex'));
		$response->assertOk();

		// Cleanup
		$user->delete();
	}


	/**
	 * Pass the test if an authenticated user without a valid API token cannot access the API.
	 */
	public function testAuthenticatedUserWithoutTokenCannotAccessApi()
	{
		$user = factory(User::class)->create();

		// Try to access the API as a logged in user without an API token
		$response = $this->actingAs($user, 'api')->getJson(route('api.getIndex'));
		$response->assertUnauthorized();

		// Cleanup
		$user->delete();
	}


	/**
	 * Pass the test if an unauthenticated user cannot access the API.
	 */
	public function testUnauthenticatedUserCannotAccessApi()
	{
		// Try to access the API a guest
		$response = $this->getJson(route('api.getIndex'));
		$response->assertUnauthorized();
	}


	/**
	 * Pass the test if an authenticated user can add a valid product.
	 */
	public function testUserCanAddValidProduct()
	{
		$user = $this->createFakeApiUser();

		// Try to create product with all attributes filled
		$response = $this->actingAs($user, 'api')->postJson(route('api.createProduct'), $this->makeProductArrayWithMissingAttribute());
		$response->assertOk();

		// Cleanup
		$user->delete();
		$product = Product::find($response->json('product')['id'])->delete();
	}


	/**
	 * Pass the test if the user cannot create a product with a missing name.
	 */
	public function testUserCannotAddProductWithMissingName()
	{
		$user = $this->createFakeApiUser();

		// Try to create product without name
		$response = $this->actingAs($user, 'api')->postJson(route('api.createProduct'), $this->makeProductArrayWithMissingAttribute('name'));
		$response->assertStatus(422); // Validation error

		// Cleanup
		$user->delete();
	}


	/**
	 * Pass the test if the user cannot create a product with a missing description.
	 */
	public function testUserCannotAddProductWithMissingDescription()
	{
		$user = $this->createFakeApiUser();

		// 	Try to create product without description
		$response = $this->actingAs($user, 'api')->postJson(route('api.createProduct'), $this->makeProductArrayWithMissingAttribute('description'));
		$response->assertStatus(422); // Validation error

		// Cleanup
		$user->delete();
	}


	/**
	 * Pass the test if the user cannot create a product with a missing price.
	 */
	public function testUserCannotAddProductWithMissingPrice()
	{
		$user = $this->createFakeApiUser();

		// Try to create a product without price
		$response = $this->actingAs($user, 'api')->postJson(route('api.createProduct'), $this->makeProductArrayWithMissingAttribute('price'));
		$response->assertStatus(422); // Validation error

		// Cleanup
		$user->delete();
	}


	/**
	 * Pass the test if the user can update a valid product.
	 */
	public function testUserCanUpdateValidProduct()
	{
		$user = $this->createFakeApiUser();

		// Try to update a product with all attributes filled
		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->putJson(route('api.updateProduct', $product), $this->makeProductArrayWithMissingAttribute());
		$response->assertOk();

		// Cleanup
		$user->delete();
		$product->delete();
	}


	/**
	 * Pass the test if the user cannot update a product with a missing name.
	 */
	public function testUserCannotUpdateProductWithMissingName()
	{
		$user = $this->createFakeApiUser();

		// Try to update product without name	
		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->putJson(route('api.updateProduct', $product), $this->makeProductArrayWithMissingAttribute('name'));
		$response->assertStatus(422); // Validation error

		// Cleanup
		$user->delete();
		$product->delete();
	}


	/**
	 * Pass the test if the user cannot update a product with a missing description.
	 */
	public function testUserCannotUpdateProductWithMissingDescription()
	{
		$user = $this->createFakeApiUser();

		// Try to update product without description
		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->putJson(route('api.updateProduct', $product), $this->makeProductArrayWithMissingAttribute('description'));
		$response->assertStatus(422); // Validation error

		// Cleanup
		$user->delete();
		$product->delete();
	}


	/**
	 * Pass the test if the user cannot update a product with a missing price.
	 */
	public function testUserCannotUpdateProductWithMissingPrice()
	{
		$user = $this->createFakeApiUser();

		// Try to update product without price
		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->putJson(route('api.updateProduct', $product), $this->makeProductArrayWithMissingAttribute('price'));
		$response->assertStatus(422); // Validation error

		// Cleanup
		$user->delete();
		$product->delete();
	}


	/**
	 * Pass the test if the user can get a valid product.
	 */
	public function testUserCanGetValidProduct()
	{
		$user = $this->createFakeApiUser();

		// Create fake product and try to get it
		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->getJson(route('api.getProduct', $product));
		$response->assertOk();

		// Cleanup
		$user->delete();
		$product->delete();
	}


	/**
	 * Pass the test if the user can delete a valid product.
	 */
	public function testUserCanDeleteValidProduct()
	{
		$user = $this->createFakeApiUser();

		// Create fake product and try to delete it
		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->deleteJson(route('api.deleteProduct', $product));
		$response->assertOk();

		// Cleanup
		$user->delete();		
	}


	/**
	 * Pass the test if the user can get a list of all products.
	 */
	public function testUserCanGetAllProducts()
	{
		$user = $this->createFakeApiUser();

		// Try to get list of products
		$response = $this->actingAs($user, 'api')->getJson(route('api.getIndex'));
		$response->assertOk();

		// Cleanup
		$user->delete();
	}


	/**
	 * Pass the test if the user can get a list of their own products.
	 */
	public function testUserCanGetOwnProducts()
	{
		$user = $this->createFakeApiUser();

		// Try to get the user's products
		$response = $this->actingAs($user, 'api')->getJson(route('api.getUserIndex'));
		$response->assertOk();

		// Cleanup
		$user->delete();
	}


	/**
	 * Pass the test if a user with a subscription can attach a product via the API.
	 */
	public function testUserWithSubscriptionCanAttachProduct()
	{
		$user = $this->createFakeApiUser();

		// Attach subscription to the user
		$user->subscriptions()->attach(1);

		// Create fake product and try to attach it to user
		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->postJson(route('api.attachProduct', $product));
		$response->assertOk();

		// Cleanup
		$user->delete();
		$product->delete();
	}


	/**
	 * Pass the test if a user without a subscription cannot attach a product via the API.
	 */
	public function testUserWithoutSubscriptionCannotAttachProduct()
	{
		$user = $this->createFakeApiUser();

		// Create fake product and try to attach it to user
		$product = factory(Product::class)->create();
		$response = $this->actingAs($user, 'api')->postJson(route('api.attachProduct', $product));
		$response->assertForbidden(); // 403

		// Cleanup
		$user->delete();
		$product->delete();
	}


	/**
	 * Pass the test if the user can upload a product image and the image was stored successfully.
	 */
	public function testUserCanUploadProductImage()
	{
		$user = $this->createFakeApiUser();

		// Create a product without an image
		$product = Product::create($this->makeProductArrayWithMissingAttribute('image'));

		// Create the fake image file
		Storage::fake('images');
		$image_file = UploadedFile::fake()->image('test_image.jpg');

		// Try to upload the image
		$response = $this->actingAs($user, 'api')->postJson(route('api.uploadImage', $product), ['image' => $image_file]);
		$response->assertOk();

		// Assert the file was stored properly
		Storage::disk('images')->assertExists($product->image);

		// Cleanup
		$user->delete();
		$product->delete();
		Storage::disk('images')->delete($product->image);
	}


}
