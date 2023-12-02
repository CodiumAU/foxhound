<?php

use Foxhound\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('channels', Controllers\ChannelController::class);
Route::get('channels/{channel}/messages', [Controllers\ChannelMessageController::class, 'index']);
Route::delete('channels/{channel}/messages', [Controllers\ChannelMessageController::class, 'destroy']);
Route::get('channels/{channel}/{uuid}/html', Controllers\ChannelHtmlController::class);
