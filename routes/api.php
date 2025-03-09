<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify', [AuthController::class, 'verify']);
Route::post('/login', [AuthController::class, 'login']);
