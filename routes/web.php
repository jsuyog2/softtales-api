<?php

use App\Http\Controllers\GetBanner;
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
    Route::get('/getBanner/{id}', [GetBanner::class, 'getBanner']);
    Route::middleware([VerifyJwtToken::class])->group(function () {
        Route::get('/userresponse', [UserResponse::class, 'userResponse']);
        Route::get('/logout', [Logout::class, 'logout']);
        Route::post('/setBanner', [SetBanner::class, 'setBanner']);

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
