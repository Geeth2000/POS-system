<?php

use Illuminate\Support\Facades\Route;

Route::view('/pos', 'pos');
Route::redirect('/', '/pos');
