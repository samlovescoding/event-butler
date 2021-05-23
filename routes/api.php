<?php

use App\Http\Controllers\Admin\AppointmentsController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/register", RegisterController::class);
Route::post("/login", LoginController::class)->name("login");

Route::group(["middleware" => "jwt", "prefix" => "/admin"], function () {
    Route::resource("/events", EventsController::class);
    Route::resource("/appointments", AppointmentsController::class);
});
