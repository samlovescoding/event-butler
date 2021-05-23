<?php

use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\AppointmentsController;
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

// Admin Routes
Route::post("/register", RegisterController::class);
Route::post("/login", LoginController::class)->name("login");
Route::resource("/admin/events", EventsController::class)->middleware("jwt");

// Customer Routes
Route::get('/events', [EventsController::class, 'index']);
Route::get('/events/{event}', [EventsController::class, 'show']);
Route::resource("/event/{event}/appointments", AppointmentsController::class);
