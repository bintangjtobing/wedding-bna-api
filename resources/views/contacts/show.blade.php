<!-- resources/views/contacts/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Detail Kontak</h1>
    <div>
        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-warning">Edit Kontak</a>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informasi Kontak</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nama</div>
                    <div class="col-md-8">{{ $contact->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Username</div>
                    <div class="col-md-8">{{ $contact->username ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nomor Telepon</div>
                    <div class="col-md-8">+{{ $contact->country_code ?? '' }} {{ $contact->phone_number }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Negara</div>
                    <div class="col-md-8">
                        {{ $contact->country ?? '-' }}
                        @if($contact->country == 'ID')
                        (Indonesia)
                        @elseif($contact->country == 'MY')
                        (Malaysia)
                        @elseif($contact->country == 'SG')
                        (Singapura)
                        @elseif($contact->country == 'US')
                        (Amerika Serikat)
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Panggilan</div>
                    <div class="col-md-8">{{ $contact->greeting ?: '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Status Undangan</div>
                    <div class="col-md-8">
                        @if($contact->invitation_status == 'belum_dikirim')
                        <span class="badge bg-warning">Belum Dikirim</span>
                        @elseif($contact->invitation_status == 'terkirim')
                        <span class="badge bg-success">Terkirim</span>
                        @else
                        <span class="badge bg-danger">Gagal</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Waktu Pengiriman</div>
                    <div class="col-md-8">
                        @if($contact->sent_at)
                        {{ \Carbon\Carbon::parse($contact->sent_at)->format('d M Y H:i:s') }}
                        @else
                        -
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dibuat pada</div>
                    <div class="col-md-8">{{ $contact->created_at->format('d M Y H:i:s') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Diperbarui pada</div>
                    <div class="col-md-8">{{ $contact->updated_at->format('d M Y H:i:s') }}</div>
                </div>
            </div>
        </div>

        <!-- Enhanced Click Analytics Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📊 Click Analytics</h5>
                <small class="text-muted">Real-time visitor insights</small>
            </div>
            <div class="card-body">
                @if($clickStats['total_clicks'] > 0)
                <!-- Primary Stats Row -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                            <h3 class="text-primary mb-1">{{ $clickStats['total_clicks'] }}</h3>
                            <p class="mb-0 small">Total Clicks</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                            <h3 class="text-success mb-1">{{ $clickStats['unique_ips'] }}</h3>
                            <p class="mb-0 small">Unique Visitors</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                            <h3 class="text-info mb-1">{{ $clickStats['countries'] }}</h3>
                            <p class="mb-0 small">Countries</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                            <h3 class="text-warning mb-1">{{ $clickStats['cities'] }}</h3>
                            <p class="mb-0 small">Cities</p>
                        </div>
                    </div>
                </div>

                <!-- Time-based Stats -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="text-center p-2 border rounded">
                            <h4 class="text-secondary mb-1">{{ $clickStats['today_clicks'] ?? 0 }}</h4>
                            <p class="mb-0 small">Today</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-2 border rounded">
                            <h4 class="text-secondary mb-1">{{ $clickStats['this_week_clicks'] ?? 0 }}</h4>
                            <p class="mb-0 small">This Week</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-2 border rounded">
                            <h4 class="text-secondary mb-1">{{ $clickStats['this_month_clicks'] ?? 0 }}</h4>
                            <p class="mb-0 small">This Month</p>
                        </div>
                    </div>
                </div>

                <!-- Device Analytics -->
                @if(isset($clickStats['device_breakdown']))
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="mb-3">📱 Device Analytics</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong>{{ $clickStats['device_breakdown']['mobile'] ?? 0 }}</strong>
                                    <br><small class="text-muted">Mobile</small>
                                    <br><small class="badge bg-primary">{{ $clickStats['mobile_percentage'] ?? 0
                                        }}%</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong>{{ $clickStats['device_breakdown']['desktop'] ?? 0 }}</strong>
                                    <br><small class="text-muted">Desktop</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong>{{ $clickStats['device_breakdown']['tablet'] ?? 0 }}</strong>
                                    <br><small class="text-muted">Tablet</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <strong>{{ $clickStats['device_breakdown']['robot'] ?? 0 }}</strong>
                                    <br><small class="text-muted">Bots</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Time Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>First Click:</strong>
                        @if($clickStats['first_click'])
                        {{ \Carbon\Carbon::parse($clickStats['first_click'])->format('d M Y H:i:s') }}
                        @else
                        -
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Last Click:</strong>
                        @if($clickStats['last_click'])
                        {{ \Carbon\Carbon::parse($clickStats['last_click'])->format('d M Y H:i:s') }}
                        <small class="text-muted">({{ \Carbon\Carbon::parse($clickStats['last_click'])->diffForHumans()
                            }})</small>
                        @else
                        -
                        @endif
                    </div>
                </div>

                <!-- Top Countries -->
                @if(isset($clickStats['top_countries']) && count($clickStats['top_countries']) > 0)
                <div class="mb-3">
                    <h6>🌍 Top Countries:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($clickStats['top_countries'] as $country => $count)
                        <span class="badge bg-secondary">{{ $country }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Top Cities -->
                @if(isset($clickStats['top_cities']) && count($clickStats['top_cities']) > 0)
                <div class="mb-3">
                    <h6>🏙️ Top Cities:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($clickStats['top_cities'] as $city => $count)
                        <span class="badge bg-info">{{ $city }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Top Devices -->
                @if(isset($clickStats['top_devices']) && count($clickStats['top_devices']) > 0)
                <div class="mb-3">
                    <h6>📱 Popular Devices:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($clickStats['top_devices'] as $device => $count)
                        <span class="badge bg-dark">{{ $device }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Top Browsers -->
                @if(isset($clickStats['browsers']) && count($clickStats['browsers']) > 0)
                <div class="mb-3">
                    <h6>🌐 Browsers:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($clickStats['browsers'] as $browser => $count)
                        <span class="badge bg-warning text-dark">{{ $browser }}: {{ $count }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @else
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-graph-up" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Belum ada aktivitas click</h5>
                        <p>Kontak ini belum pernah diklik oleh pengunjung.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Kontak
                    </a>

                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus kontak ini?')">
                            <i class="bi bi-trash"></i> Hapus Kontak
                        </button>
                    </form>

                    <form action="{{ route('contacts.resetStatus', $contact) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-warning w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Status Undangan
                        </button>
                    </form>

                    <a href="{{ route('messages.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-envelope"></i> Kirim Pesan
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Kontak dari {{ $contact->admin->role == 'groom' ? 'Mempelai Pria' : 'Mempelai Wanita'
                    }}</h5>
            </div>
            <div class="card-body">
                <p>Nama Admin: <strong>{{ $contact->admin->name }}</strong></p>
                <p>Email Admin: <strong>{{ $contact->admin->email }}</strong></p>
                <p>WhatsApp: <strong>{{ $contact->admin->whatsapp_number ?? '-' }}</strong></p>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Click Logs Detail -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📋 Riwayat Click Activities</h5>
        <small class="text-muted">Recent 20 activities</small>
    </div>
    <div class="card-body">
        @php
        $clickLogs = $contact->clickLogs()->orderBy('clicked_at', 'desc')->limit(20)->get();
        @endphp

        @if($clickLogs->isEmpty())
        <div class="text-center py-4">
            <i class="bi bi-activity text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">Belum ada riwayat click</h5>
            <p class="text-muted">Aktivitas click akan muncul di sini setelah kontak dikunjungi.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>⏰ Waktu</th>
                        <th>🌐 IP Address</th>
                        <th>📍 Location</th>
                        <th>📱 Device</th>
                        <th>🌐 Browser & OS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clickLogs as $log)
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

        @if($contact->clickLogs()->count() > 20)
        <div class="text-center mt-3">
            <small class="text-muted">
                Menampilkan 20 aktivitas terbaru dari <strong>{{ $contact->clickLogs()->count() }}</strong> total click
            </small>
        </div>
        @endif
        @endif
    </div>
</div>

<!-- Message Logs Section (unchanged) -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">📨 Riwayat Pengiriman</h5>
    </div>
    <div class="card-body">
        @if($contact->messageLogs->isEmpty())
        <p class="text-center">Belum ada riwayat pengiriman untuk kontak ini.</p>
        @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pesan</th>
                        <th>Status</th>
                        <th>Dikirim oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contact->messageLogs()->orderBy('created_at', 'desc')->get() as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td>{{ Str::limit($log->message->content, 100) }}</td>
                        <td>
                            @if($log->status == 'sent')
                            <span class="badge bg-success">Terkirim</span>
                            @elseif($log->status == 'pending')
                            <span class="badge bg-warning">Menunggu</span>
                            @else
                            <span class="badge bg-danger">Gagal</span>
                            @endif
                        </td>
                        <td>{{ $log->admin->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection