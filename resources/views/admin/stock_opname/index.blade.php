@extends('layouts.admin')
@section('title', 'Manajemen Stock Opname')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4 class="card-title">Riwayat Stock Opname</h4>
        <a href="{{ route('admin.stock_opname.create') }}" class="btn btn-sm btn-success">+ Mulai Sesi Baru</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="opname-table">
            <thead class="table-light">
                <tr>
                    <th>Periode</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Dibuka Oleh</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                    <th>Disetujui Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sessions as $session)
                    <tr>
                        <td>{{ $session->periode_bulan }}</td>
                        <td>{{ $session->tanggal_mulai }}</td>
                        <td>{{ $session->tanggal_selesai }}</td>
                        <td>{{ $session->opener->nama ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $session->status === 'aktif' ? 'success' : 'secondary' }}">
                                {{ ucfirst($session->status) }}
                            </span>
                        </td>
                        <td>{{ $session->catatan }}</td>
                        <td >
                            @if ($session->status === 'aktif')
                                <a href="{{ route('admin.stock_opname.edit', $session->id) }}" class="btn btn-sm btn-warning me-1">Input</a>
                                <a href="{{ route('admin.stock_opname.show', $session->id) }}" class="btn btn-sm btn-info me-1">Lihat</a>
                                <form action="{{ route('admin.stock_opname.end', $session->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $session->id }}">
                                    <button class="btn btn-sm btn-danger me-1 mt-2" onclick="return confirm('Akhiri sesi ini?')">Hapus</button>
                                </form>
                            @else
                                <a href="{{ route('admin.stock_opname.show', $session->id) }}" class="btn btn-sm btn-info">Lihat</a>
                            @endif
                        </td>
                        <td>{{ $session->approver->nama ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#opname-table').DataTable();
});
</script>
@endsection
