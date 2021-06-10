<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\MainController;

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

Route::get('/', function(){
    return redirect('/admin/login');
})->name('login');

Route::group(['prefix' => 'admin'], function () {
    Route::get('/login',[MainController::class, 'login'])->name('admin.login');
    Route::post('/login',[MainController::class, 'authenticate'])->name('admin.authenticate');
    
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/',[MainController::class, 'dashboard'])->name('admin.dashboard');
        Route::resource('categories', 'Admin\Categories');
        Route::resource('{categories_id}/lesson', 'Admin\Lesson');
        Route::resource('{categories_id}/{lesson_id}/questions', 'Admin\Questions');
        Route::resource('{categories_id}/{lesson_id}/{question}/answers', 'Admin\Answers');

        
    
    });
}); 