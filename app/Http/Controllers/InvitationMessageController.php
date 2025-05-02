<?php

namespace App\Http\Controllers;

use App\Models\InvitationMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class InvitationMessageController extends Controller
{
    /**
     * Display a listing of the messages.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $messages = InvitationMessage::with('contact')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('invitation_messages.index', compact('messages'));
    }

    /**
     * Toggle the approval status of a message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InvitationMessage  $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleApproval(Request $request, InvitationMessage $message)
    {
        $validated = $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $message->update([
            'is_approved' => $validated['is_approved'],
        ]);

        return redirect()->route('invitation_messages.index')
            ->with('success', 'Status persetujuan pesan berhasil diperbarui.');
    }

    /**
     * Remove the specified message from storage.
     *
     * @param  \App\Models\InvitationMessage  $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(InvitationMessage $message)
    {
        $message->delete();

        return redirect()->route('invitation_messages.index')
            ->with('success', 'Pesan berhasil dihapus.');
    }
}
