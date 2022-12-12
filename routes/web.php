<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaterkitController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComicController;
use App\Http\Controllers\Admin\EpisodeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\WebController;
use App\Http\Controllers\Front\AuthfrontController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\CustomerModuleController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Front\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Front\PublisherController as PublisherControllerFront;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
*/

//Cron Routes
Route::get("renewSubscription", [CronController::class, "renewSubscription"]);
Route::post("renewSubscriptionIos", [CronController::class, "renewSubscriptionIos"]);
Route::post("renewSubscriptionAndroid", [CronController::class, "renewSubscriptionAndroid"]);
Route::get('pushNotificationCron', [CronController::class, "pushNotificationCron"]);

Auth::routes();
Route::get('/', function(){
    return redirect('/');
});

Route::get("test", [CustomerModuleController::class, "test"]);

Route::get('/comic/{id}', function(){ echo "deeplink"; });

Route::get('/clear', function () {
    Artisan::call('cache:clear');
	Artisan::call('config:cache');
	Artisan::call('route:cache');
    return "Cache is cleared";
});
Route::get('/subscription-success/{id}',[CustomerModuleController::class,'subscriptionSuccess']);

//Customers Routes
Route::get('auth/google',[AuthfrontController::class,'redirect'])->name('google-auth');
Route::get('auth/google/call-back',[AuthfrontController::class,'callbackGoogle']);

Route::get('auth/facebook',[AuthfrontController::class,'fbRedirect'])->name('facebook-auth');
Route::get('callbackFromFacebook',[AuthfrontController::class,'callbackFromFacebook']);

Route::get('/r/{refer}',[HomeController::class,'refer'])->name("refer");

Route::get('/',[HomeController::class,'home'])->name("home");
Route::get('/home',[HomeController::class,'home'])->name("home-page");
Route::get('/comic-detail/{id}',[HomeController::class,'comicdetail'])->name("comic-detail");
Route::get('/back-comic-detail/{comic_id}/{episode_id}/{page_no}',[HomeController::class,'backComicdetail'])->name("back-comic-detail");

    Route::get('/login', [AuthfrontController::class, 'login'])->name('front-login-view');
    Route::get('/login-publisher', [AuthfrontController::class, 'loginPublisher'])->name('front-login-view-publisher');
    Route::post('loginFrontPublisher', [AuthfrontController::class, 'loginFrontPublisher'])->name('front-login-Publisher');

    Route::get('/logout', [AuthfrontController::class, 'logout'])->name('front-logout');
    Route::post('loginFront', [AuthfrontController::class, 'loginFront'])->name('front-login-action');

    Route::get('/signup', [AuthfrontController::class, 'signup'])->name('front-signup-view');
    Route::get('/signup-publisher', [AuthfrontController::class, 'signupPublisher'])->name('front-signup-view-publisher');
    Route::post('front-signup-publisher',[AuthfrontController::class,'frontSignupPublisher'])->name('front-signup-publisher');

    Route::post('signupFront', [AuthfrontController::class, 'signupFront'])->name('front-signup-action');
    Route::post('send-email-forgot-password', [AuthfrontController::class, "sendEmailForgotPassword"])->name("send-email-forgot-password");
    Route::get('reset-password/{token}', [AuthfrontController::class, 'resetPassword']);
    Route::post('reset-passwords', [AuthfrontController::class, "resetPasswords"])->name("reset-passwords");




Route::get('/genres/{id}',[CustomerDashboardController::class,'genres'])->name('genres');
Route::post('/get-comic',[CustomerDashboardController::class,'getComic'])->name('get-comic');
Route::get('/store',[CustomerDashboardController::class,'store'])->name('store');
Route::post('/get-product',[CustomerDashboardController::class,'getProduct'])->name('get-product');
Route::get('/product-details/{id}',[CustomerDashboardController::class,'productDetails'])->name("product-details");

Route::get('/product-details-new/{id}',[CustomerDashboardController::class,'productDetailsNew'])->name("product-details-new");


Route::get('/search-comics',[CustomerDashboardController::class,'searchComics']);

Route::get('/all-comic',[CustomerDashboardController::class,'allComic'])->name('all-comic');
Route::get('privacy-policy/{id}',[CustomerDashboardController::class,'privacyPolicy'])->name('privacy-policy');
Route::get('term-conditions/{id}',[CustomerDashboardController::class,'termCondition'])->name('term-conditions');
Route::get('copyright',[CustomerDashboardController::class,'copyright'])->name('copyright');
Route::get('cookie-policy',[CustomerDashboardController::class,'cookiePolicy'])->name('cookie-policy');
Route::get('cancellation-policy',[CustomerDashboardController::class,'cancellationPolicy']);

Route::get('publisher',[CustomerDashboardController::class,'publisher'])->name('publisher');
Route::post('get-publisher',[CustomerDashboardController::class,'getPublisher'])->name('get-publisher');
Route::get('publisher-profile/{id}',[CustomerDashboardController::class,'publisherprofile'])->name('publisher-profile');

Route::get('about',[CustomerDashboardController::class,'about'])->name('about');
Route::post('selectedComicget',[CustomerModuleController::class,'selectedComicget'])->name('selectedComicget');

Route::middleware(['checkCustomer'])->group( function () {
    Route::prefix('customer')->group(function () {
        Route::middleware(['auth:web'])->group( function ()
        {

            Route::post('/notify',[CustomerDashboardController::class,'notify'])->name('notify');
            Route::post('/rating',[CustomerDashboardController::class,'rating'])->name('rating');
            Route::get('/my-account',[CustomerModuleController::class,'myAccount'])->name('my-account');
            Route::get('/my-order',[CustomerModuleController::class,'myOrder'])->name('my-order');
            Route::get('/subscription-page',[CustomerModuleController::class,'subscriptionPage'])->name('subscription-page');
            Route::post('/subscription-month',[CustomerModuleController::class,'subscriptionMonth'])->name('subscription-month');
            Route::post('/subscription-year',[CustomerModuleController::class,'subscriptionYear'])->name('subscription-year');

            Route::get('/cancel-subscription',[CustomerModuleController::class,'cancelSubscription'])->name('cancel-subscription');
            Route::get('/reactive-cancel-subscription',[CustomerModuleController::class,'reactiveCancelSubscription'])->name('reactive-cancel-subscription');

            Route::get('/my-library',[CustomerModuleController::class,'myLibrary'])->name('my-library');
            Route::post('/delete-library',[CustomerModuleController::class,'deleteLibrary'])->name('delete-library');


            Route::get('/buy-now',[CustomerDashboardController::class,'buyNow'])->name('buy-now');
            Route::post('update-cart',[CustomerDashboardController::class,'updateCart'])->name('update-cart');
            Route::post('add-to-cart',[CustomerModuleController::class,'addToCart'])->name('add-to-cart');
            Route::post('/checkout',[CustomerDashboardController::class,'checkOut'])->name('checkout');
            Route::post('/add-payment-method',[CustomerModuleController::class,'addPaymentMethod'])->name('add-payment-method');

            
            Route::post('/purchase-coins',[CustomerModuleController::class,'purchaseCoins'])->name('purchase-coins');

            Route::post('/get-library',[CustomerModuleController::class,'getLibrary'])->name('get-library');
            Route::post('update-profile',[CustomerModuleController::class,'updateProfile'])->name('update-profile');

            Route::post('change-password',[CustomerModuleController::class,'changeProfilePassword'])->name('change-password');
            Route::get('order-detail/{id}',[CustomerDashboardController::class,'orderDetail'])->name('order-detail');
            Route::post('delete-account',[CustomerDashboardController::class,'deleteAccount'])->name('delete-account');
            Route::post('forgot-password',[CustomerDashboardController::class,'forgotPassword'])->name('forgot-password');

            Route::get('my-coins', [CustomerDashboardController::class, 'userCoins'])->name('my-coins');

            Route::get('/episode-detail/{id}/{comic_id}/{view}',[HomeController::class,'episodedetail'])->name("episode-detail");

            Route::post('hide-notification',[CustomerDashboardController::class,'hidenotification'])->name('hide-notification');
            Route::post('count-notification',[CustomerDashboardController::class,'countNotification'])->name('count-notification');

        });
    });
});

//Publisher Routes
Route::middleware(['checkPublisher'])->group( function () {
    Route::prefix('publisher')->group(function () {
        Route::middleware(['auth:web'])->group( function ()
        {

            Route::get('/my-dashboard',[PublisherControllerFront::class,'myDashboard'])->name("my-dashboard");
            Route::get('/my-wallet',[PublisherControllerFront::class,'myWallet'])->name("my-wallet");
            Route::post('payout-request', [PublisherControllerFront::class, 'payoutRequest']);
            Route::post('upload-document', [PublisherControllerFront::class, 'uploadDocument'])->name("upload-document");;
            Route::get('/my-accounts',[PublisherControllerFront::class,'myAccount'])->name("my-accounts");
            Route::get('/my-referral',[PublisherControllerFront::class,'myReferral'])->name("my-referral");
            Route::get('/my-comic',[PublisherControllerFront::class,'myComic'])->name("my-comic");
            Route::get('get-comics', [PublisherControllerFront::class, 'getComics']);
            Route::post('insert-comics', [ComicController::class, 'insert']);
            Route::post('update-comics', [ComicController::class, 'update']);
            Route::post('delete-comics', [ComicController::class, 'delete']);
            Route::get('get-category-by-name', [CategoryController::class, 'getcategorybyname']);

            Route::get('view-comics/{id}',[PublisherControllerFront::class,'viewComics']);

            Route::get('get-episode',[PublisherControllerFront::class,'getEpisode']);
            Route::get('add-episode/{id}',[PublisherControllerFront::class,'index']);
            Route::post('insert-episode',[EpisodeController::class,'insert']);
            Route::get('edit-episode/{id}',[PublisherControllerFront::class,'editEpisode']);
            Route::post('update-episode',[EpisodeController::class,'update']);
            Route::post('delete-episode',[EpisodeController::class,'delete']);
            Route::post('upload-comic-pdf',[EpisodeController::class,'uploadComicPdf']);
            Route::post('change-password',[CustomerModuleController::class,'changeProfilePassword'])->name('change-password');
            Route::post('publisher-update-profile',[PublisherControllerFront::class,'updateProfile'])->name('publisher-update-profile');
        });
    });
});


//Admin Routes
Route::get('/admin', function(){
    return redirect('/admin/login');
});

Route::get('admin/login', [AuthController::class, 'login'])->name('admin-login-view');
Route::get('admin/logout', [AuthController::class, 'logout'])->name('admin-logout');
Route::post('admin/loginAdmin', [AuthController::class, 'loginAdmin'])->name('admin-login-action');
Route::middleware(['checkAdmin'])->group( function () {
    Route::prefix('admin')->group(function () {
        Route::middleware(['auth:admin'])->group( function ()
        {
            Route::get('/', [DashboardController::class, 'home'])->name('home');
            Route::get('home', [DashboardController::class, 'home'])->name('home');
            Route::get('home', [DashboardController::class, 'home'])->name('dashboard');

            Route::get('category', [CategoryController::class, 'index']);
            Route::get('get-category', [CategoryController::class, 'get']);
            Route::get('get-category-by-name', [CategoryController::class, 'getcategorybyname']);
            Route::post('insert-category', [CategoryController::class, 'insert']);
            Route::post('update-category', [CategoryController::class, 'update']);
            Route::post('delete-category', [CategoryController::class, 'delete']);

            Route::get('comic',[ComicController::class, 'index']);
            Route::get('pending-comic',[ComicController::class, 'pendingComic']);
            Route::get('pending-episode',[ComicController::class, 'pendingEpisodes']);
            Route::get('get-comics', [ComicController::class, 'get']);
            Route::get('get-pendingcomics', [ComicController::class, 'getpendingcomic']);
            Route::get('getpendingEpisode',[ComicController::class, 'getpendingEpisodes']);


            Route::post('comicstatuschange',[ComicController::class,'comicStatusChange']);
            Route::get('view-comics/{id}',[ComicController::class,'viewComics']);
            Route::post('insert-comics', [ComicController::class, 'insert']);
            Route::post('update-comics', [ComicController::class, 'update']);
            Route::post('delete-comics', [ComicController::class, 'delete']);

            Route::get('get-episode',[EpisodeController::class,'get']);
            Route::get('add-episode/{id}',[EpisodeController::class,'index']);
            Route::post('insert-episode',[EpisodeController::class,'insert']);
            Route::get('edit-episode/{id}',[EpisodeController::class,'editEpisode']);
            Route::post('update-episode',[EpisodeController::class,'update']);
            Route::post('delete-episode',[EpisodeController::class,'delete']);
            Route::post('upload-comic-pdf',[EpisodeController::class,'uploadComicPdf']);

            //user view
            Route::get('user',[UserController::class,'index']);
            Route::get('get-user',[UserController::class,'get']);
            Route::get('view-user/{id}',[UserController::class,'viewuser']);
            Route::post('statuschange',[UserController::class,'statuschange']);
            //user review here
            Route::get('get-user-ratings',[UserController::class,'getReview']);
            Route::get('get-user-library',[UserController::class,'getLibrary']);
            Route::get('get-user-comic',[UserController::class,'getComic']);
            Route::get('get-user-coin',[UserController::class,'getCoin']);
            Route::post('add-coins',[UserController::class,'addCoins']);
            //end user review

            //here admin profile page
            Route::get('profile',[AuthController::class,'profile']);

            Route::post('update-profile',[AuthController::class,'update']);
            Route::post('update-password', [AuthController::class, 'updatepass']);
            //here we start the product
            Route::get('product',[ProductController::class, 'index']);
            Route::get('get-product', [ProductController::class, 'get']);
            Route::get('insert-product-view', [ProductController::class, 'insertProductView']);
            Route::post('insert-product', [ProductController::class, 'insert']);
            Route::get('edit-product/{id}',[ProductController::class,'editProduct']);
            Route::post('update-product', [ProductController::class, 'update']);
            Route::post('delete-product',[ProductController::class,'delete']);
            Route::get('get-comic-by-name', [ProductController::class, 'getcomicbyname']);
            //delete particular image when we edit
            Route::post('delete-image',[ProductController::class,'deleteimage']);

             //order modules
            Route::get('order',[OrderController::class, 'index']);
            Route::get('get-orders', [OrderController::class, 'get']);
            Route::get('view-orders/{id}',[OrderController::class,'viewOrders']);
            Route::post('order-status', [OrderController::class,'orderStatus']);
            Route::post('insert-order-note', [OrderController::class, 'insertOrderNote']);
            Route::get('get-orders-notes', [OrderController::class, 'getOrderNotes']);

            //here plans module routes
            Route::get('plan', [PlanController::class, 'index']);
            Route::get('get-plan', [PlanController::class, 'get']);
            Route::post('update-plan', [PlanController::class, 'update']);

            // here revenue module routes
            Route::get('revenue',[PayoutController::class,'indexrevenue']);
            Route::get('get-revenue', [PayoutController::class, 'getrevenue']);


            //here setting module routes
            Route::get('setting',[SettingController::class, 'index']);
            Route::post('update-setting', [SettingController::class, 'update']);
            //banner setting module routes
            Route::get('banner',[WebController::class, 'index']);
            Route::get('get-banners', [WebController::class, 'get']);
            Route::post('insert-banners', [WebController::class, 'insert']);
            Route::post('update-banners', [WebController::class, 'update']);
            Route::post('delete-banners', [WebController::class, 'delete']);
            //here notification route
            Route::get('notification',[NotificationController::class,'index']);
            Route::get('get-notification', [NotificationController::class, 'get']);
            Route::post('insert-notification', [NotificationController::class, 'insertNotification']);
            Route::post('update-notification',[NotificationController::class, 'updateNotification']);

            //here comic active route
            //admin/active-comic
            // Route::get('notification',[NotificationController::class,'index']);admin/publisher
            Route::get('publisher',[PublisherController::class, 'index']);
            Route::get('pendingpublisher',[PublisherController::class, 'pendingIndex']);
            Route::get('get-publisher',[PublisherController::class,'get']);
            Route::get('get-pending-publisher',[PublisherController::class, 'getPendingPublisher']);
            Route::post('publisherstatuschange',[PublisherController::class,'publisherStatusChange']);
            Route::post('docs-status',[PublisherController::class,'docsStatus'])->name('docs-status');
            Route::post('add-lic-docs-notes',[PublisherController::class,'rejectlicDocsForm'])->name('add-lic-docs-notes');
            Route::post('add-tax-docs-notes',[PublisherController::class,'rejecttaxDocsForm'])->name('add-tax-docs-notes');
            Route::get('view-publisher/{id}',[PublisherController::class,'viewPublisher']);
            Route::get('get-publisher-comics', [PublisherController::class, 'getPublisherComic']);

            Route::get('payout',[PayoutController::class,'index']);
            Route::get('get-payout', [PayoutController::class, 'get']);
            Route::post('update-payout', [PayoutController::class, 'update']);
            Route::post('delete-payout', [PayoutController::class, 'delete']);
        });
    });
});
// locale Route
Route::get('lang/{locale}', [LanguageController::class, 'swap']);
