<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ImageController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/addon-install', [ClientController::class, 'install'])->name('client.install');

Route::get('/images/{clientId}/{productIds}', [ImageController::class, 'list'])->name('images.list');