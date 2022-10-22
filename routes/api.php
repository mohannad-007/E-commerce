<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CouponGiftController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ReplyCommentController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WishListController;
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

Route::group(['prefix' => 'auth'], function () {
    Route::post('createAccount/{type_account}', [AccountController::class, 'createAccount']);
    Route::post('login', [AccountController::class, 'login']);
    Route::post('createAdmin', [AdminController::class, 'createAccount']);
    Route::post('loginAdmin', [AdminController::class, 'login']);
});

Route::middleware(['auth:api'])->group(function () {

    Route::group(['prefix' => 'products'], function () {
        Route::get('/list', [ProductController::class, 'index']);
        Route::get('/show/{product_id}', [ProductController::class, 'show']);
        Route::put('/edit/{product_id}', [ProductController::class, 'update']);
        Route::post('/add/{type_product}', [ProductController::class, 'store']);
        Route::delete('/remove/{product_id}', [ProductController::class, 'destroy']);
        Route::delete('/buy/{product_id}', [ProductController::class, 'buy']);
        Route::get('/search', [ProductController::class, 'search']);
        Route::get('/like/{product_id}', [LikeController::class, 'like']);
        Route::get('/order/{request}', [ProductController::class, 'order']);
        Route::get('/used/', [ProductController::class, 'UsedProducts']);
        Route::get('/buyCart/{cart_id}', [ProductController::class, 'buyCart']);
        Route::post('/storeRate/{product_id}', [RateController::class, 'store']);
    });

    Route::group(['prefix' => 'comments'], function () {
        Route::put('/edit/{comment_id}', [CommentController::class, 'update']);
        Route::post('/add/{product_id}', [CommentController::class, 'store']);
        Route::delete('/remove/{comment_id}', [CommentController::class, 'destroy']);
    });

    Route::group(['prefix' => 'reply_comments'], function () {
        Route::put('/edit/{reply_id}', [ReplyCommentController::class, 'update']);
        Route::post('/add/{comment_id}', [ReplyCommentController::class, 'store']);
        Route::delete('/remove/{reply_id}', [ReplyCommentController::class, 'destroy']);
    });

    Route::group(['prefix' => 'carts'], function () {
        Route::get('/list/', [CartController::class, 'index']);
        Route::post('/add/{product_id}', [CartController::class, 'store']);
        Route::delete('/remove/{cart_id}', [CartController::class, 'destroy']);
        Route::delete('/edit/{cart_id}/{product_id}', [CartController::class, 'edit']);
    });

    Route::group(['prefix' => 'wishList'], function () {
        Route::get('/list/', [WishListController::class, 'index']);
        Route::post('/add/{product_id}', [WishListController::class, 'store']);
        Route::delete('/remove/{wishList_id}', [WishListController::class, 'destroy']);
        Route::delete('/edit/{wishList_id}/{product_id}', [WishListController::class, 'edit']);
    });

    Route::group(['prefix' => 'request'], function () {
        Route::get('/list/', [WalletController::class, 'index']);
        Route::post('/add/', [WalletController::class, 'store']);
        Route::put('/edit/{request_id}', [WalletController::class, 'update']);
        Route::post('/store/{request_id}', [WalletController::class, 'accept']);
        Route::delete('/remove/{request_id}', [WalletController::class, 'destroy']);
    });

    Route::group(['prefix' => 'shipping'], function () {
        Route::get('/list/', [ShippingController::class, 'showAll']);
        Route::get('/receive/', [ShippingController::class, 'receive']);
        Route::get('/delivery/', [ShippingController::class, 'delivery']);
        Route::put('/updateBranch/{branch_id}', [ShippingController::class, 'updateBranch']);
        Route::delete('/remove/{shipping_id}', [ShippingController::class, 'destroy']);
        Route::put('/receiveDone/{shipping_id}', [ShippingController::class, 'receiveDone']);
        Route::delete('/deliveryDone/{shipping_id}', [ShippingController::class, 'deliveryDone']);
    });

    Route::group(['prefix' => 'coupons'], function () {
        Route::get('/index/', [CouponController::class, 'index']);
        Route::get('/useCoupon/{cart_id}', [ProductController::class, 'useCoupon']);
        Route::get('/all/', [CouponController::class, 'all']);
        Route::post('/create/{account_id}', [CouponController::class, 'create']);
        Route::get('/show/{coupon_id}', [CouponController::class, 'show']);
        Route::put('/update/{coupon_id}', [CouponController::class, 'update']);
        Route::delete('/delete/{coupon_id}', [CouponController::class, 'destroy']);
    });

    Route::group(['prefix' => 'notification'], function () {
        Route::get('/show/', [NotificationController::class, 'index']);
        Route::get('/sendEmail/', [MailController::class, 'sendEmail']);
        Route::delete('/remove/{notification_id}', [NotificationController::class, 'destroy']);
    });

    Route::group(['prefix' => 'couponsGifts'], function () {
        Route::get('/index/', [CouponGiftController::class, 'index']);
        Route::post('/create/', [CouponGiftController::class, 'create']);
        Route::get('/useCouponGift/{cart_id}', [ProductController::class, 'useCouponGift']);
    });

    Route::group(['prefix' => 'adds'], function () {
        Route::get('/list/', [AddController::class, 'index']);
        Route::post('/store/', [AddController::class, 'store']);
        Route::get('/show/{id}', [AddController::class, 'show']);
        Route::put('/update/{id}', [AddController::class, 'update']);
        Route::delete('/remove/{id}', [AddController::class, 'destroy']);
    });

    Route::group(['prefix' => 'booking'], function () {
        Route::put('/book/{product_id}', [ProductController::class, 'book']);
        Route::get('/showBook/', [ProductController::class, 'showBook']);
        Route::get('/showbuy/', [ProductController::class, 'showbuy']);
        Route::put('/returnProduct/{product_id}', [ProductController::class, 'returnProduct']);
    });

});



