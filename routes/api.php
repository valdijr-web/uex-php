<?php

use App\Http\Controllers\AddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/password/email', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
    
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    
    Route::delete('/users/delete/account', [UserController::class, 'deleteAccount'])->name('users.deleteAccount');
    Route::get('/search/address', [AddressController::class, 'getAddressByZip'])->name('search.address');

    Route::get('/address/zipcode', [AddressController::class, 'getAddressByZipCode'])->name('address.zipcode');
    Route::get('/address/suggestions', [AddressController::class, 'getSuggestionsAddress'])->name('address.suggestions');
});
