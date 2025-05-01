<!-- resources/views/contacts/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Detail Kontak</h1>
    <div>
        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-warning">Edit Kontak</a>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informasi Kontak</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nama</div>
                    <div class="col-md-8">{{ $contact->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Username</div>
                    <div class="col-md-8">{{ $contact->username }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nomor Telepon</div>
                    <div class="col-md-8">+{{ $contact->country_code }} {{ $contact->phone_number }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Negara</div>
                    <div class="col-md-8">
                        {{ $contact->country }}
                        @if($contact->country == 'ID')
                        (Indonesia)
                        @elseif($contact->country == 'MY')
                        (Malaysia)
                        @elseif($contact->country == 'SG')
                        (Singapura)
                        @elseif($contact->country == 'US')
                        (Amerika Serikat)
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Panggilan</div>
                    <div class="col-md-8">{{ $contact->greeting ?: '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Status Undangan</div>
                    <div class="col-md-8">
                        @if($contact->invitation_status == 'belum_dikirim')
                        <span class="badge bg-warning">Belum Dikirim</span>
                        @elseif($contact->invitation_status == 'terkirim')
                        <span class="badge bg-success">Terkirim</span>
                        @else
                        <span class="badge bg-danger">Gagal</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Waktu Pengiriman</div>
                    <div class="col-md-8">
                        @if($contact->sent_at)
                        {{ \Carbon\Carbon::parse($contact->sent_at)->format('d M Y H:i:s') }}
                        @else
                        -
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dibuat pada</div>
                    <div class="col-md-8">{{ $contact->created_at->format('d M Y H:i:s') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Diperbarui pada</div>
                    <div class="col-md-8">{{ $contact->updated_at->format('d M Y H:i:s') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Kontak
                    </a>

                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus kontak ini?')">
                            <i class="bi bi-trash"></i> Hapus Kontak
                        </button>
                    </form>

                    <form action="{{ route('contacts.resetStatus', $contact) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-warning w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Status Undangan
                        </button>
                    </form>

                    <a href="{{ route('messages.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-envelope"></i> Kirim Pesan
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Kontak dari {{ $contact->admin->role == 'groom' ? 'Mempelai Pria' : 'Mempelai Wanita'
                    }}</h5>
            </div>
            <div class="card-body">
                <p>Nama Admin: <strong>{{ $contact->admin->name }}</strong></p>
                <p>Email Admin: <strong>{{ $contact->admin->email }}</strong></p>
                <p>WhatsApp: <strong>{{ $contact->admin->whatsapp_number }}</strong></p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Riwayat Pengiriman</h5>
    </div>
    <div class="card-body">
        @if($contact->messageLogs->isEmpty())
        <p class="text-center">Belum ada riwayat pengiriman untuk kontak ini.</p>
        @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pesan</th>
                        <th>Status</th>
                        <th>Dikirim oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contact->messageLogs()->orderBy('created_at', 'desc')->get() as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td>{{ Str::limit($log->message->content, 100) }}</td>
                        <td>
                            @if($log->status == 'sent')
                            <span class="badge bg-success">Terkirim</span>
                            @elseif($log->status == 'pending')
                            <span class="badge bg-warning">Menunggu</span>
                            @else
                            <span class="badge bg-danger">Gagal</span>
                            @endif
                        </td>
                        <td>{{ $log->admin->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
