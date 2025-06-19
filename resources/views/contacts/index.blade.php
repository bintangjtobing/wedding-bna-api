@extends('layouts.app')

@section('content')
<div class="container-fluid px-3">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h1 class="h4 mb-3 mb-md-0">📱 Daftar Kontak</h1>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <a href="{{ route('contacts.import') }}" class="btn btn-success btn-sm">
                <i class="bi bi-upload"></i> Import
            </a>
            <a href="{{ route('contacts.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus"></i> Tambah
            </a>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-primary text-white h-100">
                <div class="card-body p-3 text-center">
                    <div class="h2 mb-1">{{ $stats['total'] }}</div>
                    <small class="opacity-75">Total Kontak</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-success text-white h-100">
                <div class="card-body p-3 text-center">
                    <div class="h2 mb-1">{{ $stats['terkirim'] }}</div>
                    <small class="opacity-75">Terkirim</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-warning text-white h-100">
                <div class="card-body p-3 text-center">
                    <div class="h2 mb-1">{{ $stats['belum_dikirim'] }}</div>
                    <small class="opacity-75">Belum Dikirim</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-danger text-white h-100">
                <div class="card-body p-3 text-center">
                    <div class="h2 mb-1">{{ $stats['gagal'] }}</div>
                    <small class="opacity-75">Gagal</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Country Breakdown (Mobile Collapsible) -->
    <div class="card mb-4">
        <div class="card-header">
            <button class="btn btn-link p-0 text-decoration-none w-100 text-start" type="button"
                data-bs-toggle="collapse" data-bs-target="#countryBreakdown">
                🌍 Breakdown per Negara <i class="bi bi-chevron-down float-end"></i>
            </button>
        </div>
        <div class="collapse" id="countryBreakdown">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-sm-3">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fw-bold">🇮🇩 {{ $stats['countries']['ID'] }}</div>
                            <small class="text-muted">Indonesia</small>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fw-bold">🇲🇾 {{ $stats['countries']['MY'] }}</div>
                            <small class="text-muted">Malaysia</small>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fw-bold">🇸🇬 {{ $stats['countries']['SG'] }}</div>
                            <small class="text-muted">Singapura</small>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fw-bold">🌍 {{ $stats['countries']['OTHER'] }}</div>
                            <small class="text-muted">Lainnya</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('contacts.index') }}"
            class="btn btn-sm {{ !request('status') && !request('country') ? 'btn-primary' : 'btn-outline-secondary' }}">
            Semua
        </a>
        <a href="{{ route('contacts.index', ['status' => 'belum_dikirim']) }}"
            class="btn btn-sm {{ request('status') == 'belum_dikirim' ? 'btn-warning text-white' : 'btn-outline-warning' }}">
            Belum Dikirim
        </a>
        <a href="{{ route('contacts.index', ['status' => 'terkirim']) }}"
            class="btn btn-sm {{ request('status') == 'terkirim' ? 'btn-success' : 'btn-outline-success' }}">
            Terkirim
        </a>
        <a href="{{ route('contacts.index', ['status' => 'gagal']) }}"
            class="btn btn-sm {{ request('status') == 'gagal' ? 'btn-danger' : 'btn-outline-danger' }}">
            Gagal
        </a>
    </div>

    <!-- Country Filter Pills -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('contacts.index', ['country' => 'ID'] + request()->except(['country'])) }}"
            class="btn btn-sm {{ request('country') == 'ID' ? 'btn-info' : 'btn-outline-info' }}">
            🇮🇩 ID
        </a>
        <a href="{{ route('contacts.index', ['country' => 'MY'] + request()->except(['country'])) }}"
            class="btn btn-sm {{ request('country') == 'MY' ? 'btn-info' : 'btn-outline-info' }}">
            🇲🇾 MY
        </a>
        <a href="{{ route('contacts.index', ['country' => 'SG'] + request()->except(['country'])) }}"
            class="btn btn-sm {{ request('country') == 'SG' ? 'btn-info' : 'btn-outline-info' }}">
            🇸🇬 SG
        </a>
        <a href="{{ route('contacts.index', ['country' => 'OTHER'] + request()->except(['country'])) }}"
            class="btn btn-sm {{ request('country') == 'OTHER' ? 'btn-info' : 'btn-outline-info' }}">
            🌍 Lainnya
        </a>
    </div>

    <!-- Search Bar -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form action="{{ route('contacts.index') }}" method="GET" class="row g-2">
                @foreach(request()->except(['search']) as $key => $value)
                @if(is_array($value))
                @foreach($value as $v)
                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                @endforeach
                @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
                @endforeach
                <div class="col">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, telepon, username..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions (Desktop) -->
    <div class="d-none d-md-block mb-3">
        @if(!$contacts->isEmpty())
        <form action="{{ route('contacts.bulkDelete') }}" method="POST" id="bulk-delete-form"
            class="d-flex align-items-center gap-2">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm" id="bulk-delete-btn" disabled>
                <i class="bi bi-trash"></i> Hapus <span id="selected-count"></span>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="select-all-btn">Pilih Semua</button>
            <a href="{{ route('contacts.export') }}{{ request()->getQueryString() ? '?'.request()->getQueryString() : '' }}"
                class="btn btn-info btn-sm">
                <i class="bi bi-download"></i> Export
            </a>
        </form>
        @endif
    </div>

    <!-- Contact List -->
    @if($contacts->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">Belum ada kontak</h5>
            <p class="text-muted">Tambahkan kontak pertama Anda atau import dari file CSV</p>
            <a href="{{ route('contacts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Tambah Kontak
            </a>
        </div>
    </div>
    @else
    <!-- Mobile Card View -->
    <div class="d-block d-md-none">
        @foreach($contacts as $contact)
        <div class="card mb-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">{{ $contact->name }}</h6>
                        <small class="text-muted">{{ $contact->username }}</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('contacts.show', $contact) }}">
                                    <i class="bi bi-eye"></i> Detail
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('contacts.edit', $contact) }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="#"
                                    onclick="event.preventDefault(); if(confirm('Hapus kontak ini?')) document.getElementById('delete-form-{{ $contact->id }}').submit();">
                                    <i class="bi bi-trash"></i> Hapus
                                </a></li>
                        </ul>
                        <form id="delete-form-{{ $contact->id }}" action="{{ route('contacts.destroy', $contact) }}"
                            method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                </div>

                <div class="row g-2 text-sm">
                    <div class="col-6">
                        <small class="text-muted d-block">Telepon</small>
                        <span class="fw-medium">+{{ $contact->country_code }} {{ $contact->phone_number }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Negara</small>
                        <span class="fw-medium">
                            @if($contact->country == 'ID') 🇮🇩 Indonesia
                            @elseif($contact->country == 'MY') 🇲🇾 Malaysia
                            @elseif($contact->country == 'SG') 🇸🇬 Singapura
                            @else 🌍 {{ $contact->country }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        @if($contact->invitation_status == 'belum_dikirim')
                        <span class="badge bg-warning">Belum Dikirim</span>
                        @elseif($contact->invitation_status == 'terkirim')
                        <span class="badge bg-success">Terkirim</span>
                        @else
                        <span class="badge bg-danger">Gagal</span>
                        @endif
                    </div>

                    @if($contact->click_logs_count > 0)
                    <div class="text-end">
                        <small class="text-primary fw-bold">{{ $contact->click_logs_count }} clicks</small><br>
                        <small class="text-success">{{ $contact->unique_visitors_count }} visitors</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Desktop Table View -->
    <div class="d-none d-md-block">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50"><input type="checkbox" id="select-all-checkbox"></th>
                            <th>Kontak</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Analytics</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $contact)
                        <tr>
                            <td>
                                <input type="checkbox" name="contact_ids[]" form="bulk-delete-form"
                                    value="{{ $contact->id }}" class="contact-checkbox">
                            </td>
                            <td>
                                <div>
                                    <div class="fw-medium">{{ $contact->name }}</div>
                                    <small class="text-muted">
                                        {{ $contact->username }} •
                                        @if($contact->country == 'ID') 🇮🇩 ID
                                        @elseif($contact->country == 'MY') 🇲🇾 MY
                                        @elseif($contact->country == 'SG') 🇸🇬 SG
                                        @else 🌍 {{ $contact->country }}
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td>+{{ $contact->country_code }} {{ $contact->phone_number }}</td>
                            <td>
                                @if($contact->invitation_status == 'belum_dikirim')
                                <span class="badge bg-warning">Belum Dikirim</span>
                                @elseif($contact->invitation_status == 'terkirim')
                                <span class="badge bg-success">Terkirim</span>
                                @else
                                <span class="badge bg-danger">Gagal</span>
                                @endif
                            </td>
                            <td>
                                @if($contact->click_logs_count > 0)
                                <div class="small">
                                    <span class="text-primary fw-bold">{{ $contact->click_logs_count }}</span>
                                    clicks<br>
                                    <span class="text-success">{{ $contact->unique_visitors_count }}</span> visitors
                                </div>
                                @else
                                <span class="text-muted small">No activity</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('contacts.show', $contact) }}"
                                        class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('contacts.edit', $contact) }}"
                                        class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <small class="text-muted">
                Menampilkan {{ $contacts->firstItem() ?? 0 }} - {{ $contacts->lastItem() ?? 0 }}
                dari {{ $contacts->total() }} kontak
            </small>
        </div>
        <div>
            {{ $contacts->appends(request()->input())->links() }}
        </div>
    </div>
    @endif
</div>

<!-- JavaScript for bulk actions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const selectedCountSpan = document.getElementById('selected-count');
    const selectAllBtn = document.getElementById('select-all-btn');

    function updateBulkDeleteButton() {
        let checkedCount = document.querySelectorAll('.contact-checkbox:checked').length;
        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = checkedCount === 0;
        }
        if (selectedCountSpan) {
            selectedCountSpan.textContent = checkedCount > 0 ? '(' + checkedCount + ')' : '';
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            contactCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    contactCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = [...contactCheckboxes].every(cb => cb.checked);
            }
            updateBulkDeleteButton();
        });
    });

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

    updateBulkDeleteButton();
});
</script>
@endsection