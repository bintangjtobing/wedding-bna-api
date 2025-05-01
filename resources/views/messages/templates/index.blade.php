@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Template Pesan</h1>
    <a href="{{ route('templates.create') }}" class="btn btn-primary">Tambah Template Baru</a>
</div>

<div class="card">
    <div class="card-body">
        @if($templates->isEmpty())
        <p class="text-center">Belum ada template pesan yang ditambahkan.</p>
        @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Template</th>
                        <th>Isi Pesan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr>
                        <td>{{ $template->template_name }}</td>
                        <td>{{ Str::limit($template->content, 100) }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('templates.use', $template) }}" class="btn btn-sm btn-success">Gunakan</a>
                                <a href="{{ route('templates.edit', $template) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('templates.destroy', $template) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus template ini?')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
