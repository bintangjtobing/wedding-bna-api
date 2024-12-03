<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function index(){
        $guest = Guest::orderBy('created_at', 'DESC')->get();
        return response()->json($guest, 200);
    }
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|email|max:255|unique:users',
            'profile_picture' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
        ]);

        $user = Guest::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'profile_picture' => $request->profile_picture,
            'comment' => $request->comment,
        ]);

        return response()->json($user, 201);
    }

    // Get user invitation by slug
    public function show($slug)
    {
        $user = Guest::where('slug_name', $slug)->first();

        if (!$user) {
            return response()->json(['message' => 'Invitation not found'], 404);
        }

        return response()->json($user);
    }
}