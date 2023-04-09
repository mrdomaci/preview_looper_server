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
Route::get('/addon-uninstall', [ClientController::class, 'uninstall'])->name('client.uninstall');
Route::get('/addon-deactivate', [ClientController::class, 'deactivate'])->name('client.deactivate');
Route::get('/addon-activate', [ClientController::class, 'activate'])->name('client.activate');

Route::get('/images/{clientId}/{productIds}', [ImageController::class, 'list'])->name('images.list');