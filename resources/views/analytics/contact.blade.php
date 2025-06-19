@extends('layouts.app')
@section('title', 'Analytics Detail - Wedding Invitation')
@section('breadcrumb', 'Analytics')
@section('page-title', 'Contact Analytics Detail')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Analytics Detail - {{ $contact->name }}</h1>
    <div>
        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-info">
            <i class="fas fa-user me-1"></i>View Contact
        </a>
        <a href="{{ route('analytics.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Analytics
        </a>
    </div>
</div>

<!-- Contact Information Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-id-card me-2"></i>
                    Contact Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong><i class="fas fa-user me-1 text-muted"></i>Name:</strong><br>
                        <span class="fs-5">{{ $contact->name }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong><i class="fas fa-at me-1 text-muted"></i>Username:</strong><br>
                        <code class="fs-6">{{ $contact->username }}</code>
                    </div>
                    <div class="col-md-3">
                        <strong><i class="fas fa-phone me-1 text-muted"></i>Phone:</strong><br>
                        <span class="font-monospace">+{{ $contact->country_code }} {{ $contact->phone_number }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong><i class="fas fa-info-circle me-1 text-muted"></i>Status:</strong><br>
                        @if($contact->invitation_status == 'belum_dikirim')
                        <span class="badge bg-warning fs-6">
                            <i class="fas fa-clock me-1"></i>Belum Dikirim
                        </span>
                        @elseif($contact->invitation_status == 'terkirim')
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-check me-1"></i>Terkirim
                        </span>
                        @else
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-times me-1"></i>Gagal
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Primary Analytics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-gradient-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="display-4 fw-bold">{{ $stats['total_clicks'] ?? 0 }}</div>
                        <div class="fs-6">Total Clicks</div>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-gradient-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="display-4 fw-bold">{{ $stats['unique_ips'] ?? 0 }}</div>
                        <div class="fs-6">Unique Visitors</div>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-gradient-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="display-4 fw-bold">{{ $stats['countries'] ?? 0 }}</div>
                        <div class="fs-6">Countries</div>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-globe"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-gradient-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="display-4 fw-bold">{{ $stats['cities'] ?? 0 }}</div>
                        <div class="fs-6">Cities</div>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-city"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Time-based Analytics -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-secondary h-100">
            <div class="card-body py-3">
                <i class="fas fa-globe-americas text-secondary mb-2" style="font-size: 1.2rem;"></i>
                <div class="h5 fw-bold text-dark mb-1">{{ $stats['continents'] ?? 0 }}</div>
                <div class="text-muted small">Continents</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-secondary h-100">
            <div class="card-body py-3">
                <i class="fas fa-map-pin text-secondary mb-2" style="font-size: 1.2rem;"></i>
                <div class="h5 fw-bold text-dark mb-1">{{ $stats['zip_codes'] ?? 0 }}</div>
                <div class="text-muted small">Zip Codes</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-info h-100">
            <div class="card-body py-3">
                <i class="fas fa-calendar-day text-info mb-2" style="font-size: 1.2rem;"></i>
                <div class="h5 fw-bold text-info mb-1">{{ $stats['today_clicks'] ?? 0 }}</div>
                <div class="text-muted small">Today</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-primary h-100">
            <div class="card-body py-3">
                <i class="fas fa-calendar-week text-primary mb-2" style="font-size: 1.2rem;"></i>
                <div class="h5 fw-bold text-primary mb-1">{{ $stats['this_week_clicks'] ?? 0 }}</div>
                <div class="text-muted small">This Week</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-success h-100">
            <div class="card-body py-3">
                <i class="fas fa-calendar-alt text-success mb-2" style="font-size: 1.2rem;"></i>
                <div class="h5 fw-bold text-success mb-1">{{ $stats['this_month_clicks'] ?? 0 }}</div>
                <div class="text-muted small">This Month</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-warning h-100">
            <div class="card-body py-3">
                <i class="fas fa-mobile-alt text-warning mb-2" style="font-size: 1.2rem;"></i>
                <div class="h5 fw-bold text-warning mb-1">
                    {{ isset($stats['device_breakdown']['mobile']) ? round(($stats['device_breakdown']['mobile'] /
                    max($stats['total_clicks'], 1)) * 100) : 0 }}%
                </div>
                <div class="text-muted small">Mobile</div>
            </div>
        </div>
    </div>
</div>
<!-- Charts Section -->
<div class="row mb-4">
    <!-- Daily Clicks Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Daily Clicks (Last 30 Days)
                </h5>
            </div>
            <div class="card-body">
                @if(isset($dailyClicks) && $dailyClicks->count() > 0)
                <div style="position: relative; height: 250px;">
                    <canvas id="dailyClicksChart"></canvas>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-chart-line" style="font-size: 3rem; color: #6c757d;"></i>
                    <h6 class="mt-3 text-muted">No Daily Data Available</h6>
                    <p class="text-muted small">Daily click data will appear here once visitors start clicking.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Device Types Chart -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-devices me-2"></i>
                    Device Types
                </h5>
            </div>
            <div class="card-body">
                @if(isset($stats['device_breakdown']) && array_sum($stats['device_breakdown']) > 0)
                <div style="position: relative; height: 200px;">
                    <canvas id="deviceChart"></canvas>
                </div>
                <div class="row mt-3">
                    @foreach($stats['device_breakdown'] as $device => $count)
                    @if($count > 0)
                    <div class="col-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas
                                @if($device == 'mobile') fa-mobile-alt text-success
                                @elseif($device == 'desktop') fa-desktop text-primary
                                @elseif($device == 'tablet') fa-tablet-alt text-info
                                @else fa-question-circle text-secondary @endif
                                me-2"></i>
                            <small class="text-muted">{{ ucfirst($device) }}: {{ $count }}</small>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-devices" style="font-size: 3rem; color: #6c757d;"></i>
                    <h6 class="mt-3 text-muted">No Device Data</h6>
                    <p class="text-muted small">Device breakdown will show here.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Geographic & Browser Info -->
<div class="row mb-4">
    <!-- Top Countries -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-flag me-2"></i>
                    Top Countries
                </h5>
            </div>
            <div class="card-body">
                @if(isset($stats['top_countries']) && count($stats['top_countries']) > 0)
                <div class="list-group list-group-flush">
                    @foreach($stats['top_countries'] as $country => $count)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="fw-medium">
                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                            {{ $country }}
                        </span>
                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-globe" style="font-size: 2.5rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2 mb-0">No geographic data yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Cities -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-city me-2"></i>
                    Top Cities
                </h5>
            </div>
            <div class="card-body">
                @if(isset($stats['top_cities']) && count($stats['top_cities']) > 0)
                <div class="list-group list-group-flush">
                    @foreach($stats['top_cities'] as $city => $count)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="fw-medium">
                            <i class="fas fa-building me-2 text-muted"></i>
                            {{ $city }}
                        </span>
                        <span class="badge bg-success rounded-pill">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-city" style="font-size: 2.5rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2 mb-0">No city data yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Browsers -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fab fa-chrome me-2"></i>
                    Top Browsers
                </h5>
            </div>
            <div class="card-body">
                @if(isset($stats['browsers']) && count($stats['browsers']) > 0)
                <div class="list-group list-group-flush">
                    @foreach($stats['browsers'] as $browser => $count)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="fw-medium">
                            <i class="fab
                                @if(stripos($browser, 'chrome') !== false) fa-chrome
                                @elseif(stripos($browser, 'firefox') !== false) fa-firefox
                                @elseif(stripos($browser, 'safari') !== false) fa-safari
                                @elseif(stripos($browser, 'edge') !== false) fa-edge
                                @elseif(stripos($browser, 'opera') !== false) fa-opera
                                @else fa-internet-explorer @endif
                                me-2 text-muted"></i>
                            {{ $browser }}
                        </span>
                        <span class="badge bg-info rounded-pill">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-globe" style="font-size: 2.5rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2 mb-0">No browser data yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Location Breakdown Table -->
@if(isset($locationBreakdown) && $locationBreakdown->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-map me-2"></i>
                    Geographic Distribution
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-flag me-1"></i>Country</th>
                                <th><i class="fas fa-city me-1"></i>City</th>
                                <th><i class="fas fa-mouse-pointer me-1"></i>Clicks</th>
                                <th><i class="fas fa-percentage me-1"></i>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locationBreakdown as $location)
                            <tr>
                                <td>
                                    <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                    {{ $location->country }}
                                </td>
                                <td>
                                    <i class="fas fa-building me-2 text-muted"></i>
                                    {{ $location->city ?? 'Unknown City' }}
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $location->count }}</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 16px;">
                                        <div class="progress-bar bg-gradient-primary"
                                            style="width: {{ ($location->count / max($stats['total_clicks'], 1)) * 100 }}%">
                                            {{ round(($location->count / max($stats['total_clicks'], 1)) * 100, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- Recent Click Logs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Recent Click Activities
                </h5>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Latest 50 clicks
                </small>
            </div>
            <div class="card-body">
                @if($logs && $logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-clock me-1"></i>Time</th>
                                <th><i class="fas fa-network-wired me-1"></i>IP Address</th>
                                <th><i class="fas fa-map-marker-alt me-1"></i>Location</th>
                                <th><i class="fas fa-devices me-1"></i>Device</th>
                                <th><i class="fas fa-globe me-1"></i>Browser & OS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>
                                    <div class="fw-bold small">
                                        <i class="fas fa-calendar me-1 text-muted"></i>
                                        {{ $log->clicked_at->format('M d, Y') }}
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $log->clicked_at->format('H:i:s') }}
                                    </small>
                                    <br><span class="badge bg-secondary small">
                                        <i class="fas fa-history me-1"></i>
                                        {{ $log->clicked_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td>
                                    <code class="small">
                                        <i class="fas fa-server me-1"></i>
                                        {{ $log->ip_address }}
                                    </code>
                                </td>
                                <td>
                                    @if($log->country)
                                    <div>
                                        <div class="fw-bold small">
                                            <i class="fas fa-flag me-1"></i>
                                            {{ $log->country_emoji }} {{ $log->city ?? 'Unknown City' }}
                                        </div>
                                        <small class="text-muted">{{ $log->country }}</small>
                                        @if($log->region)
                                        <br><small class="text-muted">
                                            <i class="fas fa-map-pin me-1"></i>
                                            {{ $log->region }}
                                        </small>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-muted small">
                                        <i class="fas fa-question-circle me-1"></i> Unknown Location
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->device_name)
                                    <div class="fw-bold small">
                                        <i class="fas
                                            @if($log->device_type == 'mobile') fa-mobile-alt
                                            @elseif($log->device_type == 'desktop') fa-desktop
                                            @elseif($log->device_type == 'tablet') fa-tablet-alt
                                            @elseif($log->device_type == 'robot') fa-robot
                                            @else fa-question-circle @endif
                                            me-1"></i>
                                        {{ $log->device_name }}
                                    </div>
                                    @if($log->device_brand)
                                    <small class="text-muted">{{ $log->device_brand }}</small>
                                    @endif
                                    @if($log->device_type)
                                    <br><span class="badge
                                        @if($log->device_type == 'mobile') bg-success
                                        @elseif($log->device_type == 'desktop') bg-primary
                                        @elseif($log->device_type == 'tablet') bg-info
                                        @elseif($log->device_type == 'robot') bg-danger
                                        @else bg-secondary @endif
                                        small">{{ ucfirst($log->device_type) }}</span>
                                    @endif
                                    @else
                                    <span class="text-muted small">
                                        <i class="fas fa-question-circle me-1"></i>
                                        Unknown Device
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->browser_name || $log->os_name)
                                    <div>
                                        @if($log->browser_name)
                                        <div class="fw-bold small">
                                            <i class="fab
                                                @if(stripos($log->browser_name, 'chrome') !== false) fa-chrome
                                                @elseif(stripos($log->browser_name, 'firefox') !== false) fa-firefox
                                                @elseif(stripos($log->browser_name, 'safari') !== false) fa-safari
                                                @elseif(stripos($log->browser_name, 'edge') !== false) fa-edge
                                                @elseif(stripos($log->browser_name, 'opera') !== false) fa-opera
                                                @else fa-internet-explorer @endif
                                                me-1"></i>
                                            {{ $log->browser_name }}
                                        </div>
                                        @endif
                                        @if($log->os_name)
                                        <small class="text-muted">
                                            <i class="fab
                                                @if(stripos($log->os_name, 'windows') !== false) fa-windows
                                                @elseif(stripos($log->os_name, 'mac') !== false || stripos($log->os_name, 'ios') !== false) fa-apple
                                                @elseif(stripos($log->os_name, 'android') !== false) fa-android
                                                @elseif(stripos($log->os_name, 'linux') !== false) fa-linux
                                                @else fa-desktop @endif
                                                me-1"></i>
                                            {{ $log->os_name }}
                                        </small>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-muted small">
                                        <i class="fas fa-question-circle me-1"></i>
                                        Unknown
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for logs -->
                @if(method_exists($logs, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $logs->links() }}
                </div>
                @endif

                @else
                <div class="text-center py-4">
                    <i class="fas fa-chart-line" style="font-size: 4rem; color: #6c757d;"></i>
                    <h5 class="mt-3 text-muted">No Click Activity Yet</h5>
                    <p class="text-muted">Click activities will appear here once visitors access the invitation.</p>
                    <div class="mt-3">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6>
                                        <i class="fas fa-lightbulb me-2"></i>
                                        Share your invitation!
                                    </h6>
                                    <p class="mb-0 small">
                                        <i class="fas fa-share-alt me-1"></i>
                                        Share the invitation link to start tracking visitor analytics.
                                        You can copy the link from the contact details page.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Charts JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Daily Clicks Chart
        @if(isset($dailyClicks) && $dailyClicks->count() > 0)
        const dailyCtx = document.getElementById('dailyClicksChart');
        if (dailyCtx) {
            const dailyData = @json($dailyClicks);
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: Object.keys(dailyData),
                    datasets: [{
                        label: 'Daily Clicks',
                        data: Object.values(dailyData),
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(54, 162, 235)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                maxTicksLimit: 10
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverRadius: 6
                        }
                    }
                }
            });
        }
        @endif

        // Device Breakdown Chart
        @if(isset($stats['device_breakdown']) && array_sum($stats['device_breakdown']) > 0)
        const deviceCtx = document.getElementById('deviceChart');
        if (deviceCtx) {
            const deviceData = @json($stats['device_breakdown']);
            const filteredData = Object.entries(deviceData).filter(([key, value]) => value > 0);

            new Chart(deviceCtx, {
                type: 'doughnut',
                data: {
                    labels: filteredData.map(([key, value]) => key.charAt(0).toUpperCase() + key.slice(1)),
                    datasets: [{
                        data: filteredData.map(([key, value]) => value),
                        backgroundColor: [
                            '#28a745', // mobile - green
                            '#007bff', // desktop - blue
                            '#17a2b8', // tablet - cyan
                            '#6c757d'  // others - gray
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.raw / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
        @endif
    });
</script>

<style>
    /* Custom Styling */
    .bg-gradient-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, #28a745, #1e7e34);
    }

    .bg-gradient-info {
        background: linear-gradient(45deg, #17a2b8, #117a8b);
    }

    .bg-gradient-warning {
        background: linear-gradient(45deg, #ffc107, #e0a800);
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.15s ease-in-out;
    }

    .card:hover {
        transform: translateY(-1px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    /* Chart containers - Fixed height issue */
    .card-body>div[style*="position: relative"] {
        width: 100% !important;
        max-height: 300px !important;
    }

    canvas {
        max-height: 250px !important;
        border-radius: 6px;
    }

    /* Table styling */
    .table th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.875rem;
    }

    .table td {
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .table-sm th,
    .table-sm td {
        padding: 0.5rem;
    }

    /* Progress bars */
    .progress {
        border-radius: 8px;
        height: 16px !important;
    }

    .progress-bar {
        border-radius: 8px;
        transition: width 0.6s ease;
    }

    /* Badges */
    .badge {
        font-size: 0.75em;
        font-weight: 500;
    }

    /* List groups */
    .list-group-item {
        border-left: none;
        border-right: none;
        border-top: none;
        border-bottom: 1px solid rgba(0, 0, 0, .125);
        padding: 0.5rem 0;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .list-group-item:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    /* Font Awesome icon styling */
    .fas,
    .fab {
        font-weight: 900;
    }

    /* Icon colors for different contexts */
    .text-primary {
        color: #007bff !important;
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-info {
        color: #17a2b8 !important;
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .text-secondary {
        color: #6c757d !important;
    }

    .text-muted {
        color: #6c757d !important;
    }

    /* Device type specific colors */
    .fa-mobile-alt {
        color: #28a745;
    }

    .fa-desktop {
        color: #007bff;
    }

    .fa-tablet-alt {
        color: #17a2b8;
    }

    .fa-robot {
        color: #dc3545;
    }

    /* Browser specific colors */
    .fa-chrome {
        color: #4285f4;
    }

    .fa-firefox {
        color: #ff7139;
    }

    .fa-safari {
        color: #006cff;
    }

    .fa-edge {
        color: #0078d4;
    }

    .fa-opera {
        color: #ff1b2d;
    }

    .fa-internet-explorer {
        color: #1e5bd3;
    }

    /* OS specific colors */
    .fa-windows {
        color: #0078d4;
    }

    .fa-apple {
        color: #999999;
    }

    .fa-android {
        color: #3ddc84;
    }

    .fa-linux {
        color: #fcc624;
    }

    /* Geography icons */
    .fa-flag {
        color: #dc3545;
    }

    .fa-globe {
        color: #007bff;
    }

    .fa-city {
        color: #6c757d;
    }

    .fa-map-marker-alt {
        color: #28a745;
    }

    .fa-building {
        color: #17a2b8;
    }

    /* Time-based icons */
    .fa-calendar-day {
        color: #17a2b8;
    }

    .fa-calendar-week {
        color: #007bff;
    }

    .fa-calendar-alt {
        color: #28a745;
    }

    .fa-clock {
        color: #ffc107;
    }

    .fa-history {
        color: #6c757d;
    }

    /* Analytics icons */
    .fa-mouse-pointer {
        color: #007bff;
    }

    .fa-users {
        color: #28a745;
    }

    .fa-chart-line {
        color: #17a2b8;
    }

    .fa-chart-bar {
        color: #ffc107;
    }

    /* Empty state styling */
    .text-center i.fas {
        opacity: 0.6;
    }

    /* Card body improvements */
    .card-body {
        padding: 1.25rem;
    }

    .card-body.py-3 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }

    /* Small cards for time-based analytics */
    .card.border-secondary .card-body,
    .card.border-info .card-body,
    .card.border-primary .card-body,
    .card.border-success .card-body,
    .card.border-warning .card-body {
        min-height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Hover effects */
    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }

    /* Animation for cards */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: fadeInUp 0.3s ease;
    }

    /* Alert styling enhancement */
    .alert {
        border: none;
        border-radius: 8px;
    }

    .alert-info {
        background-color: rgba(23, 162, 184, 0.1);
        border-left: 4px solid #17a2b8;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .display-4 {
            font-size: 1.8rem;
        }

        .h5 {
            font-size: 1.1rem;
        }

        .table-responsive {
            font-size: 0.8rem;
        }

        .card-body {
            padding: 1rem;
        }

        /* Smaller icons on mobile */
        .card-body .fs-1 {
            font-size: 1.8rem !important;
        }

        .card-body i[style*="font-size: 1.2rem"] {
            font-size: 1rem !important;
        }

        /* Adjust chart heights on mobile */
        canvas {
            max-height: 200px !important;
        }

        .card-body>div[style*="position: relative"] {
            max-height: 220px !important;
        }
    }

    @media (max-width: 576px) {
        .display-4 {
            font-size: 1.5rem;
        }

        .table-sm {
            font-size: 0.75rem;
        }

        .badge {
            font-size: 0.7em;
        }
    }
</style>
@endsection