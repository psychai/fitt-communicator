<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => [],
    'prefix' => 'fitt-communicator',
], function () {

    Route::get('/login', 'Psychai\FittCommunicator\Http\Controllers\FittCommunicatorController@login')
    ->name('fitt-communicator.login.get');

    Route::get('/register', 'Psychai\FittCommunicator\Http\Controllers\FittCommunicatorController@register')
        ->name('fitt-communicator.register.get');
});
