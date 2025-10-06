<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ListingController::class, 'index']);

Route::post('/users', [UserController::class, 'store']);

Route::post('users/authenticate', [UserController::class, 'authenticate']);

Route::middleware('guest')->group(function () {
    Route::get('/register', [UserController::class, 'create']);
    Route::get('/login', [UserController::class, 'login'])
        ->name('login');
});


Route::middleware('auth')->group(function () {

    Route::get('/listings/create', [ListingController::class, 'create']);

    Route::put('/listings/{listing}', [ListingController::class, 'update']);
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit']);
    Route::post('/listings', [ListingController::class, 'store']);
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy']);

    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/manage', [ListingController::class, 'manage']);

    Route::get('/profile', [UserController::class, 'index']);
    Route::put('/profile/update', [UserController::class, 'update']);
});

Route::get('/listings/{listing}', [ListingController::class, 'show']);
