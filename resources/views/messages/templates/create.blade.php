@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Tambah Template Pesan Baru</div>
    <div class="card-body">
        <form action="{{ route('templates.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="template_name" class="form-label">Nama Template</label>
                <input type="text" class="form-control @error('template_name') is-invalid @enderror" id="template_name" name="template_name" value="{{ old('template_name') }}" required>
                @error('template_name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Isi Pesan</label>
                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
                <div class="form-text">
                    Gunakan tanda berikut untuk personalisasi pesan:
                    <ul>
                        <li>[NAMA] - Akan diganti dengan nama kontak</li>
                    </ul>
                </div>
                @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan Template</button>
            <a href="{{ route('templates.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
