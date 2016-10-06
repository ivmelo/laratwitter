<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  int  $username
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Follow a user.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function follow($user_id)
    {
        $user_to_follow = User::findOrFail($user_id);

        if ($user_to_follow->isFollower(Auth::user()->id)) {
            // User is already a follower, do nothing.
            return redirect()->back();
        }

        // User is not a follower, attach them to the desired user.
        $user_to_follow->followers()->attach(Auth::user());

        return redirect()->back();
    }

    /**
     * Unfollow a user.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function unfollow($user_id)
    {
        $user_to_unfollow = User::findOrFail($user_id);

        if ($user_to_unfollow->isFollower(Auth::user()->id)) {
            $user_to_unfollow->followers()->detach(Auth::user());

            return redirect()->back();
        }

        return redirect()->back();
    }
}
