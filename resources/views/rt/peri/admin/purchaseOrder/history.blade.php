@extends('layouts.admin')

@section('title', 'Riwayat Stok Opname')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Riwayat Stok Opname</h4>
    </div>
    <div class="card-body">
        <table id="history-table" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Periode</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Dibuka Oleh</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($opnameSessions as $index => $session)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $session->periode_bulan }}</td>
                    <td>{{ \Carbon\Carbon::parse($session->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($session->tanggal_selesai)->format('d M Y') }}</td>
                    <td><span class="badge bg-{{ $session->status == 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($session->status) }}</span></td>
                    <td>{{ $session->opened_by->nama }}</td>
                    <td>{{ $session->catatan }}</td>
                    <td>
                        <a href="{{ route('admin.opname.detail', $session->id) }}" class="btn btn-sm btn-success">Lihat</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#history-table').DataTable({
            responsive: true,
            ordering: true,
            autoWidth: false
        });
    });
</script>
@endsection
