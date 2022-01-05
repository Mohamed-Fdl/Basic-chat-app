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
        $user_id = Auth::user()->id;
        $datas = $request->validate([
            'email' => 'required|email',
            'message' => 'required',
        ]);
        //verifie si il n'est pas deja ami avec $datas['email']
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function show(Friend $friend)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function edit(Friend $friend)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Friend $friend)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Friend  $friend
     * @return \Illuminate\Http\Response
     */
    public function destroy(Friend $friend)
    {
        //
    }
}
