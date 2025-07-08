@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Click Analytics Dashboard</h1>
    <div>
        <a href="{{ route('analytics.export') }}" class="btn btn-success">
            <i class="bi bi-download"></i> Export Data
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h3>{{ number_format($analytics['total_clicks']) }}</h3>
                <p class="mb-0">Total Clicks</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h3>{{ number_format($analytics['unique_visitors']) }}</h3>
                <p class="mb-0">Unique Visitors</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h3>{{ $analytics['countries_reached'] }}</h3>
                <p class="mb-0">Countries Reached</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h3>{{ $analytics['cities_reached'] }}</h3>
                <p class="mb-0">Cities Reached</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daily Clicks Chart -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Daily Clicks (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyClicksChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Hourly Pattern -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Hourly Pattern</h5>
            </div>
            <div class="card-body">
                <canvas id="hourlyPatternChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Countries -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Top Countries</h5>
            </div>
            <div class="card-body">
                @if($analytics['top_countries']->count() > 0)
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
                            @foreach($analytics['top_countries'] as $country => $count)
                            <tr>
                                <td>{{ $country }}</td>
                                <td>{{ $count }}</td>
                                <td>
                                    <div class="progress" style="height: 15px;">
                                        <div class="progress-bar bg-info"
                                            style="width: {{ ($count / $analytics['total_clicks']) * 100 }}%"></div>
                                    </div>
                                    {{ round(($count / $analytics['total_clicks']) * 100, 1) }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-muted">No data available</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Device Breakdown -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Device Breakdown</h5>
            </div>
            <div class="card-body">
                @if($analytics['device_breakdown']->count() > 0)
                <canvas id="deviceChart" width="400" height="200"></canvas>
                @else
                <p class="text-center text-muted">No device data available</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Contact Performance -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Contact Performance</h5>
    </div>
    <div class="card-body">
        @if($contactAnalytics->count() > 0)
        <!-- Filter Form -->
        <form method="GET" action="{{ route('analytics.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="name_filter" class="form-label">Filter by Name</label>
                    <input type="text" class="form-control" id="name_filter" name="name_filter" 
                           value="{{ request('name_filter') }}" placeholder="Enter name...">
                </div>
                <div class="col-md-3">
                    <label for="username_filter" class="form-label">Filter by Username</label>
                    <input type="text" class="form-control" id="username_filter" name="username_filter" 
                           value="{{ request('username_filter') }}" placeholder="Enter username...">
                </div>
                <div class="col-md-2">
                    <label for="min_clicks" class="form-label">Min Clicks</label>
                    <input type="number" class="form-control" id="min_clicks" name="min_clicks" 
                           value="{{ request('min_clicks') }}" placeholder="0">
                </div>
                <div class="col-md-2">
                    <label for="max_clicks" class="form-label">Max Clicks</label>
                    <input type="number" class="form-control" id="max_clicks" name="max_clicks" 
                           value="{{ request('max_clicks') }}" placeholder="999">
                </div>
                <div class="col-md-2">
                    <label for="min_visitors" class="form-label">Min Visitors</label>
                    <input type="number" class="form-control" id="min_visitors" name="min_visitors" 
                           value="{{ request('min_visitors') }}" placeholder="0">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <label for="max_visitors" class="form-label">Max Visitors</label>
                    <input type="number" class="form-control" id="max_visitors" name="max_visitors" 
                           value="{{ request('max_visitors') }}" placeholder="999">
                </div>
                <div class="col-md-2">
                    <label for="sort_by" class="form-label">Sort by</label>
                    <select class="form-control" id="sort_by" name="sort_by">
                        <option value="last_click" {{ request('sort_by', 'last_click') == 'last_click' ? 'selected' : '' }}>Last Click</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="username" {{ request('sort_by') == 'username' ? 'selected' : '' }}>Username</option>
                        <option value="clicks" {{ request('sort_by') == 'clicks' ? 'selected' : '' }}>Total Clicks</option>
                        <option value="unique_visitors" {{ request('sort_by') == 'unique_visitors' ? 'selected' : '' }}>Unique Visitors</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort_order" class="form-label">Order</label>
                    <select class="form-control" id="sort_order" name="sort_order">
                        <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ route('analytics.index') }}" class="btn btn-secondary">Clear Filters</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('analytics.index', array_merge(request()->all(), ['sort_by' => 'name', 'sort_order' => request('sort_by') == 'name' && request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-decoration-none">
                                Contact
                                @if(request('sort_by') == 'name')
                                    <i class="fas fa-sort-{{ request('sort_order', 'desc') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('analytics.index', array_merge(request()->all(), ['sort_by' => 'username', 'sort_order' => request('sort_by') == 'username' && request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-decoration-none">
                                Username
                                @if(request('sort_by') == 'username')
                                    <i class="fas fa-sort-{{ request('sort_order', 'desc') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('analytics.index', array_merge(request()->all(), ['sort_by' => 'clicks', 'sort_order' => request('sort_by') == 'clicks' && request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-decoration-none">
                                Total Clicks
                                @if(request('sort_by') == 'clicks')
                                    <i class="fas fa-sort-{{ request('sort_order', 'desc') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('analytics.index', array_merge(request()->all(), ['sort_by' => 'unique_visitors', 'sort_order' => request('sort_by') == 'unique_visitors' && request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-decoration-none">
                                Unique Visitors
                                @if(request('sort_by') == 'unique_visitors')
                                    <i class="fas fa-sort-{{ request('sort_order', 'desc') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('analytics.index', array_merge(request()->all(), ['sort_by' => 'last_click', 'sort_order' => request('sort_by', 'last_click') == 'last_click' && request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-decoration-none">
                                Last Click
                                @if(request('sort_by', 'last_click') == 'last_click')
                                    <i class="fas fa-sort-{{ request('sort_order', 'desc') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contactAnalytics as $data)
                    <tr>
                        <td>{{ $data['contact']->name }}</td>
                        <td>{{ $data['contact']->username }}</td>
                        <td><span class="badge bg-primary">{{ $data['clicks'] }}</span></td>
                        <td><span class="badge bg-success">{{ $data['unique_visitors'] }}</span></td>
                        <td>
                            @if($data['last_click'])
                            {{ \Carbon\Carbon::parse($data['last_click'])->diffForHumans() }}
                            @else
                            <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('analytics.contact', $data['contact']) }}"
                                class="btn btn-sm btn-outline-primary">View Details</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-center text-muted">No contact data available</p>
        @endif
    </div>
</div>

<!-- Recent Activities -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Recent Activities</h5>
    </div>
    <div class="card-body">
        @if($analytics['recent_activities']->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Device</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analytics['recent_activities'] as $activity)
                    <tr>
                        <td>{{ $activity->clicked_at->diffForHumans() }}</td>
                        <td>
                            <strong>{{ $activity->name }}</strong><br>
                            <small class="text-muted">{{ $activity->username }}</small>
                        </td>
                        <td>
                            @if($activity->country)
                            {{ $activity->country_emoji }} {{ $activity->city }}, {{ $activity->country }}
                            @else
                            <span class="text-muted">Unknown</span>
                            @endif
                        </td>
                        <td>
                            @if($activity->device_name)
                            {{ $activity->device_name }}<br>
                            <small class="text-muted">{{ $activity->os_name }} • {{ $activity->browser_name }}</small>
                            @else
                            <span class="text-muted">Unknown</span>
                            @endif
                        </td>
                        <td><code>{{ $activity->ip_address }}</code></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-center text-muted">No recent activities</p>
        @endif
    </div>
</div>

<!-- Charts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Clicks Chart
const dailyCtx = document.getElementById('dailyClicksChart').getContext('2d');
const dailyData = @json($analytics['daily_clicks']);
const dailyChart = new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: Object.keys(dailyData),
        datasets: [{
            label: 'Daily Clicks',
            data: Object.values(dailyData),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Hourly Pattern Chart
const hourlyCtx = document.getElementById('hourlyPatternChart').getContext('2d');
const hourlyData = @json($analytics['hourly_pattern']);
const hourlyChart = new Chart(hourlyCtx, {
    type: 'bar',
    data: {
        labels: Object.keys(hourlyData).map(h => h + ':00'),
        datasets: [{
            label: 'Clicks by Hour',
            data: Object.values(hourlyData),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Device Breakdown Chart
@if($analytics['device_breakdown']->count() > 0)
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
const deviceData = @json($analytics['device_breakdown']);
const deviceChart = new Chart(deviceCtx, {
    type: 'pie',
    data: {
        labels: Object.keys(deviceData),
        datasets: [{
            data: Object.values(deviceData),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
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