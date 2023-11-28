<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Notifications\TestNotification;
use Illuminate\Support\Facades\Notification;

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

Route::get('/', function () {
    return view('index');
});

Route::get('/test', function () {
    Storage::disk('local')->deleteDirectory('interceptor');
    User::find(1)->notify(new TestNotification('test'));
    // Notification::route('mail', 'foo@bar.com')->notify(new TestNotification);
});
