@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Daftar Kontak</h1>
    <div>
        <a href="{{ route('contacts.import') }}" class="btn btn-success me-2">Import Kontak</a>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">Tambah Kontak Baru</a>
    </div>
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
<div class="d-flex justify-content-between mb-3">
    <div>
        @if(!$contacts->isEmpty())
        <div class="btn-group">
            <a href="{{ route('contacts.export') }}{{ request('status') ? '?status='.request('status') : '' }}"
                class="btn btn-info">
                <i class="bi bi-download"></i> Export Kontak
            </a>
            <form action="{{ route('contacts.resetAll') }}" method="POST" class="d-inline">
                @csrf
                @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <button type="submit" class="btn btn-warning"
                    onclick="return confirm('Apakah Anda yakin ingin mereset semua status undangan yang ditampilkan?')">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Status
                </button>
            </form>
        </div>
        @endif
    </div>
    <div>
        <form action="{{ route('contacts.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari kontak..."
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Cari</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        @if($contacts->isEmpty())
        <p class="text-center">Belum ada kontak yang ditambahkan.</p>
        @else
        <form id="bulk-delete-form" action="{{ route('contacts.bulkDelete') }}" method="POST">
            @csrf
            <div class="mb-3">
                <button type="submit" class="btn btn-danger" id="bulk-delete-btn" disabled
                    onclick="return confirm('Apakah Anda yakin ingin menghapus kontak yang dipilih?')">
                    <i class="bi bi-trash"></i> Hapus yang Dipilih
                </button>
                <button type="button" class="btn btn-outline-secondary" id="select-all-btn">Pilih Semua</button>
                <button type="button" class="btn btn-outline-secondary" id="deselect-all-btn">Batal Pilih</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all-checkbox"></th>
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
                            <td><input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}"
                                    class="contact-checkbox"></td>
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
                                <div class="btn-group">
                                    <a href="{{ route('contacts.edit', $contact) }}"
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <a href="#" class="btn btn-sm btn-outline-warning"
                                        onclick="event.preventDefault(); document.getElementById('reset-form-{{ $contact->id }}').submit();">
                                        Reset
                                    </a>
                                    <form id="reset-form-{{ $contact->id }}"
                                        action="{{ route('contacts.resetStatus', $contact) }}" method="POST"
                                        class="d-none">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kontak ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>

        <script>
            // Script untuk menangani checkbox
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('select-all-checkbox');
                const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
                const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
                const selectAllBtn = document.getElementById('select-all-btn');
                const deselectAllBtn = document.getElementById('deselect-all-btn');

                // Fungsi untuk mengecek apakah ada checkbox yang tercentang
                function updateBulkDeleteButton() {
                    let checkedCount = document.querySelectorAll('.contact-checkbox:checked').length;
                    bulkDeleteBtn.disabled = checkedCount === 0;
                    bulkDeleteBtn.textContent = checkedCount > 0 ? `Hapus (${checkedCount})` : 'Hapus yang Dipilih';
                }

                // Event listener untuk select all checkbox
                selectAllCheckbox.addEventListener('change', function() {
                    contactCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkDeleteButton();
                });

                // Event listener untuk masing-masing checkbox kontak
                contactCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        selectAllCheckbox.checked = [...contactCheckboxes].every(cb => cb.checked);
                        updateBulkDeleteButton();
                    });
                });

                // Event listener untuk tombol "Pilih Semua"
                selectAllBtn.addEventListener('click', function() {
                    contactCheckboxes.forEach(checkbox => {
                        checkbox.checked = true;
                    });
                    selectAllCheckbox.checked = true;
                    updateBulkDeleteButton();
                });

                // Event listener untuk tombol "Batal Pilih"
                deselectAllBtn.addEventListener('click', function() {
                    contactCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    selectAllCheckbox.checked = false;
                    updateBulkDeleteButton();
                });

                // Inisialisasi
                updateBulkDeleteButton();
            });
        </script>
        @endif
        <div class="d-flex justify-content-center">
            {{ $contacts->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
