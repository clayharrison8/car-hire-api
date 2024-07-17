<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;

Route::get('/cars', [CarController::class, 'index']);

Route::put('/cars/{id}', [CarController::class, 'update']);

Route::delete('/cars/{id}', [CarController::class, 'destroy']);

Route::post('/cars/{id}/hire', [CarController::class, 'hire']);