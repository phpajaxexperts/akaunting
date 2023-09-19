<?php

use Illuminate\Support\Facades\Route;

/**
 * 'admin' middleware and 'interview' prefix applied to all routes (including names)
 *
 * @see \App\Providers\Route::register
 */

Route::admin('interview', function () {
    Route::get('/', 'Main@index');
    Route::post('/create-user', 'Main@createUser');
    Route::post('/create-transaction', 'Main@createTransaction');
});
