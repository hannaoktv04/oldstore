@extends('layouts.admin')

@section('title', 'Manajemen Stock Opname')

@section('content')
    <div>
        <div class="container-fluid py-4 d-flex justify-content-between">
            <h4>Riwayat Stock Opname</h4>
            <a href="{{ route('admin.stock_opname.create') }}" class="btn btn-sm btn-primary">+ Mulai Sesi Baru</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="table table-bordered table-striped" id="opnameTable" style="width:100%">
                <thead class="text-center">
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
                        <tr class="text-center">
                            <td>{{ $session->periode_bulan }}</td>
                            <td>{{ $session->tanggal_mulai->format('Y-m-d') }}</td>
                            <td>{{ $session->tanggal_selesai ? $session->tanggal_selesai->format('Y-m-d') : '-' }}</td>
                            <td>{{ $session->opener->nama ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $session->status === 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </td>
                            <td class="text-wrap text-start">{{ $session->catatan }}</td>
                            <td>
                                @switch($session->status)
                                    @case('aktif')
                                        <a href="{{ route('admin.stock_opname.edit', $session) }}"
                                            class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.stock_opname.endSession', $session) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <input type="hidden" name="tanggal_selesai" value="{{ now()->toDateString() }}">
                                            <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Tutup Sesi">
                                                <i class="bi bi-check2"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.stock_opname.destroy', $session) }}" method="POST"
                                            class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger me-1"
                                                onclick="return confirm('Akhiri sesi ini?')" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @break
                                    @case('menunggu')
                                        <a href="{{ route('admin.stock_opname.edit', $session) }}"
                                            class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.stock_opname.destroy', $session) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger me-1"
                                                onclick="return confirm('Hapus sesi ini?')" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @break
                                    @default
                                        <a href="{{ route('admin.stock_opname.show', $session) }}" class="btn btn-sm btn-outline-info me-1"
                                            title="Lihat">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                @endswitch
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
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
