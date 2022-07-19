<?php

use App\Http\Controllers\AddProduct;
use App\Http\Controllers\GetBanner;
use App\Http\Controllers\GetProductImage;
use App\Http\Controllers\GetProductList;
use App\Http\Controllers\Login;
use App\Http\Controllers\Logout;
use App\Http\Controllers\Register;
use App\Http\Controllers\Reverify;
use App\Http\Controllers\SetBanner;
use App\Http\Controllers\UserResponse;
use App\Http\Controllers\Verify;
use App\Http\Middleware\StoreTime;
use App\Http\Middleware\VerifyJwtToken;
use App\Http\Middleware\withoutVerifyTempToken;
use Illuminate\Support\Facades\Route;

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

Route::middleware([StoreTime::class])->group(function () {
    Route::post('/register', [Register::class, 'register']);
    Route::post('/login', [Login::class, 'login']);
    Route::get('/verify', [Verify::class, 'verify']);
    Route::get('/get_product_list', [GetProductList::class, 'getProductList']);
    Route::get('/get_banner/{id}', [GetBanner::class, 'getBanner']);
    Route::get('/get_product_image/{product_id}/{value}', [GetProductImage::class, 'getProductImage']);
    Route::middleware([VerifyJwtToken::class])->group(function () {
        Route::get('/user_response', [UserResponse::class, 'userResponse']);
        Route::get('/logout', [Logout::class, 'logout']);
        Route::post('/set_banner', [SetBanner::class, 'setBanner']);
        Route::post('/add_product', [AddProduct::class, 'addProduct']);

        // Route::post('/set_user', [SetUser::class, 'setUser']);
        // Route::post('/update_page_details', [UpdatePageDetails::class, 'UpdatePageDetails']);
        // Route::post('/upload_post', [UploadPost::class, 'uploadPost']);
        // Route::post('/update_post_details', [UpdatePostDetails::class, 'updatePostDetails']);
        // Route::post('/add_page', [AddPage::class, 'addPage']);
        // Route::get('/check_username', [VerifyUsername::class, 'verifyUsername']);
        // Route::get('/getImage/{id}', [GetProfilePic::class, 'getProfilePic']);
        // Route::get('/post/{id}', [GetPost::class, 'getPost']);
        // Route::get('/thumbnail/{id}', [GetPostThumbnail::class, 'getPostThumbnail']);
        // Route::get('/getPage/{username}', [GetPage::class, 'getPage']);
        // Route::get('/get_post_details/{id}', [GetPostDetails::class, 'getPostDetails']);
        // Route::get('/getPageList', [GetPageList::class, 'getPageList']);
        // Route::get('/getAllPost', [GetAllPost::class, 'getAllPost']);

    });
    Route::middleware([withoutVerifyTempToken::class])->group(function () {
        Route::get('/reverify', [Reverify::class, 'reverify']);
    });
});
