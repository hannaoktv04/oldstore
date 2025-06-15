@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Pengajuan - Status: {{ ucfirst($status) }}</h4>

    @forelse ($pengajuans as $pengajuan)
        <div class="mb-5 p-3 border rounded">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0">Pengajuan #{{ str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT) }}</h5>
                    <small class="text-muted">
                        Pemohon: <strong>{{ $pengajuan->user->nama ?? 'Tidak diketahui' }}</strong> &middot;
                        Diajukan: {{ \Carbon\Carbon::parse($pengajuan->tanggal_permintaan)->format('d F Y') }}
                    </small>
                </div>

                <div class="text-center">
                    <small class="text-muted">Jadwal Pengambilan</small><br>
                    <strong>{{ \Carbon\Carbon::parse($pengajuan->pickup_schedule)->format('d F Y') }}</strong>
                </div>


                <div class="text-center">
                    <small class="text-muted d-block mb-1">Aksi</small>
                    <div class="d-flex gap-2 justify-content-end">
                        <!-- Tombol trigger -->
                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $pengajuan->id }}">
                            <i class="bi bi-check-circle fs-5"></i>
                        </a>
                        {{-- Modal --}}
                            <div class="modal fade" id="approveModal-{{ $pengajuan->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form method="POST" action="{{ route('pengajuan.approve', $pengajuan->id) }}">
                                        @csrf
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header bg-success text-white rounded-top-4">
                                                <h5 class="modal-title">Setujui Pengajuan #{{ str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT) }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-4 text-muted">Silakan sesuaikan jumlah barang yang disetujui atau centang untuk menyetujui sesuai permintaan.</p>

                                                @foreach ($pengajuan->details as $detail)
                                                    <div class="mb-4 border rounded p-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-start">
                                                                <strong>{{ $detail->item->nama_barang }}</strong><br>
                                                                <small class="text-muted">Diminta: {{ $detail->qty_requested }} {{ $detail->item->satuan }}</small><br>
                                                                <small class="text-muted">Stok tersedia: <strong>{{ $detail->item->stocks->sum('qty') }}</strong></small>
                                                            </div>
                                                            <div class="form-check text-end">
                                                                <input type="checkbox" class="form-check-input setujui-semua" data-target="#approvedQty{{ $detail->id }}" data-jumlah="{{ $detail->qty_requested }}" id="check{{ $detail->id }}">
                                                                <label class="form-check-label small" for="check{{ $detail->id }}">Sesuai permintaan</label>
                                                            </div>
                                                        </div>
                                                        <input type="number"
                                                            name="approved_qty[{{ $detail->id }}]"
                                                            id="approvedQty{{ $detail->id }}"
                                                            class="form-control mt-2"
                                                            max="{{ $detail->qty_requested }}"
                                                            min="0"
                                                            placeholder="Jumlah disetujui"
                                                            required>
                                                    </div>
                                                @endforeach

                                                <div class="mb-3">
                                                    <label class="form-label">Keterangan Persetujuan <span class="text-muted small">(opsional)</span></label>
                                                    <textarea class="form-control" name="admin_note" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light rounded-bottom-4">
                                                <button type="submit" class="btn btn-success px-4 py-2">Setujui Pengajuan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        {{-- Modal Tolak --}}
                        <div class="modal fade" id="rejectModal-{{ $pengajuan->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('pengajuan.reject', $pengajuan->id) }}">
                                    @csrf
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header bg-danger text-white rounded-top-4">
                                            <h5 class="modal-title">Tolak Pengajuan #{{ str_pad($pengajuan->id,3,'0',STR_PAD_LEFT) }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p class="text-muted">Berikan alasan penolakan (opsional).</p>
                                            <textarea name="admin_note" rows="3" class="form-control"></textarea>
                                        </div>

                                        <div class="modal-footer bg-light rounded-bottom-4">
                                            <button type="submit" class="btn btn-danger px-4">Tolak Pengajuan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>




                        <a href="#" class="text-primary" title="Detail"><i class="bi bi-file-earmark-text fs-5"></i></a>
                        <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $pengajuan->id }}" title="Tolak">
                            <i class="bi bi-x-circle fs-5"></i>
                        </a>

                    </div>
                </div>
            </div>

            @foreach ($pengajuan->details as $detail)
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $detail->item->photo_url }}" class="me-3 rounded" width="80" height="80" style="object-fit: cover;">
                    <div>
                        <strong>{{ $detail->item->category->categori_name ?? 'Kategori Tidak Diketahui' }}</strong><br>
                        {{ $detail->item->nama_barang }} <br>
                        Jumlah: {{ $detail->qty_requested }} {{ $detail->item->satuan }}
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div class="alert alert-info">Belum ada pengajuan dengan status "{{ $status }}".</div>
    @endforelse

    <a href="{{ route('admin.dashboard') }}" class="btn btn-success">Kembali ke Dashboard</a>
</div>
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.setujui-semua').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const targetInput = document.querySelector(this.dataset.target);
                if (this.checked) {
                    targetInput.value = this.dataset.jumlah;
                    targetInput.setAttribute('readonly', true);
                } else {
                    targetInput.removeAttribute('readonly');
                    targetInput.value = '';
                }
            });
        });
    });
</script>
@endpush
