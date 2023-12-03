<?php

use Foxhound\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('channels', Controllers\ChannelController::class);
Route::get('channels/{channel}/messages', [Controllers\ChannelMessageController::class, 'index']);
Route::delete('channels/{channel}/messages', [Controllers\ChannelMessageController::class, 'destroy']);
Route::get('channels/{channel}/messages/{uuid}', [Controllers\ChannelMessageController::class, 'show']);
Route::get('channels/{channel}/messages/{message}/attachment/{attachment}', Controllers\ChannelMessageAttachmentController::class)->name('foxhound.attachment');
