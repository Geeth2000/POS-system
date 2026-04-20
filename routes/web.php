<?php

use Illuminate\Support\Facades\Route;

Route::view('/pos', 'pos');
Route::redirect('/', '/pos');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
