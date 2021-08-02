<?php

use App\Http\Controllers\TopicsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\RepliesController;
use App\Http\Controllers\NotificationsController;
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

Route::get('/',[TopicsController::class,'index'])->name('root');

Auth::routes(['verify'=>true]);

Route::resource('users',UsersController::class,['only'=>['show','update','edit']]);

Route::resource('topics', TopicsController::class, ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);

Route::resource('categories',CategoriesController::class,['only'=>['show']]);

Route::post('upload_image',[TopicsController::class,'uploadImage'])->name('topics.upload_image');

Route::get('topics/{topic}/{slug?}',[TopicsController::class,'show'])->name('topics.show');

Route::resource('replies', RepliesController::class, ['only' => [ 'store','destroy']]);

Route::resource('notifications',NotificationsController::class,['only'=>['index']]);

Route::get('permission-denied',[PagesController::class,'permissionDenied'])->name('permission-denied');
