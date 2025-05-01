<!-- resources/views/contacts/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Tambah Kontak Baru</div>
    <div class="card-body">
        <form action="{{ route('contacts.store') }}" method="POST">
            @csrf
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

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
    // Script untuk mengubah kode negara secara otomatis saat negara berubah
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endsection
