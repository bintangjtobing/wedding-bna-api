@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        Import Kontak dari CSV
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h5>Petunjuk Import:</h5>
            <ol>
                <li>File harus berformat CSV (Comma Separated Values).</li>
                <li>Pastikan file CSV memiliki kolom "nama" dan "telepon" (atau "nomor" atau "hp" atau "no_hp").</li>
                <li>Ukuran file maksimal 2MB.</li>
                <li>Nomor telepon yang sudah terdaftar tidak akan diimport ulang.</li>
            </ol>
        </div>

        <form action="{{ route('contacts.processImport') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="csv_file" class="form-label">File CSV</label>
                <input type="file" class="form-control @error('csv_file') is-invalid @enderror" id="csv_file"
                    name="csv_file" required>
                @error('csv_file')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mt-2">
                <a href="{{ route('contacts.exportTemplate') }}" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-download"></i> Download Template CSV
                </a>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Import</button>
            <a href="{{ route('contacts.index') }}" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
</div>
@endsection
