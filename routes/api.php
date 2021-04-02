<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
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

Route::middleware(['guest'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});


Route::prefix('v1')->middleware(['jwt.verify'])->group(function () {
    Route::post('email', [EmailController::class, 'send']);
    Route::get('email', [EmailController::class, 'index']);
    Route::get('email/{id}', [EmailController::class, 'show']);
});
