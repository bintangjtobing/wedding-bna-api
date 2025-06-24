<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Services\ClickLogService;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    protected $clickLogService;

    public function __construct(ClickLogService $clickLogService)
    {
        $this->clickLogService = $clickLogService;
    }
    /**
     * Display contacts with mobile-first responsive design
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = $admin->contacts();

        // Filter berdasarkan status undangan jika ada
        if ($request->has('status') && in_array($request->status, ['belum_dikirim', 'terkirim', 'gagal'])) {
            $query->where('invitation_status', $request->status);
        }

        // Filter berdasarkan negara jika ada
        if ($request->has('country') && !empty($request->country)) {
            if ($request->country === 'OTHER') {
                $query->whereNotIn('country', ['ID', 'MY', 'SG', 'US']);
            } else {
                $query->where('country', $request->country);
            }
        }

        // Pencarian kontak
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Eager load click logs untuk performance
        $query->withCount('clickLogs');

        // Urutkan berdasarkan yang terbaru
        $query->latest();

        // Paginasi dengan parameter yang di-preserve
        $contacts = $query->paginate(20)->appends($request->all());

        // Statistik untuk card summary
        $stats = [
            'total' => $admin->contacts()->count(),
            'terkirim' => $admin->contacts()->where('invitation_status', 'terkirim')->count(),
            'belum_dikirim' => $admin->contacts()->where('invitation_status', 'belum_dikirim')->count(),
            'gagal' => $admin->contacts()->where('invitation_status', 'gagal')->count(),

            // Country breakdown
            'countries' => [
                'ID' => $admin->contacts()->where('country', 'ID')->count(),
                'MY' => $admin->contacts()->where('country', 'MY')->count(),
                'SG' => $admin->contacts()->where('country', 'SG')->count(),
                'OTHER' => $admin->contacts()->whereNotIn('country', ['ID', 'MY', 'SG', 'US'])->count(),
            ]
        ];

        return view('contacts.index', compact('contacts', 'stats'));
    }

    /**
     * Display contact analytics dashboard
     */
    public function analytics(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // Get contacts with click analytics
        $contactsWithClicks = $admin->contacts()
            ->withCount('clickLogs')
            ->with(['clickLogs' => function($query) {
                $query->select('contact_id', 'ip_address', 'country', 'city', 'device_type', 'clicked_at')
                      ->orderBy('clicked_at', 'desc');
            }])
            ->having('click_logs_count', '>', 0)
            ->orderByDesc('click_logs_count')
            ->get();

        // Overall analytics
        $contactIds = $admin->contacts()->pluck('id');
        $allClickLogs = \App\Models\ClickLog::whereIn('contact_id', $contactIds)->get();

        $analytics = [
            'total_clicks' => $allClickLogs->count(),
            'unique_visitors' => $allClickLogs->unique('ip_address')->count(),
            'countries_reached' => $allClickLogs->whereNotNull('country')->unique('country')->count(),
            'cities_reached' => $allClickLogs->whereNotNull('city')->unique('city')->count(),

            // Time-based stats
            'today_clicks' => $allClickLogs->where('clicked_at', '>=', now()->startOfDay())->count(),
            'week_clicks' => $allClickLogs->where('clicked_at', '>=', now()->startOfWeek())->count(),
            'month_clicks' => $allClickLogs->where('clicked_at', '>=', now()->startOfMonth())->count(),

            // Top countries
            'top_countries' => $allClickLogs->whereNotNull('country')
                ->groupBy('country')
                ->map->count()
                ->sortDesc()
                ->take(5),

            // Device breakdown
            'device_breakdown' => $allClickLogs->whereNotNull('device_type')
                ->groupBy('device_type')
                ->map->count()
                ->sortDesc(),

            // Recent activities
            'recent_activities' => $allClickLogs->sortByDesc('clicked_at')->take(10),
        ];

        return view('contacts.analytics', compact('analytics', 'contactsWithClicks'));
    }

    /**
     * Display contact management tools
     */
    public function manage(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // Get summary stats
        $stats = [
            'total_contacts' => $admin->contacts()->count(),
            'pending_invitations' => $admin->contacts()->where('invitation_status', 'belum_dikirim')->count(),
            'sent_invitations' => $admin->contacts()->where('invitation_status', 'terkirim')->count(),
            'failed_invitations' => $admin->contacts()->where('invitation_status', 'gagal')->count(),
        ];

        // Get recent import/export activities (jika ada log table)
        $recentActivities = collect([
            // Bisa ditambahkan log activities nanti
        ]);

        // Get failed contacts for retry
        $failedContacts = $admin->contacts()
            ->where('invitation_status', 'gagal')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('contacts.manage', compact('stats', 'recentActivities', 'failedContacts'));
    }
    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        // Eager load relasi yang dibutuhkan untuk mengurangi jumlah query
        $contact->load(['admin', 'messageLogs.message', 'messageLogs.admin']);

        $clickStats = $this->clickLogService->getClickStats($contact);

        return view('contacts.show', compact('contact', 'clickStats'));
    }
    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'nullable|string|max:100|unique:contacts,username',
        'phone_number' => 'required|string|max:20',
        'country' => 'required|string|size:2',
        'country_code' => 'required|string|max:5',
        'greeting' => 'nullable|string|max:50',
        'save_action' => 'required|in:save,save_and_add',
    ]);

    // Buat kontak baru
    $contact = new Contact([
        'name' => $validated['name'],
        'username' => $validated['username'],
        'phone_number' => $validated['phone_number'],
        'country' => $validated['country'],
        'country_code' => $validated['country_code'],
        'greeting' => $validated['greeting'],
    ]);

    Auth::guard('admin')->user()->contacts()->save($contact);

    // Hitung jumlah kontak yang ditambahkan hari ini (untuk progress indicator)
    $todayContactsCount = Auth::guard('admin')->user()->contacts()
        ->whereDate('created_at', today())
        ->count();

    // Tentukan redirect berdasarkan aksi yang dipilih
    if ($validated['save_action'] === 'save_and_add') {
        return redirect()->route('contacts.create')
            ->with('success', "Kontak '{$contact->name}' berhasil ditambahkan.")
            ->with('contacts_added_count', $todayContactsCount)
            ->with('last_added_contact', [
                'name' => $contact->name,
                'country' => $contact->country,
                'greeting' => $contact->greeting
            ]);
    } else {
        return redirect()->route('contacts.index')
            ->with('success', "Kontak '{$contact->name}' berhasil ditambahkan.");
    }
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
            'username' => 'nullable|string|max:100|unique:contacts,username,' . $contact->id,
            'phone_number' => 'required|string|max:20',
            'country' => 'required|string|size:2',
            'country_code' => 'required|string|max:5',
            'greeting' => 'nullable|string|max:50',
            'invitation_status' => 'required|in:belum_dikirim,terkirim,gagal',
        ]);

        // Simpan waktu status terakhir jika mengubah status
        if ($contact->invitation_status !== $validated['invitation_status']) {
            if ($validated['invitation_status'] === 'terkirim') {
                $contact->sent_at = now();
            } elseif ($validated['invitation_status'] === 'belum_dikirim') {
                $contact->sent_at = null;
            }
        }

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
    public function apiGetContacts(Request $request)
    {
        // Gunakan auth()->user() untuk mendapatkan user yang saat ini login via API
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $contacts = $admin->contacts()->get();

        return response()->json([
            'status' => 'success',
            'data' => $contacts
        ]);
    }
    public function apiSearchContacts(Request $request)
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

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
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%"); // Tambahkan pencarian berdasarkan username
            });
        }

        // Pencarian kontak berdasarkan username
        if ($request->has('username') && !empty($request->username)) {
            $username = $request->username;
            $query->where('username', $username);
        }

        // Pengurutan
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSortColumns = ['name', 'phone_number', 'username', 'invitation_status', 'sent_at', 'created_at'];

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
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

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
    public function apiGetFailedContacts()
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $failedContacts = $admin->contacts()->where('invitation_status', 'gagal')->get();

        return response()->json([
            'status' => 'success',
            'data' => $failedContacts
        ]);
    }
    public function apiUpdateContact(Request $request, Contact $contact)
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Periksa apakah kontak milik admin ini
        if ($contact->admin_id !== $admin->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mengupdate kontak ini'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:100|unique:contacts,username,' . $contact->id,
            'phone_number' => 'required|string|max:20',
            'country' => 'required|string|size:2',
            'country_code' => 'required|string|max:5',
            'greeting' => 'nullable|string|max:50',
            'invitation_status' => 'required|in:belum_dikirim,terkirim,gagal',
        ]);

        // Simpan waktu status terakhir jika mengubah status
        if ($contact->invitation_status !== $validated['invitation_status']) {
            if ($validated['invitation_status'] === 'terkirim') {
                $contact->sent_at = now();
            } elseif ($validated['invitation_status'] === 'belum_dikirim') {
                $contact->sent_at = null;
            }
        }

        $contact->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Kontak berhasil diperbarui.',
            'data' => $contact
        ]);
    }
    public function apiDeleteContact(Contact $contact)
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Periksa apakah kontak milik admin ini
        if ($contact->admin_id !== $admin->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menghapus kontak ini'
            ], 403);
        }

        $contact->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kontak berhasil dihapus.'
        ]);
    }
    public function apiGetContactByUsername($username, Request $request)
    {
        $contact = Contact::where('username', $username)->first();

        if (!$contact) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found',
            ], 404);
        }

        // Log click tracking
        try {
            $clickLogService = app(\App\Services\ClickLogService::class);
            $clickLogService->logClick($contact, $request);
        } catch (\Exception $e) {
            // Don't fail the main request if click logging fails
            \Log::warning('Click logging failed', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);
        }

        // Hilangkan informasi sensitif
        $contact->makeHidden(['admin_id']);

        return response()->json([
            'status' => 'success',
            'data' => $contact
        ]);
    }
}
