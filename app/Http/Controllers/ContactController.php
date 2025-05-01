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

        // Pencarian kontak
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $contacts = $query->latest()->paginate(20);
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
    public function apiSearchContacts(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = $admin->contacts();

        // Filter berdasarkan status undangan jika ada
        if ($request->has('status') && in_array($request->status, ['belum_dikirim', 'terkirim', 'gagal'])) {
            $query->where('invitation_status', $request->status);
        }

        // Pencarian kontak
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Pengurutan
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSortColumns = ['name', 'phone_number', 'invitation_status', 'sent_at', 'created_at'];

        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Paginasi
        $perPage = $request->input('per_page', 20);
        $contacts = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $contacts,
            'meta' => [
                'total' => $contacts->total(),
                'per_page' => $contacts->perPage(),
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
            ]
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
    public function exportContacts(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = $admin->contacts();

        // Filter berdasarkan status undangan jika ada
        if ($request->has('status') && in_array($request->status, ['belum_dikirim', 'terkirim', 'gagal'])) {
            $query->where('invitation_status', $request->status);
        }

        $contacts = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="kontak_undangan.csv"',
        ];

        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');

            // Tulis header CSV
            fputcsv($file, ['Nama', 'Nomor Telepon', 'Status Undangan', 'Waktu Kirim']);

            // Tulis data kontak
            foreach ($contacts as $contact) {
                $status = '';
                switch ($contact->invitation_status) {
                    case 'belum_dikirim':
                        $status = 'Belum Dikirim';
                        break;
                    case 'terkirim':
                        $status = 'Terkirim';
                        break;
                    case 'gagal':
                        $status = 'Gagal';
                        break;
                }

                fputcsv($file, [
                    $contact->name,
                    $contact->phone_number,
                    $status,
                    $contact->sent_at ? $contact->sent_at->format('Y-m-d H:i:s') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function showImportForm()
    {
        return view('contacts.import');
    }
    public function resetAllStatus(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // Jika ada filter status
        if ($request->has('status') && in_array($request->status, ['terkirim', 'gagal', 'belum_dikirim'])) {
            $admin->contacts()->where('invitation_status', $request->status)->update([
                'invitation_status' => 'belum_dikirim',
                'sent_at' => null
            ]);
        } else {
            // Reset semua kontak
            $admin->contacts()->update([
                'invitation_status' => 'belum_dikirim',
                'sent_at' => null
            ]);
        }

        return redirect()->back()->with('success', 'Status undangan berhasil direset.');
    }
    public function exportTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_kontak.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Tulis header CSV
            fputcsv($file, ['nama', 'telepon']);

            // Tulis contoh data
            fputcsv($file, ['Nama Tamu 1', '081234567890']);
            fputcsv($file, ['Nama Tamu 2', '081234567891']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    // API export kontak
    public function apiExportContacts(Request $request)
    {
        // Untuk API, kita kembalikan JSON dengan data kontak
        $admin = Auth::guard('admin')->user();
        $query = $admin->contacts();

        // Filter berdasarkan status undangan jika ada
        if ($request->has('status') && in_array($request->status, ['belum_dikirim', 'terkirim', 'gagal'])) {
            $query->where('invitation_status', $request->status);
        }

        $contacts = $query->get()->map(function($contact) {
            $status = '';
            switch ($contact->invitation_status) {
                case 'belum_dikirim':
                    $status = 'Belum Dikirim';
                    break;
                case 'terkirim':
                    $status = 'Terkirim';
                    break;
                case 'gagal':
                    $status = 'Gagal';
                    break;
            }

            return [
                'id' => $contact->id,
                'name' => $contact->name,
                'phone_number' => $contact->phone_number,
                'invitation_status' => $contact->invitation_status,
                'status_text' => $status,
                'sent_at' => $contact->sent_at ? $contact->sent_at->format('Y-m-d H:i:s') : null,
                'created_at' => $contact->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $contact->updated_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $contacts
        ]);
    }
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
        ]);

        $admin = Auth::guard('admin')->user();
        $deletedCount = 0;

        foreach ($request->contact_ids as $contactId) {
            $contact = Contact::find($contactId);

            if ($contact && $contact->admin_id === $admin->id) {
                $contact->delete();
                $deletedCount++;
            }
        }

        return redirect()->back()->with('success', "Berhasil menghapus {$deletedCount} kontak.");
    }
    public function apiBulkDelete(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
        ]);

        $admin = Auth::guard('admin')->user();
        $deletedCount = 0;
        $deletedIds = [];

        foreach ($request->contact_ids as $contactId) {
            $contact = Contact::find($contactId);

            if ($contact && $contact->admin_id === $admin->id) {
                $contact->delete();
                $deletedCount++;
                $deletedIds[] = $contactId;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil menghapus {$deletedCount} kontak.",
            'data' => [
                'deleted_count' => $deletedCount,
                'deleted_ids' => $deletedIds,
            ]
        ]);
    }
    public function importContacts(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $admin = Auth::guard('admin')->user();
        $file = $request->file('csv_file');

        $path = $file->getRealPath();
        $records = array_map('str_getcsv', file($path));

        // Asumsikan baris pertama adalah header
        $headers = array_shift($records);

        // Tentukan indeks kolom nama dan nomor telepon
        $nameIndex = array_search('nama', array_map('strtolower', $headers));
        $phoneIndex = array_search('telepon', array_map('strtolower', $headers)) ??
                    array_search('nomor', array_map('strtolower', $headers)) ??
                    array_search('hp', array_map('strtolower', $headers)) ??
                    array_search('no_hp', array_map('strtolower', $headers));

        if ($nameIndex === false || $phoneIndex === false) {
            return redirect()->back()->with('error', 'Format CSV tidak valid. Pastikan terdapat kolom "nama" dan "telepon/nomor/hp/no_hp".');
        }

        $importedCount = 0;
        $errors = [];

        foreach ($records as $index => $record) {
            if (isset($record[$nameIndex]) && isset($record[$phoneIndex])) {
                $name = trim($record[$nameIndex]);
                $phone = trim($record[$phoneIndex]);

                // Validasi data
                if (empty($name) || empty($phone)) {
                    $errors[] = "Baris " . ($index + 2) . ": Nama atau nomor telepon kosong.";
                    continue;
                }

                // Cek apakah nomor telepon sudah terdaftar
                $existingContact = $admin->contacts()->where('phone_number', $phone)->first();
                if ($existingContact) {
                    $errors[] = "Baris " . ($index + 2) . ": Nomor telepon $phone sudah terdaftar atas nama {$existingContact->name}.";
                    continue;
                }

                // Buat kontak baru
                $admin->contacts()->create([
                    'name' => $name,
                    'phone_number' => $phone,
                    'invitation_status' => 'belum_dikirim'
                ]);

                $importedCount++;
            } else {
                $errors[] = "Baris " . ($index + 2) . ": Format data tidak lengkap.";
            }
        }

        $message = "Berhasil mengimpor $importedCount kontak.";
        if (!empty($errors)) {
            $message .= " Terdapat " . count($errors) . " error.";
        }

        return redirect()->route('contacts.index')->with('success', $message)
            ->with('import_errors', $errors);
    }
    public function resetStatus(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $contact->updateInvitationStatus('belum_dikirim');
        $contact->sent_at = null;
        $contact->save();

        return redirect()->back()->with('success', 'Status undangan berhasil direset.');
    }
}