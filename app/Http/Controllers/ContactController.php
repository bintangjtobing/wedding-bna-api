<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = $admin->contacts();

        // Filter berdasarkan status undangan jika ada
        if ($request->has('status') && in_array($request->status, ['belum_dikirim', 'terkirim', 'gagal'])) {
            $query->where('invitation_status', $request->status);
        }

        $contacts = $query->get();
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $contact = new Contact([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ]);

        Auth::guard('admin')->user()->contacts()->save($contact);

        return redirect()->route('contacts.index')
            ->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $contact->update($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Kontak berhasil diperbarui.');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Kontak berhasil dihapus.');
    }

    // API endpoint untuk mendapatkan daftar kontak
    public function apiGetContacts()
    {
        $admin = Auth::guard('admin')->user();
        $contacts = $admin->contacts;

        return response()->json([
            'status' => 'success',
            'data' => $contacts
        ]);
    }

    // API endpoint untuk menambahkan kontak baru
    public function apiAddContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $admin = Auth::guard('admin')->user();
        $contact = new Contact([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ]);

        $admin->contacts()->save($contact);

        return response()->json([
            'status' => 'success',
            'message' => 'Kontak berhasil ditambahkan',
            'data' => $contact
        ]);
    }
}
