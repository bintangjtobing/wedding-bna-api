@extends('layouts.app')

@section('title', 'Dashboard - Wedding Invitation')
@section('breadcrumb', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class=" row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                Selamat datang,</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $currentAdmin->name }}!
                                <span class="text-success text-sm font-weight-bolder">
                                    @if(isset($currentAdmin->role))
                                    ({{ $currentAdmin->role == 'groom' ? 'Mempelai Pria'
                                    : 'Mempelai Wanita' }})
                                    @else
                                    (Wedding Admin)
                                    @endif
                                </span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="fas fa-smile text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards Row 1 -->
<div class="row">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total
                                Kontak</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $contactCount }}
                                <span class="text-success text-sm font-weight-bolder">kontak</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-dark shadow text-center border-radius-md">
                            <i class="fas fa-users text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total
                                Clicks</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ number_format($clickAnalytics['total_clicks']) }}
                                <span class="text-info text-sm font-weight-bolder">clicks</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="fas fa-mouse-pointer text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Unique
                                Visitors</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ number_format($clickAnalytics['unique_visitors']) }}
                                <span class="text-success text-sm font-weight-bolder">visitors</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                            <i class="fas fa-eye text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                Countries</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $clickAnalytics['countries_reached'] }}
                                <span class="text-warning text-sm font-weight-bolder">negara</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                            <i class="fas fa-globe text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invitation Status Row -->
<div class="row mt-4">
    <div class="col-lg-7 mb-lg-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="d-flex flex-column h-100">
                            <p class="mb-1 pt-2 text-bold">Status Undangan Saya</p>
                            <h5 class="font-weight-bolder">{{ $contactCount }} Total Kontak
                            </h5>
                            <p class="mb-5">Distribusi status pengiriman undangan pernikahan
                            </p>
                            <a class="text-body text-sm font-weight-bold mb-0 icon-move-right mt-auto"
                                href="{{ route('contacts.index') }}">
                                Lihat Semua Kontak
                                <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5 ms-auto text-center mt-5 mt-lg-0">
                        <div class="bg-gradient-primary border-radius-lg h-100">
                            <div class="position-relative d-flex align-items-center justify-content-center h-100">
                                <canvas id="invitationChart" width="100" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100 p-3">
            <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100"
                style="background-image: url('{{ asset('img/curved-images/curved1.jpg') }}');">
                <span class="mask bg-gradient-dark"></span>
                <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">
                    <h5 class="text-white font-weight-bolder mb-4 pt-2">Quick Actions</h5>
                    <p class="text-white">Aksi cepat untuk mengelola undangan pernikahan
                        Anda dengan mudah.</p>
                    <a class="text-white text-sm font-weight-bold mb-0 icon-move-right mt-auto"
                        href="{{ route('messages.create') }}">
                        Kirim Pesan Baru
                        <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Overall Statistics -->
<div class="row mt-4">
    <div class="col-lg-7 mb-lg-0 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="row">
                    <div class="col-lg-6 col-7">
                        <h6>Statistik Keseluruhan</h6>
                        <p class="text-sm mb-0">
                            <i class="fa fa-check text-info" aria-hidden="true"></i>
                            <span class="font-weight-bold ms-1">Gabungan data mempelai pria & wanita</span>
                        </p>
                    </div>
                    <div class="col-lg-6 col-5 my-auto text-end">
                        <div class="dropdown float-lg-end pe-4">
                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa fa-ellipsis-v text-secondary"></i>
                            </a>
                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                <li><a class="dropdown-item border-radius-md" href="{{ route('analytics.index') }}">View
                                        Analytics</a></li>
                                <li><a class="dropdown-item border-radius-md"
                                        href="{{ route('contacts.export') }}">Export Data</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kategori
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Mempelai Pria</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Mempelai Wanita</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <img src="{{ asset('img/small-logos/logo-xd.svg') }}"
                                                class="avatar avatar-sm me-3" alt="xd">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Total Kontak</h6>
                                            <p class="text-xs text-secondary mb-0">Jumlah undangan</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $groomContactCount }}</p>
                                    <p class="text-xs text-secondary mb-0">kontak</p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ $brideContactCount }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $groomContactCount +
                                        $brideContactCount }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <img src="{{ asset('img/small-logos/logo-atlassian.svg') }}"
                                                class="avatar avatar-sm me-3" alt="atlassian">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Terkirim</h6>
                                            <p class="text-xs text-secondary mb-0">Undangan berhasil</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $groomSentCount }}</p>
                                    <p class="text-xs text-secondary mb-0">terkirim</p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ $brideSentCount }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $groomSentCount +
                                        $brideSentCount }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <img src="{{ asset('img/small-logos/logo-slack.svg') }}"
                                                class="avatar avatar-sm me-3" alt="team">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Total Clicks</h6>
                                            <p class="text-xs text-secondary mb-0">Engagement rate</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $groomClicks }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $groomUniqueVisitors }} visitors</p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ $brideClicks }}</span>
                                    <br><span class="text-xs text-secondary">{{ $brideUniqueVisitors }} visitors</span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $totalClicks }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Device Breakdown</h6>
                <p class="text-sm">
                    <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
                    <span class="font-weight-bold">Analisis perangkat pengunjung</span>
                </p>
            </div>
            <div class="card-body p-3">
                @if($clickAnalytics['device_breakdown']->count() > 0)
                <div class="chart">
                    <canvas id="deviceBreakdownChart" class="chart-canvas" height="300"></canvas>
                </div>
                <div class="row mt-3">
                    @foreach($clickAnalytics['device_breakdown'] as $device => $count)
                    <div class="col-6 mb-2">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-xs me-2
                                @if($device == 'mobile') bg-gradient-success
                                @elseif($device == 'desktop') bg-gradient-primary
                                @elseif($device == 'tablet') bg-gradient-info
                                @else bg-gradient-secondary @endif
                                text-center border-radius-sm">
                                <i class="
                                    @if($device == 'mobile') fas fa-mobile-button
                                    @elseif($device == 'desktop') fas fa-tv-2
                                    @elseif($device == 'tablet') fas fa-tablet-button
                                    @else fas fa-controller @endif
                                    text-white text-xs opacity-10" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-sm">{{ $count }}</h6>
                                <p class="text-xs mb-0">{{ ucfirst($device) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">Belum ada data device</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>Recent Click Activities</h6>
                <p class="text-sm">
                    <i class="fa fa-check text-info" aria-hidden="true"></i>
                    <span class="font-weight-bold ms-1">Aktivitas terbaru dari pengunjung undangan</span>
                </p>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                @if($clickAnalytics['recent_clicks']->count() > 0)
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contact
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Location</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Device</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Time</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clickAnalytics['recent_clicks'] as $click)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <div class="avatar avatar-sm bg-gradient-secondary me-3">
                                                <span class="text-white text-xs">{{ substr($click->name, 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $click->name }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ '@' . ($click->username ??
                                                'no-username') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($click->country)
                                    <p class="text-xs font-weight-bold mb-0">{{ $click->country_emoji }} {{ $click->city
                                        }}, {{ $click->country }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $click->ip_address }}</p>
                                    @else
                                    <p class="text-xs text-secondary mb-0">Unknown Location</p>
                                    @endif
                                </td>
                                <td class="align-middle text-center text-sm">
                                    @if($click->device_name)
                                    <span class="badge badge-sm
                                        @if($click->device_type == 'mobile') bg-gradient-success
                                        @elseif($click->device_type == 'desktop') bg-gradient-primary
                                        @elseif($click->device_type == 'tablet') bg-gradient-info
                                        @else bg-gradient-secondary @endif">
                                        {{ $click->device_name }}
                                    </span>
                                    <p class="text-xs text-secondary mb-0">{{ $click->browser_name }}</p>
                                    @else
                                    <span class="text-secondary text-xs font-weight-bold">Unknown</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{
                                        $click->clicked_at->diffForHumans() }}</span>
                                </td>
                                <td class="align-middle">
                                    <a href="{{ route('contacts.show', $click->contact_id) }}"
                                        class="text-secondary font-weight-bold text-xs" data-toggle="tooltip"
                                        data-original-title="View contact">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <img src="{{ asset('img/illustrations/rocket-white.png') }}" alt="rocket" class="img-fluid"
                        style="max-height: 200px;">
                    <h5 class="mt-3">Belum ada aktivitas click</h5>
                    <p class="text-muted">Aktivitas akan muncul setelah tamu mengakses undangan.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Pastikan Chart.js sudah loaded
document.addEventListener('DOMContentLoaded', function() {
    // Invitation Status Chart
    @if($contactCount > 0)
    var ctx1 = document.getElementById("invitationChart");
    if (ctx1) {
        ctx1 = ctx1.getContext("2d");
        new Chart(ctx1, {
            type: "doughnut",
            data: {
                labels: ['Terkirim', 'Belum Dikirim', 'Gagal'],
                datasets: [{
                    data: [{{ $sentInvitations }}, {{ $pendingInvitations }}, {{ $failedInvitations }}],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    @endif

    // Device Breakdown Chart
    @if($clickAnalytics['device_breakdown']->count() > 0)
    var ctx2 = document.getElementById("deviceBreakdownChart");
    if (ctx2) {
        ctx2 = ctx2.getContext("2d");
        new Chart(ctx2, {
            type: "doughnut",
            data: {
                labels: {!! json_encode(array_map('ucfirst', array_keys($clickAnalytics['device_breakdown']->toArray()))) !!},
                datasets: [{
                    data: {!! json_encode(array_values($clickAnalytics['device_breakdown']->toArray())) !!},
                    backgroundColor: ['#28a745', '#007bff', '#17a2b8', '#6c757d'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush