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
		Route::post('create', 'API\ProductController@create');

		// Get a product
		Route::get('show/{product}', 'API\ProductController@show');

		// Update a product
		Route::put('update/{product}', 'API\ProductController@update');

		// Delete a product
		Route::delete('delete/{product}', 'API\ProductController@delete');

		// Attach product to user
		Route::post('attach/{product}', 'API\ProductController@attach');

		// Remove product from user
		Route::post('detach/{product}', 'API\ProductController@detach');

		// List products attached to user
		Route::get('user-index', 'API\ProductController@userIndex');

		// List all products
		Route::get('index', 'API\ProductController@index');
	});

});
