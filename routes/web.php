<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScriptController;
use App\Http\Controllers\Auth\LoginController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/script',[App\Http\Controllers\ScriptController::class,'index']);
// Route::post('/script',[App\Http\Controllers\ScriptController::class,'store']);
// Route::get('/u-script/{id}',[App\Http\Controllers\ScriptController::class,'script']);

Auth::routes();

Route::get('/',[LoginController::class,'index']);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('script', ScriptController::class)->middleware('auth');
Route::get('/script/u-script/{id}',[ScriptController::class,'runScript'])->name('script.run');
Route::get('/script/u-script',[App\Http\Controllers\ScriptController::class,'runScript']);

Route::get('/clear', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return "Cache is cleared";
});
