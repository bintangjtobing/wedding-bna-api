@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        Dashboard
    </div>
    <div class="card-body">
        <h5 class="card-title">Selamat datang, {{ $currentAdmin->name }}!</h5>
        <p class="card-text">Role: {{ $currentAdmin->role == 'groom' ? 'Mempelai Pria' : 'Mempelai Wanita' }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">Kontak Saya</div>
            <div class="card-body">
                <h1 class="display-4">{{ $contactCount }}</h1>
                <p class="card-text">Total kontak yang Anda miliki</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('contacts.index') }}" class="btn btn-primary">Lihat Kontak</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">Click Analytics</div>
            <div class="card-body">
                <h3 class="text-primary">{{ number_format($clickAnalytics['total_clicks']) }}</h3>
                <p class="card-text mb-1">Total Clicks</p>
                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted">Unique Visitors</small>
                        <div class="fw-bold text-success">{{ number_format($clickAnalytics['unique_visitors']) }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Countries</small>
                        <div class="fw-bold text-info">{{ $clickAnalytics['countries_reached'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">Status Undangan Saya</div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="progress" style="height: 25px;">
                        @php
                        $totalContacts = $contactCount > 0 ? $contactCount : 1;
                        $sentPercentage = ($sentInvitations / $totalContacts) * 100;
                        $pendingPercentage = ($pendingInvitations / $totalContacts) * 100;
                        $failedPercentage = ($failedInvitations / $totalContacts) * 100;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $sentPercentage }}%"
                            aria-valuenow="{{ $sentPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $sentInvitations }}
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pendingPercentage }}%"
                            aria-valuenow="{{ $pendingPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $pendingInvitations }}
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $failedPercentage }}%"
                            aria-valuenow="{{ $failedPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $failedInvitations }}
                        </div>
                    </div>
                </div>
                <div class="row text-center my-2">
                    <div class="col">
                        <span class="badge bg-success">{{ $sentInvitations }}</span>
                    </div>
                    <div class="col">
                        <span class="badge bg-warning">{{ $pendingInvitations }}</span>
                    </div>
                    <div class="col">
                        <span class="badge bg-danger">{{ $failedInvitations }}</span>
                    </div>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('messages.create') }}" class="btn btn-success">Kirim Pesan Baru</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">Statistik Kontak</div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Mempelai Pria ({{ $groomContactCount }} kontak)</h6>
                    <div class="progress mb-2" style="height: 20px;">
                        @php
                        $groomTotal = $groomContactCount > 0 ? $groomContactCount : 1;
                        $groomSentPercentage = ($groomSentCount / $groomTotal) * 100;
                        $groomPendingPercentage = ($groomPendingCount / $groomTotal) * 100;
                        $groomFailedPercentage = ($groomFailedCount / $groomTotal) * 100;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $groomSentPercentage }}%" aria-valuenow="{{ $groomSentPercentage }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $groomSentCount }}
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $groomPendingPercentage }}%" aria-valuenow="{{ $groomPendingPercentage }}"
                            aria-valuemin="0" aria-valuemin="0" aria-valuemax="100">
                            {{ $groomPendingCount }}
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar"
                            style="width: {{ $groomFailedPercentage }}%" aria-valuenow="{{ $groomFailedPercentage }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $groomFailedCount }}
                        </div>
                    </div>
                    <small class="text-muted">{{ $groomClicks }} clicks, {{ $groomUniqueVisitors }} visitors</small>
                </div>

                <div class="mb-3">
                    <h6>Mempelai Wanita ({{ $brideContactCount }} kontak)</h6>
                    <div class="progress mb-2" style="height: 20px;">
                        @php
                        $brideTotal = $brideContactCount > 0 ? $brideContactCount : 1;
                        $brideSentPercentage = ($brideSentCount / $brideTotal) * 100;
                        $bridePendingPercentage = ($bridePendingCount / $brideTotal) * 100;
                        $brideFailedPercentage = ($brideFailedCount / $brideTotal) * 100;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $brideSentPercentage }}%" aria-valuenow="{{ $brideSentPercentage }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $brideSentCount }}
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $bridePendingPercentage }}%" aria-valuenow="{{ $bridePendingPercentage }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $bridePendingCount }}
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar"
                            style="width: {{ $brideFailedPercentage }}%" aria-valuenow="{{ $brideFailedPercentage }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $brideFailedCount }}
                        </div>
                    </div>
                    <small class="text-muted">{{ $brideClicks }} clicks, {{ $brideUniqueVisitors }} visitors</small>
                </div>

                <div class="mt-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Kontak:</span>
                        <strong>{{ $groomContactCount + $brideContactCount }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Clicks:</span>
                        <strong>{{ $totalClicks }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Terkirim:</span>
                        <strong>{{ $groomSentCount + $brideSentCount }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Click Analytics Section -->
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Top Countries Reached</h5>
            </div>
            <div class="card-body">
                @if($clickAnalytics['top_countries']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Clicks</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clickAnalytics['top_countries'] as $country => $count)
                            <tr>
                                <td>{{ $country }}</td>
                                <td>{{ $count }}</td>
                                <td>
                                    <div class="progress" style="height: 15px;">
                                        <div class="progress-bar bg-info"
                                            style="width: {{ ($count / $clickAnalytics['total_clicks']) * 100 }}%">
                                        </div>
                                    </div>
                                    {{ round(($count / $clickAnalytics['total_clicks']) * 100, 1) }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-muted">Belum ada data click</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Device Breakdown</h5>
            </div>
            <div class="card-body">
                @if($clickAnalytics['device_breakdown']->count() > 0)
                <div class="row">
                    @foreach($clickAnalytics['device_breakdown'] as $device => $count)
                    <div class="col-6 mb-3">
                        <div class="text-center">
                            <h4 class="text-primary">{{ $count }}</h4>
                            <p class="mb-0">{{ $device }}</p>
                            <small class="text-muted">{{ round(($count / $clickAnalytics['total_clicks']) * 100, 1)
                                }}%</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-muted">Belum ada data device</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Recent Click Activities</h5>
            </div>
            <div class="card-body">
                @if($clickAnalytics['recent_clicks']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Device</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clickAnalytics['recent_clicks'] as $click)
                            <tr>
                                <td>
                                    <strong>{{ $click->name }}</strong><br>
                                    <small class="text-muted">{{ $click->username }}</small>
                                </td>
                                <td>
                                    @if($click->country)
                                    {{ $click->country_emoji }} {{ $click->city }}, {{ $click->country }}
                                    @else
                                    <span class="text-muted">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($click->device_name)
                                    {{ $click->device_name }}<br>
                                    <small class="text-muted">{{ $click->os_name }} • {{ $click->browser_name }}</small>
                                    @else
                                    <span class="text-muted">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $click->clicked_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-muted">Belum ada aktivitas click</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Status Pengiriman Undangan -->
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                Status Pengiriman Undangan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Jumlah</th>
                                <th>Persentase</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-success">Terkirim</span></td>
                                <td>{{ $sentInvitations }}</td>
                                <td>
                                    @php
                                    $sentPercent = $contactCount > 0 ? round(($sentInvitations / $contactCount) * 100,
                                    1) : 0;
                                    @endphp
                                    {{ $sentPercent }}%
                                </td>
                                <td>
                                    <a href="{{ route('contacts.index', ['status' => 'terkirim']) }}"
                                        class="btn btn-sm btn-outline-success">Lihat Kontak</a>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning">Belum Dikirim</span></td>
                                <td>{{ $pendingInvitations }}</td>
                                <td>
                                    @php
                                    $pendingPercent = $contactCount > 0 ? round(($pendingInvitations / $contactCount) *
                                    100, 1) : 0;
                                    @endphp
                                    {{ $pendingPercent }}%
                                </td>
                                <td>
                                    <a href="{{ route('contacts.index', ['status' => 'belum_dikirim']) }}"
                                        class="btn btn-sm btn-outline-warning">Lihat Kontak</a>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">Gagal</span></td>
                                <td>{{ $failedInvitations }}</td>
                                <td>
                                    @php
                                    $failedPercent = $contactCount > 0 ? round(($failedInvitations / $contactCount) *
                                    100, 1) : 0;
                                    @endphp
                                    {{ $failedPercent }}%
                                </td>
                                <td>
                                    <a href="{{ route('contacts.index', ['status' => 'gagal']) }}"
                                        class="btn btn-sm btn-outline-danger">Lihat Kontak</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection