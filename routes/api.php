<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('channels/mail', [Controllers\Channels\MailController::class, 'index']);
Route::get('channels/mail/{uuid}/html', Controllers\Channels\MailRenderController::class);
