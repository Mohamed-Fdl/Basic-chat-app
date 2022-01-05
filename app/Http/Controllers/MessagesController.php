<?php

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
        //  dd($request->content);
        //je valide les données

        if ($data = $request->validate([
            'message' => 'required',
            'd_id' => 'required',
        ])) {


            //je construit le message

            $data['user_id'] = Auth::user()->id;
            $data['reception_id'] = $data['d_id'] + Auth::user()->id;

            // je recupere l'utlisateur qui recoit le message => construire la notification

            $receiver = $data['d_id'];
            $user_recever = User::find($data['d_id']);
            unset($data['d_id']);

            //finalement je construit le message

            $newMessage = Messages::create($data);

            //je notifie au receveur le nouveau message

            $user_recever->notify(new MessageSend($newMessage));

            //je recupere tout les amis de l'user courant pour verifier si le receveur est deja ami ou pas et puis mettre a jour l'ordre d'affichage des discussions

            $friends = Friend::where('user', Auth::user()->id)->get();
            foreach ($friends as $friend) {
                if ($friend->friend == $receiver) {
                    $friend->updated_at = now();
                    $friend->save();
                }
            }

            //je retourne une reponse de success

            return response()->json(['status' => true, 'message' => 'Message sent successfully'], 200);
        } else {

            //je retourne une reponse d'erreur

            return response()->json(['status' => false, 'message' => 'An error occured'], 500);
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
     * @param  \App\Models\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function show(Messages $messages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function edit(Messages $messages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Messages $messages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function destroy(Messages $messages)
    {
        //
    }
}
