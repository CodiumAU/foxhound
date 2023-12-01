<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Notifications\TestNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TestMailableNotification;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test', function () {
    Storage::disk('local')->deleteDirectory('foxhound');
    User::find(1)->notify(new TestNotification('test'));
    User::find(1)->notify(new TestMailableNotification('test'));
    // Notification::route('mail', 'foo@bar.com')->notify(new TestNotification('blah'));
});

Route::fallback(function () {
    return view('index');
});
