<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\InvitationMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvitationMessageController extends Controller
{
    /**
     * Mendapatkan semua ucapan/doa yang sudah disetujui.
     *
     * @return JsonResponse
     */
    public function getAllMessages(): JsonResponse
    {
        // Ambil semua pesan yang sudah disetujui, urutkan dari yang terbaru
        \Log::info('Accessing getAllMessages');
        $messages = InvitationMessage::where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->with('contact:id,name,username') // Eager load contact dengan kolom terbatas
            ->get();
        \Log::info('Messages count: ' . $messages->count());
        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * Mendapatkan semua ucapan untuk kontak dengan username tertentu.
     *
     * @param string $username
     * @return JsonResponse
     */
    public function getMessagesByUsername(string $username): JsonResponse
    {
        $contact = Contact::where('username', $username)->first();

        if (!$contact) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found',
            ], 404);
        }

        $messages = InvitationMessage::where('contact_id', $contact->id)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * Menyimpan ucapan/doa baru.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|exists:contacts,username',
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'attendance' => 'required|in:hadir,tidak_hadir,belum_pasti',
        ]);

        // Cari kontak berdasarkan username
        $contact = Contact::where('username', $validated['username'])->first();

        if (!$contact) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found',
            ], 404);
        }

        // Buat pesan ucapan baru
        $invitationMessage = InvitationMessage::create([
            'contact_id' => $contact->id,
            'name' => $validated['name'],
            'message' => $validated['message'],
            'attendance' => $validated['attendance'],
            'is_approved' => true, // Defaultnya disetujui
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Ucapan telah berhasil dikirim. Terima kasih!',
            'data' => $invitationMessage
        ], 201);
    }

    /**
     * Admin: Memperbarui status persetujuan pesan.
     *
     * @param Request $request
     * @param InvitationMessage $message
     * @return JsonResponse
     */
    public function updateApprovalStatus(Request $request, InvitationMessage $message): JsonResponse
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $message->update([
            'is_approved' => $validated['is_approved'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status persetujuan pesan berhasil diperbarui.',
            'data' => $message
        ]);
    }

    /**
     * Admin: Menghapus pesan.
     *
     * @param InvitationMessage $message
     * @return JsonResponse
     */
    public function destroy(InvitationMessage $message): JsonResponse
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $message->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pesan berhasil dihapus.',
        ]);
    }
}
