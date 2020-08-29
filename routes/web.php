<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]); // Don't let users register

Route::get('/home', 'HomeController@index')->name('home');

// Generate a new API token for the user, store it in the database (hashed), and return the one-time plain text token
Route::get('token', function(Request $request) {
	$token = Auth::user()->createToken('api-token');
	return response()->json(['token' => $token->plainTextToken]);
})->middleware('auth')->name('user.generateApiToken');