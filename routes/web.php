<?php

use DigitalClaim\AzureScheduler\Controller;
use Illuminate\Support\Facades\Route;

Route::get('/handle-scheduler', [Controller::class, 'handle'])
    ->middleware(['throttle:azure-scheduler'])
    ->name('azure-scheduler-handle');
