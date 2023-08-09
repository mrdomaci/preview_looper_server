<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomepageController;
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

Route::get('/', [HomepageController::class, 'index'])->name('welcome');
Route::get('/plugin/{serviceUrlPath}', [HomepageController::class, 'plugin'])->name('plugin');
Route::get('/plugin/{serviceUrlPath}/terms', [HomepageController::class, 'terms'])->name('terms');

Route::get('/addon-install/{country}/{serviceUrlPath}', [ClientController::class, 'install'])->name('client.install');
Route::get('/client-settings/{country}/{serviceUrlPath}',[ClientController::class, 'settings'])->name('client.settings');
Route::post('/client-settings/{country}/{serviceUrlPath}/{language}/{eshopId}',[ClientController::class, 'saveSettings'])->name('client.saveSettings');

Route::get('/addon-uninstall/{serviceUrlPath}', [ClientController::class, 'uninstall'])->name('client.uninstall');
Route::get('/addon-deactivate/{serviceUrlPath}', [ClientController::class, 'deactivate'])->name('client.deactivate');
Route::get('/addon-activate/{serviceUrlPath}', [ClientController::class, 'activate'])->name('client.activate');

Route::get('/locale/{locale}', [HomepageController::class, 'setLocale'])->name('homepage.setLocale');

Route::get('/images/{clientId}/{productIds}', [ImageController::class, 'list'])->name('images.list')->middleware('cors');