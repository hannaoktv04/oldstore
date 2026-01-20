@extends('peri::layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark mb-0">Daftar Transaksi: {{ ucfirst($status) }}</h4>
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">ID / TRANSAKSI</th>
                                    <th class="py-3 text-muted small fw-bold">USER / TANGGAL</th>
                                    <th class="py-3 text-muted small fw-bold">DETAIL BARANG</th>
                                    <th class="py-3 text-muted small fw-bold text-center">STATUS SISTEM</th>
                                    <th class="pe-4 py-3 text-muted small fw-bold text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pengajuans as $p)
                                    @php
                                        $isMidtrans = isset($p->order_number);
                                        $no = $isMidtrans ? $p->order_number : str_pad($p->id, 3, '0', STR_PAD_LEFT);
                                        $items = $isMidtrans ? $p->items : $p->details;
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-primary">{{ $isMidtrans ? '#'.$no : 'REQ-'.$no }}</div>
                                            <span class="badge {{ $isMidtrans ? 'bg-light text-primary border border-primary' : 'bg-light text-info border border-info' }} rounded-pill" style="font-size: 10px;">
                                                {{ $isMidtrans ? 'MIDTRANS' : 'MANUAL' }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="fw-semibold">{{ $p->user->nama ?? $p->user->name }}</div>
                                            <div class="text-muted small">{{ $p->created_at->format('d M Y') }}</div>
                                        </td>

                                        <td>
                                            @foreach($items as $item)
                                                <div class="small text-dark mb-1">
                                                    • {{ $item->item->nama_barang }} 
                                                    <span class="text-muted">({{ $isMidtrans ? $item->quantity : $item->qty_requested }} {{ $item->item->satuan }})</span>
                                                </div>
                                            @endforeach
                                        </td>

                                        <td class="text-center">
                                            @if($status === 'submitted')
                                                <span class="text-warning small fw-bold"><i class="bi bi-clock-history me-1"></i> Pending</span>
                                            @elseif($status === 'approved')
                                                <span class="text-success small fw-bold"><i class="bi bi-check-circle me-1"></i> Siap Kirim</span>
                                            @elseif($status === 'delivered')
                                                <span class="text-info small fw-bold"><i class="bi bi-truck me-1"></i> Dikirim</span>
                                            @else
                                                <span class="text-secondary small fw-bold">{{ ucfirst($status) }}</span>
                                            @endif
                                        </td>

                                        <td class="pe-4 text-end">
                                            @if($status === 'submitted' && !$isMidtrans)
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn btn-sm btn-success rounded-3" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $p->id }}" title="Setujui">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger rounded-3" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $p->id }}" title="Tolak">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            @elseif($status === 'approved')
                                                <button class="btn btn-sm btn-primary px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $p->id }}">
                                                    <i class="bi bi-pencil-square me-1"></i> Resi
                                                </button>
                                            @elseif($status === 'delivered')
                                                <span class="badge bg-light text-muted border px-3 fw-normal">Wait Confirm</span>
                                            @elseif($status === 'received')
                                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#detailTransaksiModal-{{ $p->id }}">
                                                    Detail
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Include Modals tetap di dalam loop table agar ID sesuai --}}
                                    @includeWhen($status === 'submitted' && !$isMidtrans, 'peri::admin.modals.approve', ['pengajuan' => $p])
                                    @includeWhen($status === 'submitted' && !$isMidtrans, 'peri::admin.modals.reject', ['pengajuan' => $p])
                                    @includeWhen($status === 'approved', 'peri::admin.modals.assign', ['pengajuan' => $p, 'staff_pengiriman' => $staff_pengiriman])
                                    @includeWhen($status === 'received', 'peri::admin.modals.history', ['pengajuan' => $p])

                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                            Tidak ada data transaksi untuk status "{{ $status }}".
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .table thead th { letter-spacing: 0.05em; border-bottom: none; }
    .table tbody td { border-bottom: 1px solid #f8f9fa; }
    .table-hover tbody tr:hover { background-color: #fbfbfb; transition: 0.2s; }
    .btn-sm { padding: 0.4rem 0.8rem; }
</style>
@endsection