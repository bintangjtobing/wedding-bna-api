<!-- resources/views/contacts/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Tambah Kontak Baru</div>
    <div class="card-body">
        <form action="{{ route('contacts.store') }}" method="POST" id="contactForm">
            @csrf
            <!-- Input hidden untuk menentukan aksi setelah simpan -->
            <input type="hidden" name="save_action" id="save_action" value="save">

            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name') }}" required>
                <div class="form-text">Nama lengkap tamu undangan.</div>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                    name="username" value="{{ old('username') }}">
                <div class="form-text">Username akan dibuat otomatis dari nama jika dikosongkan.</div>
                @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="country" class="form-label">Negara</label>
                    <select class="form-select @error('country') is-invalid @enderror" id="country" name="country">
                        <option value="ID" {{ old('country', 'ID' )=='ID' ? 'selected' : '' }}>Indonesia</option>
                        <option value="MY" {{ old('country')=='MY' ? 'selected' : '' }}>Malaysia</option>
                        <option value="SG" {{ old('country')=='SG' ? 'selected' : '' }}>Singapura</option>
                        <option value="US" {{ old('country')=='US' ? 'selected' : '' }}>Amerika Serikat</option>
                        <!-- Tambahkan negara lainnya sesuai kebutuhan -->
                    </select>
                    @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="country_code" class="form-label">Kode Negara</label>
                    <div class="input-group">
                        <span class="input-group-text">+</span>
                        <input type="text" class="form-control @error('country_code') is-invalid @enderror"
                            id="country_code" name="country_code" value="{{ old('country_code', '62') }}">
                    </div>
                    @error('country_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="phone_number" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                        id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                    <div class="form-text">Masukkan tanpa kode negara, contoh: 81234567890</div>
                    @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="greeting" class="form-label">Panggilan</label>
                <input type="text" class="form-control @error('greeting') is-invalid @enderror" id="greeting"
                    name="greeting" value="{{ old('greeting') }}">
                <div class="form-text">Panggilan untuk tamu, misalnya "Pak", "Bapak", "Ibu", "Kak", dll.</div>
                @error('greeting')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tombol dengan opsi berbeda -->
            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary" onclick="setSaveAction('save')">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
                <button type="submit" class="btn btn-success" onclick="setSaveAction('save_and_add')">
                    <i class="bi bi-plus-circle"></i> Simpan & Tambahkan yang Lain
                </button>
                <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Batal
                </a>
            </div>

            <!-- Info helper -->
            <div class="mt-3">
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i>
                    Gunakan "Simpan & Tambahkan yang Lain" untuk menambahkan beberapa kontak secara berturut-turut
                </small>
            </div>
        </form>
    </div>
</div>

<!-- Progress indicator dan last added contact info -->
@if(session('contacts_added_count'))
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <i class="bi bi-check-circle"></i>
    <strong>Berhasil!</strong> Kontak "{{ session('last_added_contact.name') }}" telah ditambahkan.
    Total hari ini: {{ session('contacts_added_count') }} kontak.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('last_added_contact'))
<div class="card mt-3 border-success">
    <div class="card-header bg-success text-white">
        <i class="bi bi-magic"></i> Quick Fill
    </div>
    <div class="card-body">
        <p class="mb-2">Kontak terakhir menggunakan:</p>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-success"
                onclick="fillCountry('{{ session('last_added_contact.country') }}')">
                Negara: {{ session('last_added_contact.country') }}
            </button>
            @if(session('last_added_contact.greeting'))
            <button type="button" class="btn btn-sm btn-outline-success"
                onclick="fillGreeting('{{ session('last_added_contact.greeting') }}')">
                Panggilan: {{ session('last_added_contact.greeting') }}
            </button>
            @endif
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearQuickFill()">
                <i class="bi bi-x"></i> Sembunyikan
            </button>
        </div>
    </div>
</div>
@endif

<script>
    // Script untuk mengatur aksi simpan
function setSaveAction(action) {
    document.getElementById('save_action').value = action;
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto focus ke field nama saat halaman dimuat
    document.getElementById('name').focus();

    const countrySelect = document.getElementById('country');
    const countryCodeInput = document.getElementById('country_code');
    const countryCodeMap = {
        'ID': '62',
        'MY': '60',
        'SG': '65',
        'US': '1',
        // Tambahkan kode negara lainnya
    };

    countrySelect.addEventListener('change', function() {
        const countryCode = countryCodeMap[this.value];
        if (countryCode) {
            countryCodeInput.value = countryCode;
        }
    });

    // Script untuk membuat username otomatis dari nama
    const nameInput = document.getElementById('name');
    const usernameInput = document.getElementById('username');

    nameInput.addEventListener('input', function() {
        if (!usernameInput.value) {
            // Ubah ke huruf kecil dan ganti spasi dengan tanda hubung
            const username = this.value.toLowerCase().replace(/\s+/g, '-')
                // Hilangkan karakter khusus
                .replace(/[^\w\-]+/g, '')
                // Hilangkan tanda hubung berurutan
                .replace(/\-\-+/g, '-')
                // Hilangkan tanda hubung di awal dan akhir
                .replace(/^-+/, '').replace(/-+$/, '');

            usernameInput.value = username;
        }
    });

    // Quick fill functions
    window.fillCountry = function(country) {
        const countrySelect = document.getElementById('country');
        const countryCodeInput = document.getElementById('country_code');

        countrySelect.value = country;
        // Trigger change event to update country code
        countrySelect.dispatchEvent(new Event('change'));
    };

    window.fillGreeting = function(greeting) {
        document.getElementById('greeting').value = greeting;
    };

    window.clearQuickFill = function() {
        const quickFillCard = document.querySelector('.card.border-success');
        if (quickFillCard) {
            quickFillCard.style.display = 'none';
        }
    };

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + Enter untuk "Simpan & Tambahkan yang Lain"
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            setSaveAction('save_and_add');
            document.getElementById('contactForm').submit();
        }
        // Escape untuk batal
        else if (e.key === 'Escape') {
            window.location.href = "{{ route('contacts.index') }}";
        }
    });
});
</script>

<style>
    /* Animasi untuk feedback visual */
    .btn {
        transition: all 0.2s ease;
    }

    .btn:active {
        transform: translateY(1px);
    }

    /* Highlight untuk field yang focus */
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endsection