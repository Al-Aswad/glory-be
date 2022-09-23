<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GiftController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::controller(GiftController::class)->middleware('auth.jwt')->group(function () {
        Route::get('gifts', 'getGifts')->name('gifts');
        Route::get('gifts/{id}', 'getGiftsById')->name('giftsById');
        Route::post('gifts', 'createGift')->name('createGift');
        Route::put('gifts/{id}', 'updateGift')->name('updateGift');
        Route::patch('gifts/{id}', 'updateAttributeGift')->name('updateAttributeGift');
        Route::delete('gifts/{id}', 'deleteGift')->name('deleteGift');
        Route::post('gifts/{id}/redeem', 'redeemGifts')->name('redeemGifts');
        Route::post('gifts/redeem', 'redeemGiftsBulk')->name('redeemGiftsBulk');
        Route::post('gifts/{id}/rating', 'ratingGifts')->name('ratingGifts');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::post('me', 'me');
    });
});
