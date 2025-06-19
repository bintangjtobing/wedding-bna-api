<!-- resources/views/messages/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Kirim Pesan Undangan</div>
    <div class="card-body">
        <div class="mb-4">
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Status Kontak Mempelai Pria</h5>
                            <div class="d-flex justify-content-between">
                                <span>Total:</span>
                                <strong>{{ $groomAdmin->contacts->count() ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Terkirim:</span>
                                <strong>{{ $groomSentCount }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Belum Dikirim:</span>
                                <strong>{{ $groomPendingCount }}</strong>
                            </div>
                            <!-- Breakdown per negara untuk Mempelai Pria -->
                            <hr>
                            <small class="text-muted">Per Negara:</small>
                            <div class="d-flex justify-content-between">
                                <span>🇮🇩 ID:</span>
                                <strong>{{ $groomAdmin->contacts->where('country', 'ID')->count() ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>🇲🇾 MY:</span>
                                <strong>{{ $groomAdmin->contacts->where('country', 'MY')->count() ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>🇸🇬 SG:</span>
                                <strong>{{ $groomAdmin->contacts->where('country', 'SG')->count() ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>🌍 Lainnya:</span>
                                <strong>{{ $groomAdmin->contacts->whereNotIn('country', ['ID', 'MY', 'SG'])->count() ??
                                    0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Status Kontak Mempelai Wanita</h5>
                            <div class="d-flex justify-content-between">
                                <span>Total:</span>
                                <strong>{{ $brideAdmin->contacts->count() ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Terkirim:</span>
                                <strong>{{ $brideSentCount }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Belum Dikirim:</span>
                                <strong>{{ $bridePendingCount }}</strong>
                            </div>
                            <!-- Breakdown per negara untuk Mempelai Wanita -->
                            <hr>
                            <small class="text-muted">Per Negara:</small>
                            <div class="d-flex justify-content-between">
                                <span>🇮🇩 ID:</span>
                                <strong>{{ $brideAdmin->contacts->where('country', 'ID')->count() ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>🇲🇾 MY:</span>
                                <strong>{{ $brideAdmin->contacts->where('country', 'MY')->count() ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>🇸🇬 SG:</span>
                                <strong>{{ $brideAdmin->contacts->where('country', 'SG')->count() ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>🌍 Lainnya:</span>
                                <strong>{{ $brideAdmin->contacts->whereNotIn('country', ['ID', 'MY', 'SG'])->count() ??
                                    0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($template))
        <div class="alert alert-info mb-4">
            <h5>Menggunakan template: {{ $template->template_name }}</h5>
        </div>
        @else
        <div class="mb-4">
            <a href="{{ route('templates.index') }}" class="btn btn-outline-info">
                <i class="bi bi-file-text"></i> Gunakan Template Pesan
            </a>
        </div>
        @endif

        <form action="{{ route('messages.send') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="message_content" class="form-label">Isi Pesan</label>
                <textarea class="form-control @error('message_content') is-invalid @enderror" id="message_content"
                    name="message_content" rows="7"
                    required>{{ old('message_content', isset($template) ? $template->content : '') }}</textarea>
                <div class="form-text">
                    <strong>Variabel tersedia:</strong>
                    <ul class="list-inline">
                        <li class="list-inline-item"><code>[NAMA]</code> - Nama lengkap</li>
                        <li class="list-inline-item"><code>[USERNAME]</code> - Username</li>
                        <li class="list-inline-item"><code>[PANGGILAN]</code> - Panggilan</li>
                        <li class="list-inline-item"><code>[NEGARA]</code> - Kode Negara (ID, MY, dll)</li>
                        <li class="list-inline-item"><code>[KODE_NEGARA]</code> - Kode Telepon Negara (62, 60, dll)</li>
                    </ul>
                </div>
                @error('message_content')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Kirim ke Kontak:</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="admin_selection[]" id="groom_admin"
                        value="{{ $groomAdmin->id ?? '' }}" {{ old('admin_selection') && in_array($groomAdmin->id ?? '',
                    old('admin_selection')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="groom_admin">
                        Kontak Mempelai Pria ({{ $groomAdmin->contacts->count() ?? 0 }} kontak)
                        @if($groomPendingCount > 0)
                        <span class="badge bg-warning">{{ $groomPendingCount }} belum dikirim</span>
                        @endif
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="admin_selection[]" id="bride_admin"
                        value="{{ $brideAdmin->id ?? '' }}" {{ old('admin_selection') && in_array($brideAdmin->id ?? '',
                    old('admin_selection')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="bride_admin">
                        Kontak Mempelai Wanita ({{ $brideAdmin->contacts->count() ?? 0 }} kontak)
                        @if($bridePendingCount > 0)
                        <span class="badge bg-warning">{{ $bridePendingCount }} belum dikirim</span>
                        @endif
                    </label>
                </div>
                @error('admin_selection')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Filter berdasarkan Negara -->
            <div class="mb-3">
                <label class="form-label">Filter berdasarkan Negara (Opsional):</label>
                <div class="alert alert-info">
                    <small><strong>Tips:</strong> Untuk kirim pesan bertahap berdasarkan bahasa:</small>
                    <ul class="mb-0">
                        <li>Pilih 🇮🇩 Indonesia untuk kirim template Bahasa Indonesia</li>
                        <li>Pilih 🇲🇾 Malaysia untuk kirim template Bahasa Inggris</li>
                        <li>Jika tidak dipilih, akan kirim ke semua negara</li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="country_filter[]" id="filter_id"
                                value="ID">
                            <label class="form-check-label" for="filter_id">
                                🇮🇩 Indonesia (ID)
                                <span class="badge bg-primary">
                                    {{ ($groomAdmin->contacts->where('country', 'ID')->count() ?? 0) +
                                    ($brideAdmin->contacts->where('country', 'ID')->count() ?? 0) }} kontak
                                </span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="country_filter[]" id="filter_my"
                                value="MY">
                            <label class="form-check-label" for="filter_my">
                                🇲🇾 Malaysia (MY)
                                <span class="badge bg-primary">
                                    {{ ($groomAdmin->contacts->where('country', 'MY')->count() ?? 0) +
                                    ($brideAdmin->contacts->where('country', 'MY')->count() ?? 0) }} kontak
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="country_filter[]" id="filter_sg"
                                value="SG">
                            <label class="form-check-label" for="filter_sg">
                                🇸🇬 Singapura (SG)
                                <span class="badge bg-primary">
                                    {{ ($groomAdmin->contacts->where('country', 'SG')->count() ?? 0) +
                                    ($brideAdmin->contacts->where('country', 'SG')->count() ?? 0) }} kontak
                                </span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="country_filter[]" id="filter_other"
                                value="OTHER">
                            <label class="form-check-label" for="filter_other">
                                🌍 Negara Lainnya
                                <span class="badge bg-primary">
                                    {{ ($groomAdmin->contacts->whereNotIn('country', ['ID', 'MY', 'SG'])->count() ?? 0)
                                    + ($brideAdmin->contacts->whereNotIn('country', ['ID', 'MY', 'SG'])->count() ?? 0)
                                    }} kontak
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <small class="form-text text-muted">
                    Jika tidak ada yang dipilih, pesan akan dikirim ke semua negara sesuai admin yang dipilih.
                </small>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="only_pending" id="only_pending" value="1" {{
                        old('only_pending') ? 'checked' : '' }}>
                    <label class="form-check-label" for="only_pending">
                        Hanya kirim ke kontak yang belum dikirimi undangan
                    </label>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Pratinjau Pesan</div>
                <div class="card-body">
                    <div id="preview-container" class="bg-light p-3 rounded mb-3">
                        <p class="mb-0" id="preview-text">Tulis pesan untuk melihat pratinjau...</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pratinjau untuk:</label>
                        <select id="preview-contact" class="form-select">
                            <option value="">-- Pilih kontak untuk pratinjau --</option>
                            <optgroup label="Kontak Mempelai Pria">
                                @foreach($groomAdmin->contacts ?? [] as $contact)
                                <option value="{{ json_encode([
                                    'name' => $contact->name,
                                    'username' => $contact->username,
                                    'greeting' => $contact->greeting,
                                    'country' => $contact->country,
                                    'country_code' => $contact->country_code
                                ]) }}">{{ $contact->name }} ({{ $contact->country }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Kontak Mempelai Wanita">
                                @foreach($brideAdmin->contacts ?? [] as $contact)
                                <option value="{{ json_encode([
                                    'name' => $contact->name,
                                    'username' => $contact->username,
                                    'greeting' => $contact->greeting,
                                    'country' => $contact->country,
                                    'country_code' => $contact->country_code
                                ]) }}">{{ $contact->name }} ({{ $contact->country }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Kirim Pesan</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('message_content');
    const previewText = document.getElementById('preview-text');
    const previewContact = document.getElementById('preview-contact');
    let selectedContact = null;

    // Function to update preview
    function updatePreview() {
        let message = messageInput.value;

        if (!message) {
            previewText.textContent = 'Tulis pesan untuk melihat pratinjau...';
            return;
        }

        if (selectedContact) {
            // Replace placeholders with contact data
            message = message.replace(/\[NAMA\]/g, selectedContact.name || '');
            message = message.replace(/\[USERNAME\]/g, selectedContact.username || '');
            message = message.replace(/\[PANGGILAN\]/g, selectedContact.greeting || selectedContact.name || '');
            message = message.replace(/\[NEGARA\]/g, selectedContact.country || '');
            message = message.replace(/\[KODE_NEGARA\]/g, selectedContact.country_code || '');
        }

        // Replace line breaks with HTML breaks for display
        previewText.innerHTML = message.replace(/\n/g, '<br>');
    }

    // Listen for changes in message content
    messageInput.addEventListener('input', updatePreview);

    // Listen for contact selection changes
    previewContact.addEventListener('change', function() {
        try {
            selectedContact = this.value ? JSON.parse(this.value) : null;
            updatePreview();
        } catch(e) {
            console.error('Error parsing contact data', e);
        }
    });

    // Initial preview update
    updatePreview();
});
</script>
@endsection