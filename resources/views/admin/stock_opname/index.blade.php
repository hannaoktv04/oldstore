@extends('layouts.admin')

@section('title', 'Manajemen Stock Opname')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between mb-3">
        <h4 class="card-title">Riwayat Stock Opname</h4>
        <a href="{{ route('admin.stock_opname.create') }}" class="btn btn-sm btn-primary">+ Mulai Sesi Baru</a>
    </div>
    <div class="card-body">
        <div style="overflow-x: auto;">
            <table class="table table-bordered table-striped nowrap" id="opnameTable" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>Periode</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Admin</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $session)
                    <tr>
                        <td>{{ $session->periode_bulan }}</td>
                        <td>{{ $session->tanggal_mulai->format('Y-m-d') }}</td>
                        <td>{{ $session->tanggal_selesai ? $session->tanggal_selesai->format('Y-m-d') : '-' }}</td>
                        <td>{{ $session->opener->nama ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $session->status === 'aktif' ? 'success' : 'secondary' }}">
                                {{ ucfirst($session->status) }}
                            </span>
                        </td>
                        <td>{{ $session->catatan }}</td>
                        <td>
                            @if ($session->status === 'aktif')
                            <a href="{{ route('admin.stock_opname.edit', $session) }}"
                                class="btn btn-sm btn-warning me-1">Edit</a>
                            <form action="{{ route('admin.stock_opname.endSession', $session) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <input type="hidden" name="tanggal_selesai" value="{{ now()->toDateString() }}">
                                <button type="submit" class="btn btn-sm btn-dark me-1">Close</button>
                            </form>
                            <form action="{{ route('admin.stock_opname.destroy', $session) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger me-1"
                                    onclick="return confirm('Akhiri sesi ini?')">Hapus</button>
                            </form>
                            @elseif ($session->status === 'menunggu')
                            <a href="{{ route('admin.stock_opname.edit', $session) }}"
                                class="btn btn-sm btn-warning me-1">Edit</a>
                            <form action="{{ route('admin.stock_opname.destroy', $session) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger me-1"
                                    onclick="return confirm('Hapus sesi ini?')">Hapus</button>
                            </form>
                            @else
                            <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-sm btn-info">Lihat</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
            $('#opnameTable').DataTable({
                scrollX: true,
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data yang tersedia",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        previous: "<",
                        next: ">",
                    }
                },
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }],
                order: [
                    [0, 'desc']
                ]
            });
        });
</script>
@endpush
