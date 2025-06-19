@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Analytics Detail - {{ $contact->name }}</h1>
    <div>
        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-info">
            <i class="bi bi-person"></i> View Contact
        </a>
        <a href="{{ route('analytics.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Analytics
        </a>
    </div>
</div>

<!-- Contact Information Card -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">📞 Contact Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Name:</strong><br>
                {{ $contact->name }}
            </div>
            <div class="col-md-3">
                <strong>Username:</strong><br>
                {{ $contact->username ?? '-' }}
            </div>
            <div class="col-md-3">
                <strong>Phone:</strong><br>
                +{{ $contact->country_code }} {{ $contact->phone_number }}
            </div>
            <div class="col-md-3">
                <strong>Status:</strong><br>
                @if($contact->invitation_status == 'belum_dikirim')
                <span class="badge bg-warning">Belum Dikirim</span>
                @elseif($contact->invitation_status == 'terkirim')
                <span class="badge bg-success">Terkirim</span>
                @else
                <span class="badge bg-danger">Gagal</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h2>{{ $stats['total_clicks'] ?? 0 }}</h2>
                <p class="mb-0">Total Clicks</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h2>{{ $stats['unique_ips'] ?? 0 }}</h2>
                <p class="mb-0">Unique Visitors</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h2>{{ $stats['countries'] ?? 0 }}</h2>
                <p class="mb-0">Countries</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h2>{{ $stats['cities'] ?? 0 }}</h2>
                <p class="mb-0">Cities</p>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-dark text-white">
            <div class="card-body text-center">
                <h4>{{ $stats['continents'] ?? 0 }}</h4>
                <p class="mb-0 small">Continents</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <h4>{{ $stats['zip_codes'] ?? 0 }}</h4>
                <p class="mb-0 small">Zip Codes</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h4>{{ $stats['today_clicks'] ?? 0 }}</h4>
                <p class="mb-0 small">Today</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h4>{{ $stats['this_week_clicks'] ?? 0 }}</h4>
                <p class="mb-0 small">This Week</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h4>{{ $stats['this_month_clicks'] ?? 0 }}</h4>
                <p class="mb-0 small">This Month</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h4>{{ $stats['mobile_percentage'] ?? 0 }}%</h4>
                <p class="mb-0 small">Mobile</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daily Clicks Chart -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">📈 Daily Clicks (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                @if(isset($dailyClicks) && $dailyClicks->count() > 0)
                <canvas id="dailyClicksChart" width="400" height="200"></canvas>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">No daily data available</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Device Breakdown -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">📱 Device Types</h5>
            </div>
            <div class="card-body">
                @if(isset($stats['device_breakdown']) && array_sum($stats['device_breakdown']) > 0)
                <canvas id="deviceChart" width="300" height="200"></canvas>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">No device data available</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Geographic Distribution -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🌍 Geographic Distribution</h5>
            </div>
            <div class="card-body">
                @if(isset($locationBreakdown) && $locationBreakdown->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
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
                                <td>{{ $location->country }}</td>
                                <td>{{ $location->city }}</td>
                                <td>{{ $location->count }}</td>
                                <td>
                                    @php
                                    $percentage = $stats['total_clicks'] > 0 ? round(($location->count /
                                    $stats['total_clicks']) * 100, 1) : 0;
                                    @endphp
                                    <div class="progress" style="height: 15px;">
                                        <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    {{ $percentage }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">No location data available</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Device & Browser Stats -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">💻 Technology Stats</h5>
            </div>
            <div class="card-body">
                @if(isset($stats['top_devices']) && count($stats['top_devices']) > 0)
                <div class="mb-3">
                    <h6>📱 Popular Devices:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($stats['top_devices'] as $device => $count)
                        <span class="badge bg-dark">{{ $device }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($stats['browsers']) && count($stats['browsers']) > 0)
                <div class="mb-3">
                    <h6>🌐 Browsers:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($stats['browsers'] as $browser => $count)
                        <span class="badge bg-warning text-dark">{{ $browser }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($stats['top_countries']) && count($stats['top_countries']) > 0)
                <div class="mb-3">
                    <h6>🌍 Countries:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($stats['top_countries'] as $country => $count)
                        <span class="badge bg-info">{{ $country }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($stats['top_continents']) && count($stats['top_continents']) > 0)
                <div class="mb-3">
                    <h6>🌎 Continents:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($stats['top_continents'] as $continent => $count)
                        <span class="badge bg-primary">{{ $continent }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Time Information -->
@if(isset($stats['first_click']) || isset($stats['last_click']))
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">⏱️ Time Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>First Click:</strong>
                @if(isset($stats['first_click']) && $stats['first_click'])
                {{ \Carbon\Carbon::parse($stats['first_click'])->format('d M Y H:i:s') }}
                <br><small class="text-muted">{{ \Carbon\Carbon::parse($stats['first_click'])->diffForHumans()
                    }}</small>
                @else
                <span class="text-muted">Never</span>
                @endif
            </div>
            <div class="col-md-6">
                <strong>Last Click:</strong>
                @if(isset($stats['last_click']) && $stats['last_click'])
                {{ \Carbon\Carbon::parse($stats['last_click'])->format('d M Y H:i:s') }}
                <br><small class="text-muted">{{ \Carbon\Carbon::parse($stats['last_click'])->diffForHumans() }}</small>
                @else
                <span class="text-muted">Never</span>
                @endif
            </div>
        </div>

        @if(isset($stats['avg_latitude']) && isset($stats['avg_longitude']) && $stats['avg_latitude'] !== 'N/A')
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Average Location:</strong>
                📍 {{ $stats['avg_latitude'] }}, {{ $stats['avg_longitude'] }}
            </div>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Recent Click Logs -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📋 Recent Click Activities</h5>
    </div>
    <div class="card-body">
        @if($logs->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>⏰ Time</th>
                        <th>🌐 IP Address</th>
                        <th>📍 Location</th>
                        <th>📱 Device</th>
                        <th>🌐 Browser & OS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>
                            <strong>{{ $log->clicked_at->format('d M Y') }}</strong><br>
                            <small class="text-muted">{{ $log->clicked_at->format('H:i:s') }}</small><br>
                            <small class="badge bg-secondary">{{ $log->clicked_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <code class="small">{{ $log->ip_address }}</code>
                        </td>
                        <td>
                            @if($log->country)
                            <div>
                                <strong>{{ $log->country_emoji }} {{ $log->city ?? 'Unknown City' }}</strong><br>
                                <small class="text-muted">{{ $log->country }}</small>
                                @if($log->region)
                                <br><small class="text-muted">{{ $log->region }}</small>
                                @endif
                                @if($log->continent)
                                <br><small class="text-info">{{ $log->continent }}</small>
                                @endif
                                @if($log->latitude && $log->longitude)
                                <br><small class="text-success">📍 {{ number_format($log->latitude, 4) }}, {{
                                    number_format($log->longitude, 4) }}</small>
                                @endif
                                @if($log->zipcode)
                                <br><small class="text-warning">📮 {{ $log->zipcode }}</small>
                                @endif
                            </div>
                            @else
                            <span class="text-muted">🌍 Unknown Location</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                @if($log->device_name)
                                <strong>{{ $log->device_name }}</strong><br>
                                @endif

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
                                ">{{ ucfirst($log->device_type) }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                @if($log->browser_name)
                                <strong>{{ $log->browser_name }}</strong><br>
                                @endif

                                @if($log->os_name)
                                <small class="text-muted">{{ $log->os_name }}</small>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $logs->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-activity text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">No click activities yet</h5>
            <p class="text-muted">Click activities will appear here when visitors access this contact's invitation.</p>
        </div>
        @endif
    </div>
</div>

<!-- Charts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Clicks Chart
@if(isset($dailyClicks) && $dailyClicks->count() > 0)
const dailyCtx = document.getElementById('dailyClicksChart').getContext('2d');
const dailyData = @json($dailyClicks);
const dailyChart = new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: Object.keys(dailyData),
        datasets: [{
            label: 'Daily Clicks',
            data: Object.values(dailyData),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
@endif

// Device Breakdown Chart
@if(isset($stats['device_breakdown']) && array_sum($stats['device_breakdown']) > 0)
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
const deviceData = @json($stats['device_breakdown']);
const deviceChart = new Chart(deviceCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(deviceData).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
        datasets: [{
            data: Object.values(deviceData),
            backgroundColor: [
                '#28a745', // mobile - green
                '#007bff', // desktop - blue
                '#17a2b8', // tablet - cyan
                '#dc3545', // robot - red
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
@endif
</script>
@endsection