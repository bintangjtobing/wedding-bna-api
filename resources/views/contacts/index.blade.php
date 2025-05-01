@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Daftar Kontak</h1>
    <div>
        <div class="btn-group mb-2">
            <a href="{{ route('contacts.index') }}"
                class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }}">Semua</a>
            <a href="{{ route('contacts.index', ['status' => 'belum_dikirim']) }}"
                class="btn btn-outline-warning {{ request('status') == 'belum_dikirim' ? 'active' : '' }}">Belum
                Dikirim</a>
            <a href="{{ route('contacts.index', ['status' => 'terkirim']) }}"
                class="btn btn-outline-success {{ request('status') == 'terkirim' ? 'active' : '' }}">Terkirim</a>
            <a href="{{ route('contacts.index', ['status' => 'gagal']) }}"
                class="btn btn-outline-danger {{ request('status') == 'gagal' ? 'active' : '' }}">Gagal</a>
        </div>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">Tambah Kontak Baru</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($contacts->isEmpty())
        <p class="text-center">Belum ada kontak yang ditambahkan.</p>
        @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Nomor Telepon</th>
                        <th>Status Undangan</th>
                        <th>Tanggal Kirim</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contacts as $contact)
                    <tr>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->phone_number }}</td>
                        <td>
                            @if($contact->invitation_status == 'belum_dikirim')
                            <span class="badge bg-warning">Belum Dikirim</span>
                            @elseif($contact->invitation_status == 'terkirim')
                            <span class="badge bg-success">Terkirim</span>
                            @else
                            <span class="badge bg-danger">Gagal</span>
                            @endif
                        </td>
                        <td>{{ $contact->sent_at ? $contact->sent_at->format('d M Y H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kontak ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
