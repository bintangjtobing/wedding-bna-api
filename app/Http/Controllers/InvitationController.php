<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Reply;
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

        // Jika user tidak ditemukan
        if (!$user) {
            // Membuat nama default tanpa garis dan huruf kapital
            $defaultName = ucwords(str_replace('-', ' ', $slug)); // Mengganti '-' dengan spasi dan capitalize huruf pertama

            // Daftar URL gambar profil yang tersedia
            $profilePictures = [
                'https://res.cloudinary.com/dflafxsqp/image/upload/v1733239077/1_owgewp.png',
                'https://res.cloudinary.com/dflafxsqp/image/upload/v1733239077/2_igwgbi.png',
                'https://res.cloudinary.com/dflafxsqp/image/upload/v1733239077/3_oh8tqk.png',
                'https://res.cloudinary.com/dflafxsqp/image/upload/v1733239077/4_vmymin.png'
            ];

            // Mengambil gambar profil acak dari array
            $randomProfilePicture = $profilePictures[array_rand($profilePictures)];

            // Mengembalikan response dengan nama default dan gambar profil acak
            return response()->json([
                'name' => $defaultName,
                'slug_name' => $slug, // Kembalikan slug_name untuk reference
                'profile_picture' => $randomProfilePicture, // Gambar profil acak
                'comment' => null, // Null jika tidak ada komentar
            ]);
        }

        // Jika user ditemukan, kembalikan data user seperti biasa
        return response()->json($user);
    }
    public function replyToComment(Request $request, $slug)
    {

        // Mencari guest berdasarkan slug
        $guest = Guest::where('slug_name', $slug)->first();

        if (!$guest) {
            return response()->json(['message' => 'Invitation not found'], 404);
        }

        // Menyimpan balasan
        $reply = Reply::create([
            'guest_id' => $guest->id,  // ID guest yang komentarnya dibalas
            'commented_by_id' => 1, // ID admin atau user yang membalas
            'reply' => $request->reply,
        ]);

        return response()->json([
            'message' => 'Reply created successfully',
            'reply' => $reply
        ], 201);
    }
}