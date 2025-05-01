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
                    name="message_content" rows="5"
                    required>{{ old('message_content', isset($template) ? $template->content : '') }}</textarea>
                <div class="form-text">Gunakan [NAMA] untuk menyertakan nama tamu di pesan.</div>
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

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="only_pending" id="only_pending" value="1" {{
                        old('only_pending') ? 'checked' : '' }}>
                    <label class="form-check-label" for="only_pending">
                        Hanya kirim ke kontak yang belum dikirimi undangan
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Kirim Pesan</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
