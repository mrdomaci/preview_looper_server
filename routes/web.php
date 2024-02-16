<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductCategoryRecommendationController;
use App\Http\Controllers\ProductController;
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
Route::post('/client-sync/{country}/{serviceUrlPath}/{language}/{eshopId}',[ClientController::class, 'sync'])->name('client.sync');
Route::get('/products/{clientId}/{name}', [ProductController::class, 'getData'])->name('products.getData');
Route::post('/recommendation/{country}/{serviceUrlPath}/{language}/{eshopId}', [ProductCategoryRecommendationController::class, 'add'])->name('recommendation.add');
Route::delete('/recommendation/{country}/{serviceUrlPath}/{language}/{eshopId}', [ProductCategoryRecommendationController::class, 'delete'])->name('recommendation.delete');

Route::get('/addon-uninstall/{serviceUrlPath}', [ClientController::class, 'uninstall'])->name('client.uninstall');
Route::get('/addon-deactivate/{serviceUrlPath}', [ClientController::class, 'deactivate'])->name('client.deactivate');
Route::get('/addon-activate/{serviceUrlPath}', [ClientController::class, 'activate'])->name('client.activate');

Route::get('/locale/{locale}', [HomepageController::class, 'setLocale'])->name('homepage.setLocale');

Route::get('/images/{eshopID}/{moduloCheck}', [ImageController::class, 'all'])->name('images.all')->middleware('cors');
Route::get('/products/{ehsopID}/{moduloCheck}/{guids}', [ProductController::class, 'recommend'])->name('products.all')->middleware('cors');