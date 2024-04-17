<?php

use App\Http\Controllers\AudioController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'audios'], function () {
        Route::get('/', [AudioController::class, 'index'])->name('audios.index');
        Route::get('/create', [AudioController::class, 'create'])->name('audios.create');
        Route::get('/show/{audio}', [AudioController::class, 'show'])->name('audios.show');
        Route::post('/', [AudioController::class, 'store'])->name('audios.store');
    });
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/failed-jobs', function (){
    dd(DB::table('failed_jobs')->get());
});
