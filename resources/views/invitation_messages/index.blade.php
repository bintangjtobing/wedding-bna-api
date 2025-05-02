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
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Pengirim</th>
                        <th>Pesan</th>
                        <th>Kehadiran</th>
                        <th>Kontak Tujuan</th>
                        <th>Waktu Kirim</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                    <tr>
                        <td>{{ $message->name }}</td>
                        <td>{{ Str::limit($message->message, 100) }}</td>
                        <td>
                            @if($message->attendance == 'hadir')
                            <span class="badge bg-success">Hadir</span>
                            @elseif($message->attendance == 'tidak_hadir')
                            <span class="badge bg-danger">Tidak Hadir</span>
                            @else
                            <span class="badge bg-warning">Belum Pasti</span>
                            @endif
                        </td>
                        <td>{{ $message->contact ? $message->contact->name : 'Tidak ada' }}</td>
                        <td>{{ $message->created_at->format('d M Y H:i') }}</td>
                        <td>
                            @if($message->is_approved)
                            <span class="badge bg-success">Disetujui</span>
                            @else
                            <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <form action="{{ route('invitation_messages.toggle_approval', $message) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_approved"
                                        value="{{ $message->is_approved ? '0' : '1' }}">
                                    <button type="submit"
                                        class="btn btn-sm {{ $message->is_approved ? 'btn-danger' : 'btn-success' }}">
                                        {{ $message->is_approved ? 'Tolak' : 'Setujui' }}
                                    </button>
                                </form>
                                <form action="{{ route('invitation_messages.destroy', $message) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
