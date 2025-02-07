<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ShoppingListItemController;
use App\Http\Middleware\AuthenticateJWT;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware([AuthenticateJWT::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Shopping List Routes
    Route::get('/shopping-lists', [ShoppingListController::class, 'index']);
    Route::post('/shopping-lists', [ShoppingListController::class, 'store']);
    Route::put('/shopping-lists/{id}', [ShoppingListController::class, 'update']);
    Route::delete('/shopping-lists/{id}', [ShoppingListController::class, 'destroy']);

    // Shopping List Items Routes
    Route::post('/shopping-lists/{id}/items', [ShoppingListItemController::class, 'store']);
    Route::put('/shopping-lists/{id}/items/{itemId}', [ShoppingListItemController::class, 'update']);
    Route::delete('/shopping-lists/{id}/items/{itemId}', [ShoppingListItemController::class, 'destroy']);
});

