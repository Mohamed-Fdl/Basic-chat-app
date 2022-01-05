# Basic chat App
In this repo I show you how I made a basic chat app WITH Laravel.
The app allow you to register login and start a new discussion by knowing the receiver email


## Database structure
### Tables
I need to store users and todos

* users
    1. name
    2. email
    3. password
* messages
    1. user_id
    2. message
    3. reception_id
    4. read_status
    5. timestamps
* friends
    1. user
    2. friend_id
    3. email
    4. timestamps


## Route APP


```php

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


```

## Messages controller

```php


namespace App\Http\Controllers;

use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Friend;
use App\Models\User;
use App\Notifications\MessageSend;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function new(Request $request)
    {
        //data validation

        if ($data = $request->validate([
            'message' => 'required',
            'd_id' => 'required',
        ])) {


            //i construct the message

            $data['user_id'] = Auth::user()->id;
            $data['reception_id'] = $data['d_id'] + Auth::user()->id;

            //construct the notification

            $receiver = $data['d_id'];
            $user_recever = User::find($data['d_id']);
            unset($data['d_id']);

            //finally create the message 

            $newMessage = Messages::create($data);

            //send notification

            $user_recever->notify(new MessageSend($newMessage));

            //update user's friends

            $friends = Friend::where('user', Auth::user()->id)->get();
            foreach ($friends as $friend) {
                if ($friend->friend == $receiver) {
                    $friend->updated_at = now();
                    $friend->save();
                }
            }

            //return success

            return response()->json(['status' => true, 'message' => 'Message sent successfully'], 200);
        } else {

            //return fail

            return response()->json(['status' => false, 'message' => 'An error occured'], 500);
        }
    }

   

   
}


```

## Friends controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Messages;
use App\Models\User;
use FFI\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\MessageSend;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function newfriend(Request $request)
    {
        //validate datas
        $user_id = Auth::user()->id;
        $datas = $request->validate([
            'email' => 'required|email',
            'message' => 'required',
        ]);
        //create new friend after verify if he isn't already friend
        $allFriends = Friend::where('user', $user_id)->get('email');
        foreach ($allFriends as $friend) {
            if ($friend->email == $datas['email']) {
                return back();
            }
        }
        try {
            $friend = DB::table('users')->where('email', $datas['email'])->get();
            foreach ($friend as $friend) {
                $friend_id = $friend->id;
                $friend_email = $friend->email;
                $newFriend = new Friend();
                $newFriend->user = $user_id;
                $newFriend->friend = $friend_id;
                $newFriend->email = $friend_email;
                $newFriend->save();
                $newFriend2 = new Friend();
                $newFriend2->user = $friend->id;
                $newFriend2->friend= $user_id;
                $newFriend2->email = Auth::user()->email;
                $newFriend2->save();
                //On envoi le message
                $message['user_id'] = $user_id;
                $message['message'] = $datas['message'];
                $message['reception_id'] = $user_id + $friend_id;
                $friend_notif=User::find($friend_id);
                $newMessage=Messages::create($message);
                $friend_notif->notify(new MessageSend($newMessage));
                return back();
            }
        } catch (Exception $e) {
        }
    }

}




```

###### After  that you can easily displays messages in views by using reception_id and user's id (Auth::user()->id) 


