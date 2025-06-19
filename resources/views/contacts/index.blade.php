@extends('layouts.app')
@section('title', 'Contact - Wedding Invitation')
@section('breadcrumb', 'Contacts')
@section('page-title', 'Contacts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Daftar Kontak</h1>
    <div>
        <a href="{{ route('contacts.import') }}" class="btn btn-success me-2">Import Kontak</a>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">Tambah Kontak Baru</a>
    </div>
</div>

<!-- Status Filter Buttons -->
<div class="btn-group mb-3">
    <a href="{{ route('contacts.index') }}"
        class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }}">Semua</a>
    <a href="{{ route('contacts.index', ['status' => 'belum_dikirim']) }}"
        class="btn btn-outline-warning {{ request('status') == 'belum_dikirim' ? 'active' : '' }}">Belum Dikirim</a>
    <a href="{{ route('contacts.index', ['status' => 'terkirim']) }}"
        class="btn btn-outline-success {{ request('status') == 'terkirim' ? 'active' : '' }}">Terkirim</a>
    <a href="{{ route('contacts.index', ['status' => 'gagal']) }}"
        class="btn btn-outline-danger {{ request('status') == 'gagal' ? 'active' : '' }}">Gagal</a>
</div>

@if(!$contacts->isEmpty())
<div class="mb-3">
    <form action="{{ route('contacts.resetAll') }}" method="POST" class="d-inline me-2">
        @csrf
        @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <button type="submit" class="btn btn-warning"
            onclick="return confirm('Apakah Anda yakin ingin mereset semua status undangan yang ditampilkan?')">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Status Undangan
            @if(request('status'))
            {{ request('status') == 'belum_dikirim' ? 'Belum Dikirim' : (request('status') == 'terkirim' ? 'Terkirim' :
            'Gagal') }}
            @else
            Semua
            @endif
        </button>
    </form>

    <a href="{{ route('contacts.export') }}{{ request('status') ? '?status='.request('status') : '' }}"
        class="btn btn-info">
        <i class="bi bi-download"></i> Export Kontak
    </a>
</div>
@endif

<!-- Bulk Actions & Search -->
<div class="d-flex justify-content-between mb-3">
    <div>
        @if(!$contacts->isEmpty())
        <form action="{{ route('contacts.bulkDelete') }}" method="POST" id="bulk-delete-form" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger" id="bulk-delete-btn" disabled
                onclick="return confirm('Apakah Anda yakin ingin menghapus kontak yang dipilih?')">
                <i class="bi bi-trash"></i> Hapus yang Dipilih <span id="selected-count"></span>
            </button>
        </form>
        <button type="button" class="btn btn-outline-secondary ms-2" id="select-all-btn">Pilih Semua</button>
        <button type="button" class="btn btn-outline-secondary ms-1" id="deselect-all-btn">Batal Pilih</button>
        @endif
    </div>
    <div>
        <form action="{{ route('contacts.index') }}" method="GET" class="d-flex">
            @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" class="form-control me-2" placeholder="Cari kontak..."
                value="{{ request('search') }}" style="min-width: 250px;">
            <button type="submit" class="btn btn-outline-primary">Cari</button>
        </form>
    </div>
</div>

<!-- Contacts Table -->
<div class="card">
    <div class="card-body">
        @if($contacts->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-person-plus" style="font-size: 4rem; color: #6c757d;"></i>
            <h5 class="mt-3 text-muted">Belum ada kontak yang ditambahkan</h5>
            <p class="text-muted">Mulai dengan menambah kontak baru atau import dari file CSV</p>
            <a href="{{ route('contacts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Tambah Kontak Pertama
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                        </th>
                        <th style="width: 200px;">Nama</th>
                        <th style="width: 150px;">Username</th>
                        <th style="width: 100px;">Panggilan</th>
                        <th style="width: 150px;">Nomor Telepon</th>
                        <th style="width: 120px;">Status Undangan</th>
                        <th style="width: 130px;">Waktu Kirim</th>
                        <th style="width: 120px;">Analytics</th>
                        <th style="width: 200px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contacts as $contact)
                    <tr>
                        <td>
                            <input type="checkbox" name="contact_ids[]" form="bulk-delete-form"
                                value="{{ $contact->id }}" class="contact-checkbox form-check-input">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm bg-gradient-primary me-2">
                                    <span class="text-white text-xs font-weight-bold">
                                        {{ strtoupper(substr($contact->name, 0, 2)) }}
                                    </span>
                                </div>
                                <span class="fw-bold">{{ $contact->name }}</span>
                            </div>
                        </td>
                        <td>
                            <code class="text-sm">{{ $contact->username ?? '-' }}</code>
                        </td>
                        <td>
                            <span class="text-muted">{{ $contact->greeting ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="font-monospace">+{{ $contact->country_code }} {{ $contact->phone_number
                                }}</span>
                        </td>
                        <td>
                            @if($contact->invitation_status == 'belum_dikirim')
                            <span class="badge bg-warning text-dark">Belum Dikirim</span>
                            @elseif($contact->invitation_status == 'terkirim')
                            <span class="badge bg-success">Terkirim</span>
                            @else
                            <span class="badge bg-danger">Gagal</span>
                            @endif
                        </td>
                        <td>
                            @if($contact->sent_at)
                            <div class="text-sm">
                                <div class="fw-bold">{{ $contact->sent_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $contact->sent_at->format('H:i') }}</small>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($contact->click_logs_count > 0)
                            <div class="small">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-mouse text-primary me-1"></i>
                                    <strong class="text-primary">{{ $contact->click_logs_count }}</strong>
                                    <span class="text-muted ms-1">clicks</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-people text-success me-1"></i>
                                    <strong class="text-success">{{ $contact->unique_visitors_count }}</strong>
                                    <span class="text-muted ms-1">visitors</span>
                                </div>
                                @if($contact->latest_click)
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $contact->latest_click->clicked_at->diffForHumans() }}
                                </small>
                                @endif
                            </div>
                            @else
                            <div class="text-center">
                                <i class="bi bi-bar-chart text-muted" style="font-size: 1.2rem;"></i>
                                <div class="small text-muted">No data</div>
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Contact actions">
                                <!-- View Button -->
                                <a href="{{ route('contacts.show', $contact) }}" class="btn btn-outline-info btn-sm"
                                    title="View Details" data-bs-toggle="tooltip">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-warning btn-sm"
                                    title="Edit Contact" data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <!-- Reset Button -->
                                <button type="button" class="btn btn-outline-secondary btn-sm" title="Reset Status"
                                    data-bs-toggle="tooltip" onclick="resetContactStatus({{ $contact->id }})">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-outline-danger btn-sm" title="Delete Contact"
                                    data-bs-toggle="tooltip" onclick="deleteContact({{ $contact->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <!-- Hidden Forms -->
                            <form id="reset-form-{{ $contact->id }}"
                                action="{{ route('contacts.resetStatus', $contact) }}" method="POST" class="d-none">
                                @csrf
                                @method('PATCH')
                            </form>

                            <form id="delete-form-{{ $contact->id }}" action="{{ route('contacts.destroy', $contact) }}"
                                method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                <p class="text-muted mb-0 small">
                    Showing {{ $contacts->firstItem() ?? 0 }} to {{ $contacts->lastItem() ?? 0 }}
                    of {{ number_format($contacts->total()) }} results
                </p>
            </div>
            <div>
                {{ $contacts->appends(request()->input())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    /* Custom styling untuk table yang lebih rapi */
    .table th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
        padding: 1rem 0.75rem;
    }

    .table td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }

    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }

    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    /* Hover effects */
    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .btn-outline-info:hover,
    .btn-outline-warning:hover,
    .btn-outline-secondary:hover,
    .btn-outline-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
</style>

<script>
    // Improved JavaScript untuk functionality yang lebih baik
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const selectedCountSpan = document.getElementById('selected-count');
    const selectAllBtn = document.getElementById('select-all-btn');
    const deselectAllBtn = document.getElementById('deselect-all-btn');

    function updateBulkDeleteButton() {
        let checkedCount = document.querySelectorAll('.contact-checkbox:checked').length;
        bulkDeleteBtn.disabled = checkedCount === 0;

        if (checkedCount > 0) {
            selectedCountSpan.textContent = '(' + checkedCount + ')';
            bulkDeleteBtn.classList.remove('btn-danger');
            bulkDeleteBtn.classList.add('btn-warning');
        } else {
            selectedCountSpan.textContent = '';
            bulkDeleteBtn.classList.remove('btn-warning');
            bulkDeleteBtn.classList.add('btn-danger');
        }
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            contactCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    // Individual checkbox functionality
    contactCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = [...contactCheckboxes].every(cb => cb.checked);
            }
            updateBulkDeleteButton();
        });
    });

    // Select all button
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            contactCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
            }
            updateBulkDeleteButton();
        });
    }

    // Deselect all button
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            contactCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            }
            updateBulkDeleteButton();
        });
    }

    // Initialize
    updateBulkDeleteButton();
});

// Action functions
function resetContactStatus(contactId) {
    if (confirm('Apakah Anda yakin ingin mereset status undangan kontak ini?')) {
        document.getElementById('reset-form-' + contactId).submit();
    }
}

function deleteContact(contactId) {
    if (confirm('Apakah Anda yakin ingin menghapus kontak ini? Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('delete-form-' + contactId).submit();
    }
}

// Search functionality enhancement
document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        this.closest('form').submit();
    }
});
</script>
@endsection