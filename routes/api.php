<?php

use Foxhound\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('channels', Controllers\ChannelController::class);
Route::get('channels/{channel}/messages', [Controllers\MessageController::class, 'index']);
Route::delete('channels/{channel}/messages', [Controllers\MessageController::class, 'destroy']);
Route::get('channels/{channel}/messages/{uuid}', [Controllers\MessageController::class, 'show']);
Route::get('channels/{channel}/messages/{message}/attachment/{attachment}', Controllers\AttachmentController::class)->name('foxhound::attachment');
