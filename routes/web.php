<?php

declare(strict_types=1);

use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InstallController;
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

// Presentation
Route::get('/', [HomepageController::class, 'index'])->name('welcome');
Route::get('/plugin/{serviceUrlPath}', [HomepageController::class, 'plugin'])->name('plugin');
Route::get('/plugin/{serviceUrlPath}/terms', [HomepageController::class, 'terms'])->name('terms');

// Locale
Route::get('/locale/{locale}', [HomepageController::class, 'setLocale'])->name('homepage.setLocale');

// Installation
Route::get('/addon-install/{country}/{serviceUrlPath}', [InstallController::class, 'install'])->name('client.install');
Route::get('/addon-uninstall/{serviceUrlPath}', [InstallController::class, 'uninstall'])->name('client.uninstall');
Route::get('/addon-deactivate/{serviceUrlPath}', [InstallController::class, 'deactivate'])->name('client.deactivate');
Route::get('/addon-activate/{serviceUrlPath}', [InstallController::class, 'activate'])->name('client.activate');

// Settings
Route::get('/client-settings/{country}/{serviceUrlPath}', [ClientController::class, 'settings'])->name('client.settings');
Route::post('/client-settings/{country}/{serviceUrlPath}/{language}/{eshopId}', [ClientController::class, 'saveSettings'])->name('client.saveSettings');

// Data sync
Route::post('/client-sync/{country}/{serviceUrlPath}/{language}/{eshopId}', [ClientController::class, 'sync'])->name('client.sync');

// Produts autocomplete
Route::get('/products/{clientId}/{name}', [ProductController::class, 'getData'])->name('products.getData');

// Recommendation settings
Route::post('/recommendation/{country}/{serviceUrlPath}/{language}/{eshopId}', [ProductCategoryRecommendationController::class, 'add'])->name('recommendation.add');
Route::delete('/recommendation/{country}/{serviceUrlPath}/{language}/{eshopId}', [ProductCategoryRecommendationController::class, 'delete'])->name('recommendation.delete');

// Ajax endpoints
Route::get('/images/{eshopID}/{moduloCheck}', [ImageController::class, 'all'])->name('images.all')->middleware('cors');
Route::get('/products/{ehsopID}/{moduloCheck}/{guids}', [ProductController::class, 'recommend'])->name('products.all')->middleware('cors');
