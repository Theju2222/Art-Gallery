<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Customer\EnqueryController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/foo', function () {
    \Artisan::call('storage:link');
});

Route::get('/fee', function () {
    \Artisan::call('make:model Room');
});


Route::get('/blogs', function () {
    return view('blogs');
})->name('voyager.blogs.index');

Route::get('/photos', function () {
    return view('photos');
})->name('voyager.photos.index');

Route::get('/events', function () {
    return view('events');
})->name('voyager.events.index');

Route::get('/enquery', [EnqueryController::class, 'public_link']);
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('/enquries/detail', [EnqueryController::class, 'enquery_details'])->name('enquery.detail')->middleware('admin.user');
});