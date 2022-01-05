<?php

use App\Http\Controllers\FriendController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\UserController;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('login',[UserController::class,'login'])->name('loguser');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/logout', function () {
    Auth::logout();
    return view('welcome');
})->name('logout');

Route::post('registering',[UserController::class,'create'])->name('registering');

Route::get('/discussion/{username}', function ($username) {
    $d_id = User::where('username',$username)->first()->id;
    return view('discussions',['d_id'=>$d_id]);
})->name('discussions');

Route::post('/updateUser', [UserController::class,'update'])->name('updateUser');

Route::get('/dashboard', function () {
    $id=Auth::user()->id;
    $nbr_friends=Friend::where('user',$id)->count();
    $friends=Friend::where('user',$id)->get();
    return view('dashboard',compact('nbr_friends','friends'));
})->name('dashboard');

Route::get('/discussion', function () {
    return view('discussions');
})->name('discussion');

Route::post('/messages',[MessagesController::class,'new']);

Route::post('/newfriend',[FriendController::class,'newfriend'])->name('newfriend');
