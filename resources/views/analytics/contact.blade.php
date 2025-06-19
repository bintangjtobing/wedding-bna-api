<!-- Perbaikan untuk halaman analytics detail kontak -->
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Analytics Detail - {{ $contact->name }}</h1>
    <div>
        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-info">View Contact</a>
        <a href="{{ route('analytics.index') }}" class="btn btn-secondary">Back to Analytics</a>
    </div>
</div>

<!-- Contact Information Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i>
                    Contact Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Name:</strong><br>
                        <span class="fs-5">{{ $contact->name }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Username:</strong><br>
                        <code class="fs-6">{{ $contact->username }}</code>
                    </div>
                    <div class="col-md-3">
                        <strong>Phone:</strong><br>
                        <span class="font-monospace">+{{ $contact->country_code }} {{ $contact->phone_number }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong><br>
                        @if($contact->invitation_status == 'belum_dikirim')
                        <span class="badge bg-warning fs-6">Belum Dikirim</span>
                        @elseif($contact->invitation_status == 'terkirim')
                        <span class="badge bg-success fs-6">Terkirim</span>
                        @else
                        <span class="badge bg-danger fs-6">Gagal</span>
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
                        <i class="bi bi-mouse-fill"></i>
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
                        <i class="bi bi-people-fill"></i>
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
                        <i class="bi bi-globe"></i>
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
                        <i class="bi bi-geo-alt-fill"></i>
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
            <div class="card-body">
                <div class="display-6 fw-bold text-dark">{{ $stats['continents'] ?? 0 }}</div>
                <div class="text-muted small">Continents</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-secondary h-100">
            <div class="card-body">
                <div class="display-6 fw-bold text-dark">{{ $stats['zip_codes'] ?? 0 }}</div>
                <div class="text-muted small">Zip Codes</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-info h-100">
            <div class="card-body">
                <div class="display-6 fw-bold text-info">{{ $stats['today_clicks'] ?? 0 }}</div>
                <div class="text-muted small">Today</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-primary h-100">
            <div class="card-body">
                <div class="display-6 fw-bold text-primary">{{ $stats['this_week_clicks'] ?? 0 }}</div>
                <div class="text-muted small">This Week</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-success h-100">
            <div class="card-body">
                <div class="display-6 fw-bold text-success">{{ $stats['this_month_clicks'] ?? 0 }}</div>
                <div class="text-muted small">This Month</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card text-center border-warning h-100">
            <div class="card-body">
                <div class="display-6 fw-bold text-warning">
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
                    <i class="bi bi-bar-chart me-2"></i>
                    Daily Clicks (Last 30 Days)
                </h5>
            </div>
            <div class="card-body">
                @if(isset($dailyClicks) && $dailyClicks->count() > 0)
                <canvas id="dailyClicksChart" height="300"></canvas>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-graph-up" style="font-size: 4rem; color: #6c757d;"></i>
                    <h5 class="mt-3 text-muted">No Daily Data Available</h5>
                    <p class="text-muted">Daily click data will appear here once visitors start clicking.</p>
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
                    <i class="bi bi-phone me-2"></i>
                    Device Types
                </h5>
            </div>
            <div class="card-body">
                @if(isset($stats['device_breakdown']) && array_sum($stats['device_breakdown']) > 0)
                <canvas id="deviceChart" height="300"></canvas>
                <div class="row mt-3">
                    @foreach($stats['device_breakdown'] as $device => $count)
                    @if($count > 0)
                    <div class="col-6 mb-2">
                        <div class="d-flex align-items-center">
                            <div class="badge
                                @if($device == 'mobile') bg-success
                                @elseif($device == 'desktop') bg-primary
                                @elseif($device == 'tablet') bg-info
                                @else bg-secondary @endif
                                me-2" style="width: 12px; height: 12px;"></div>
                            <small class="text-muted">{{ ucfirst($device) }}: {{ $count }}</small>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-devices" style="font-size: 4rem; color: #6c757d;"></i>
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
                    <i class="bi bi-flag me-2"></i>
                    Top Countries
                </h5>
            </div>
            <div class="card-body">
                @if(isset($stats['top_countries']) && count($stats['top_countries']) > 0)
                <div class="list-group list-group-flush">
                    @foreach($stats['top_countries'] as $country => $count)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="fw-medium">{{ $country }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-globe2" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2">No geographic data yet</p>
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
                    <i class="bi bi-building me-2"></i>
                    Top Cities
                </h5>
            </div>
            <div class="card-body">
                @if(isset($stats['top_cities']) && count($stats['top_cities']) > 0)
                <div class="list-group list-group-flush">
                    @foreach($stats['top_cities'] as $city => $count)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="fw-medium">{{ $city }}</span>
                        <span class="badge bg-success rounded-pill">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-geo-alt" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2">No city data yet</p>
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
                    <i class="bi bi-browser-chrome me-2"></i>
                    Top Browsers
                </h5>
            </div>
            <div class="card-body">
                @if(isset($stats['browsers']) && count($stats['browsers']) > 0)
                <div class="list-group list-group-flush">
                    @foreach($stats['browsers'] as $browser => $count)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="fw-medium">{{ $browser }}</span>
                        <span class="badge bg-info rounded-pill">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-browser-safari" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2">No browser data yet</p>
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
                    <i class="bi bi-map me-2"></i>
                    Geographic Distribution
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Country</th>
                                <th>City</th>
                                <th>Clicks</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locationBreakdown as $location)
                            <tr>
                                <td>
                                    <i class="bi bi-flag me-2"></i>
                                    {{ $location->country }}
                                </td>
                                <td>
                                    <i class="bi bi-geo-alt me-2"></i>
                                    {{ $location->city ?? 'Unknown City' }}
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $location->count }}</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
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
                    <i class="bi bi-activity me-2"></i>
                    Recent Click Activities
                </h5>
                <small class="text-muted">Latest 50 clicks</small>
            </div>
            <div class="card-body">
                @if($logs && $logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Time</th>
                                <th>IP Address</th>
                                <th>Location</th>
                                <th>Device</th>
                                <th>Browser & OS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $log->clicked_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $log->clicked_at->format('H:i:s') }}</small>
                                    <br><span class="badge bg-secondary small">{{ $log->clicked_at->diffForHumans()
                                        }}</span>
                                </td>
                                <td>
                                    <code class="small">{{ $log->ip_address }}</code>
                                </td>
                                <td>
                                    @if($log->country)
                                    <div>
                                        <div class="fw-bold">
                                            {{ $log->country_emoji }} {{ $log->city ?? 'Unknown City' }}
                                        </div>
                                        <small class="text-muted">{{ $log->country }}</small>
                                        @if($log->region)
                                        <br><small class="text-muted">{{ $log->region }}</small>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-muted">
                                        <i class="bi bi-geo-alt"></i> Unknown Location
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->device_name)
                                    <div class="fw-bold">{{ $log->device_name }}</div>
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
                                    <span class="text-muted">Unknown Device</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->browser_name || $log->os_name)
                                    <div>
                                        @if($log->browser_name)
                                        <div class="fw-bold">
                                            <i class="bi bi-browser-chrome me-1"></i>
                                            {{ $log->browser_name }}
                                        </div>
                                        @endif
                                        @if($log->os_name)
                                        <small class="text-muted">
                                            <i class="bi bi-cpu me-1"></i>
                                            {{ $log->os_name }}
                                        </small>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-muted">Unknown</span>
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
                <div class="text-center py-5">
                    <i class="bi bi-activity" style="font-size: 5rem; color: #6c757d;"></i>
                    <h4 class="mt-3 text-muted">No Click Activity Yet</h4>
                    <p class="text-muted">Click activities will appear here once visitors access the invitation.</p>
                    <div class="mt-4">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6><i class="bi bi-lightbulb me-2"></i>Share your invitation!</h6>
                                    <p class="mb-0 small">
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
                    pointRadius: 5
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
                        hoverRadius: 8
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
                    borderWidth: 3,
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
                cutout: '60%'
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
        box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 2rem 0 rgba(33, 40, 50, 0.2);
    }

    .table th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .progress {
        border-radius: 10px;
    }

    .progress-bar {
        border-radius: 10px;
    }

    .badge {
        font-size: 0.75em;
    }

    .list-group-item {
        border-left: none;
        border-right: none;
        border-top: none;
        border-bottom: 1px solid rgba(0, 0, 0, .125);
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }

        .display-6 {
            font-size: 1.5rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
@endsection