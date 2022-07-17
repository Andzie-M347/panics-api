<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [AuthController::class, 'create_user']);
Route::post('login', [AuthController::class, 'log_in']);

Route::middleware('auth:sanctum')->group( function () {
    Route::post('send-panic/{id}', [AuthController::class, 'send_panic']);
    Route::post('cancel-panic/{id}', [AuthController::class, 'cancel_panic']);
    Route::get('panics', [AuthController::class, 'get_panic']);
});
