<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/receberImagem', function () {
    return view('receberImagem');
});

Route::get('/contato', function () {
    return view('contato');
});

Route::get('/sobre', function () {
    return view('sobre');
});
Route::get('/erro', function () {
    return view('erro');
});

Route::get('/upload', [ PhotoController::class, 'create' ]);
Route::post('/upload', [ PhotoController::class, 'store' ]);
