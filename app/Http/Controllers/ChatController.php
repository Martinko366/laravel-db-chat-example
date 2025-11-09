<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function users(Request $request)
    {
        // Get all users except the current one
        $users = User::where('id', '!=', auth()->id())
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json([
            'users' => $users,
            'current_user' => auth()->user()
        ]);
    }
}
