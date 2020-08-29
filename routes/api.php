<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Ensure that all product API requests have a valid API token (using Sanctum middleware)
Route::middleware(['auth:sanctum'])->group(function() {

	// Group all product-related routes together
	Route::group(['prefix' => 'product'], function() 
	{
		// Create a new product
		Route::post('create', 'API\ProductController@create')->name('api.createProduct');

		// Get a product
		Route::get('show/{product}', 'API\ProductController@show')->name('api.getProduct');

		// Update a product
		Route::put('update/{product}', 'API\ProductController@update')->name('api.updateProduct');

		// Delete a product
		Route::delete('delete/{product}', 'API\ProductController@delete')->name('api.deleteProduct');

		// Attach product to user
		Route::post('attach/{product}', 'API\ProductController@attach')->name('api.attachProduct');

		// Remove product from user
		Route::post('detach/{product}', 'API\ProductController@detach')->name('api.detachProduct');

		// List products attached to user
		Route::get('user-index', 'API\ProductController@userIndex')->name('api.getUserIndex');

		// List all products
		Route::get('index', 'API\ProductController@index')->name('api.getIndex');

		// Upload image to product
		Route::post('upload-image/{product}', 'API\ProductController@uploadImage')->name('api.uploadImage');
	});

});
