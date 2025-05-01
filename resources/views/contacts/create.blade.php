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
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                    name="phone_number" value="{{ old('phone_number') }}" required>
                @error('phone_number')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
