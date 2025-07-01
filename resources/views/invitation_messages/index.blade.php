@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Daftar Ucapan dan Doa</h1>
</div>

<div class="card">
    <div class="card-body">
        @if($messages->isEmpty())
        <p class="text-center">Belum ada ucapan yang dikirimkan.</p>
        @else
        <div class="row">
            @foreach($messages as $message)
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 text-truncate">{{ $message->name }}</h6>
                        <div class="d-flex gap-1">
                            @if($message->attendance == 'hadir')
                            <span class="badge bg-success">Hadir</span>
                            @elseif($message->attendance == 'tidak_hadir')
                            <span class="badge bg-danger">Tidak Hadir</span>
                            @else
                            <span class="badge bg-warning">Belum Pasti</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text mb-3">{{ Str::limit($message->message, 350) }}</p>

                        <div class="row text-muted small mb-3">
                            <div class="col-12 mb-2">
                                <i class="fas fa-user me-1"></i>
                                <strong>Kontak:</strong> {{ $message->contact ? $message->contact->name : 'Tidak ada' }}
                            </div>
                            <div class="col-12 mb-2">
                                <i class="fas fa-clock me-1"></i>
                                <strong>Waktu:</strong> {{ $message->created_at->format('d M Y H:i') }}
                            </div>
                            <div class="col-12">
                                <i class="fas fa-check-circle me-1"></i>
                                <strong>Status:</strong>
                                @if($message->is_approved)
                                <span class="badge bg-success">Disetujui</span>
                                @else
                                <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2"
                            onclick="viewMessage({{ $message->id }}, '{{ addslashes($message->name) }}', '{{ addslashes($message->message) }}', '{{ $message->attendance }}', '{{ $message->contact ? addslashes($message->contact->name) : 'Tidak ada' }}', '{{ $message->created_at->format('d M Y H:i') }}')">
                            <i class="fas fa-eye me-1"></i> Lihat Lengkap
                        </button>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <form action="{{ route('invitation_messages.toggle_approval', $message) }}" method="POST"
                                class="flex-fill">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_approved" value="{{ $message->is_approved ? '0' : '1' }}">
                                <button type="submit"
                                    class="btn btn-sm {{ $message->is_approved ? 'btn-outline-danger' : 'btn-outline-success' }} w-100">
                                    <i class="fas {{ $message->is_approved ? 'fa-times' : 'fa-check' }} me-1"></i>
                                    {{ $message->is_approved ? 'Tolak' : 'Setujui' }}
                                </button>
                            </form>
                            <form action="{{ route('invitation_messages.destroy', $message) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')"
                                    title="Hapus pesan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $messages->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal untuk melihat pesan lengkap -->
<div class="modal fade" id="viewMessageModal" tabindex="-1" aria-labelledby="viewMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMessageModalLabel">Pesan Lengkap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="modalSender"></h6>
                <p id="modalMessage"></p>
                <p id="modalAttendance"></p>
                <p id="modalContact"></p>
                <p id="modalDate"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Script untuk menampilkan pesan lengkap di modal
    function viewMessage(id, name, message, attendance, contactName, date) {
        document.getElementById('modalSender').textContent = name;
        document.getElementById('modalMessage').textContent = message;
        document.getElementById('modalAttendance').textContent = 'Kehadiran: ' + attendance;
        document.getElementById('modalContact').textContent = 'Untuk: ' + contactName;
        document.getElementById('modalDate').textContent = 'Dikirim pada: ' + date;

        // Tampilkan modal
        var modal = new bootstrap.Modal(document.getElementById('viewMessageModal'));
        modal.show();
    }
</script>
@endsection