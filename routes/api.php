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

Route::middleware('auth:api')->get('/user', function (Request $request) {

    return $request->user();
});

Route::prefix('cart')->group(function () {
    Route::post('/add-product-in-cart', 'App\Http\Controllers\CartController@addProductInCart');
    Route::post('/remove-product-from-cart', 'App\Http\Controllers\CartController@removeProductFromCart');
    Route::post('/set-cart-product-quantity', 'App\Http\Controllers\CartController@setCartProductQuantity');
    Route::get('/get-user-cart', 'App\Http\Controllers\CartController@getUserCart');
});

