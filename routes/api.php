<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\HomeController;
use App\Http\Controllers\Api\v1\ProductController;
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
Route::post('signup1', [AuthController::class, "signup"]);


Route::post('signup', [AuthController::class, "signup"]);
Route::post('login', [AuthController::class, "login"]);
Route::post('social-signup', [AuthController::class, "socialSignup"]);

Route::get('home', [HomeController::class, "index"]);
Route::post('get-comic', [HomeController::class, "getComic"]);
Route::post('comic-details', [HomeController::class, "comicDetails"]);

Route::post('get-product', [ProductController::class, "getProduct"]);
Route::post('product-details', [ProductController::class, "productdetails"]);
Route::post('send-email-forgot-password', [AuthController::class, "sendEmailForgotPassword"]);

Route::post('get-publisher', [HomeController::class, "getPublisher"]);
Route::post('publisher-details', [HomeController::class, "publisherDetails"]);

Route::post('pushtest', [AuthController::class, "testPush"]);

Route::group(['middleware' => ['auth:api']], function()
{
    Route::post('logout', [AuthController::class, "logout"]);
    Route::post('delete-account', [AuthController::class, "deleteAccount"]);
    Route::post('changePassword', [AuthController::class, "changePassword"]);
    Route::post('changeProfile', [AuthController::class, "changeProfile"]);
    Route::get('get-address', [AuthController::class, "getAdress"]);
    Route::post('add-address', [AuthController::class, "addAdress"]);

    Route::post('rating', [HomeController::class, "rating"]);

    Route::post('notify', [HomeController::class, "Notify"]);
    Route::post('create-stripe-customer', [ProductController::class, "createStripeCustomer"]);
    Route::post('order', [ProductController::class, "order"]);
    Route::post('my-order', [ProductController::class, "myOrder"]);
    Route::post('order-details', [ProductController::class, "orderDetails"]);

    Route::post('my-library', [HomeController::class, "myLibrary"]);
    Route::post('delete-library', [HomeController::class, "deleteLibrary"]);
    Route::post('user-comic', [HomeController::class, "userComic"]);


    Route::post('subscription', [AuthController::class, "subscription"]);

    Route::post('get-notifications', [AuthController::class, "getNotification"]);
    Route::post('delete-notification', [AuthController::class, "deleteNotification"]);
    Route::post('delete-all-notification', [AuthController::class, "deleteAllNotification"]);

    Route::post('cancel-subscription', [AuthController::class, "cancelSubscription"]);
    Route::post('user-subscription-get', [AuthController::class, "userSubscriptionGet"]);


    Route::post('get-wallet-history', [AuthController::class, "getWalletHistory"]);
    Route::post('purchase-coins', [AuthController::class, "purchaseCoins"]);

    Route::post('episode-details', [HomeController::class, "episodeDetails"]);

});
