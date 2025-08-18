@extends('peri::layouts.admin')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Pengajuan - Status: {{ ucfirst($status) }}</h4>

    @forelse ($pengajuans as $pengajuan)
        @php
            $id = $pengajuan->id;
            $no = str_pad($id, 3, '0', STR_PAD_LEFT);

            $filteredDetails = $pengajuan->details->filter(function ($detail) use ($status) {
                return $status === 'submitted' ? true : $detail->qty_approved > 0;
            });

            $shouldDisplay = $filteredDetails->isNotEmpty();
            $completedDate = $pengajuan->updated_at;
        @endphp

        @if ($shouldDisplay)
            <div class="mb-3 p-3 border rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Pengajuan #{{ $no }}</h5>
                        <small class="text-muted">
                            Pemohon: <strong>{{ $pengajuan->user->nama ?? 'Tidak diketahui' }}</strong>
                            &middot; Diajukan:
                            {{ \Carbon\Carbon::parse($pengajuan->tanggal_permintaan)->format('d F Y') }}
                        </small>
                    </div>
                </div>

                <div class="row gy-4 gx-2 align-items-center">
                    {{-- Kiri: daftar item --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        @foreach ($filteredDetails as $detail)
                            <div class="d-flex align-items-start mb-3">
                                @php
                                    $thumb = $detail->item->gallery->first()
                                        ? asset('storage/' . $detail->item->gallery->first())
                                        : asset('assets/img/default.png');
                                @endphp
                                <img src="{{ $thumb }}" class="me-3 rounded border" width="80" height="80"
                                     style="object-fit: cover;" alt="{{ $detail->item->nama_barang }}">
                                <div>
                                    <strong>{{ $detail->item->category->categori_name ?? 'Kategori Tidak Diketahui' }}</strong><br>
                                    {{ $detail->item->nama_barang }}<br>
                                    Jumlah:
                                    {{ $status === 'submitted' ? $detail->qty_requested : $detail->qty_approved }}
                                    {{ $detail->item->satuan->nama_satuan ?? 'Satuan Tidak Diketahui' }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tengah: tanggal --}}
                    <div class="col-12 col-md-4 col-lg-5 text-center text-md-center">
                        @if ($status === 'received')
                            <small class="text-muted">Tanggal Selesai</small><br>
                            @if ($completedDate)
                                <strong>{{ \Carbon\Carbon::parse($completedDate)->format('d F Y, H:i') }}</strong>
                            @else
                                <strong class="text-danger">Belum Selesai</strong>
                            @endif
                        @else
                            <small class="text-muted">Tanggal Pengiriman</small><br>
                            @if ($pengajuan->tanggal_pengiriman)
                                <strong>{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengiriman)->format('d F Y, H:i') }}</strong>
                            @else
                                <strong class="text-danger">Belum Dijadwalkan</strong>
                            @endif
                        @endif
                    </div>

                    {{-- Kanan: ACTIONS (disatukan di sini) --}}
                    <div class="col-12 col-md-2 col-lg-3 d-flex flex-column align-items-center text-md-end ms-md-auto gap-2">
                        @switch($status)
                            @case('submitted')
                                <div class="d-flex justify-content-center justify-content-md-end gap-3">
                                    <button type="button" class="btn btn-outline-success btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal-{{ $pengajuan->id }}"
                                            title="Setujui">
                                        <i class="ri-check-line"></i>
                                    </button>

                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal-{{ $pengajuan->id }}"
                                            title="Tolak">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            @break

                            @case('approved')
                                <div class="d-flex justify-content-center justify-content-md-end gap-3 align-items-center">
                                    <button onclick="printResi({{ $pengajuan->id }})"
                                            class="btn btn-sm btn-outline-primary" title="Cetak Resi">
                                        <i class="ri-printer-line"></i>
                                    </button>

                                    <div id="resi-{{ $pengajuan->id }}" style="display:none;">
                                        @include('peri::admin.resi', ['pengajuan' => $pengajuan])
                                    </div>

                                    @if(!$pengajuan->itemDelivery || !$pengajuan->itemDelivery->staff_pengiriman)
                                        <button class="btn btn-sm btn-outline-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#assignModal-{{ $pengajuan->id }}">
                                            <i class="ri-truck-line" title="Assign Staff Pengiriman"></i>
                                        </button>
                                    @else
                                        <small class="text-muted">
                                            Sudah diassign ke:
                                            <strong>{{ $pengajuan->itemDelivery->staff->nama ?? 'Staff tidak ditemukan' }}</strong>
                                        </small>
                                    @endif
                                </div>
                            @break

                            @case('delivered')
                                <div class="d-flex justify-content-center justify-content-md-end gap-3 align-items-center">
                                    <small class="text-muted">
                                        Dikirim oleh:
                                        <strong>{{ $pengajuan->itemDelivery->staff->nama ?? 'Tidak diketahui' }}</strong>
                                    </small>
                                </div>
                            @break

                            @case('received')
                                <button class="btn btn-outline-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detailTransaksiModal-{{ $pengajuan->id }}">
                                    <i class="ri-more-2-line" title="Detail Transaksi"></i>
                                </button>
                            @break

                            @case('rejected')
                                <div class="text-danger small fst-italic mt-1">
                                    Ditolak: {{ $pengajuan->keterangan ?? 'Tidak ada keterangan' }}
                                </div>
                            @break
                        @endswitch
                    </div>
                </div>

                {{-- MODALS (tetap reusable, hanya dipanggil sesuai status) --}}
                @includeWhen($status === 'submitted', 'peri::admin.modals.approve', ['pengajuan' => $pengajuan])
                @includeWhen($status === 'submitted', 'peri::admin.modals.reject', ['pengajuan' => $pengajuan])

                @includeWhen($status === 'approved', 'peri::admin.modals.assign', [
                    'pengajuan' => $pengajuan,
                    'staff_pengiriman' => $staff_pengiriman,
                ])

                @includeWhen($status === 'received', 'peri::admin.modals.history', ['pengajuan' => $pengajuan])
            </div>
        @endif
    @empty
        <div class="alert alert-info">Belum ada pengajuan dengan status "{{ $status }}".</div>
    @endforelse

    <a href="{{ route('admin.dashboard.index') }}" class="btn btn-primary">Kembali ke Dashboard</a>
</div>
@endsection

@push('scripts')
<script>
function printResi(id) {
    const wrap = document.getElementById('resi-' + id);
    if (!wrap) return;
    const html = wrap.innerHTML;

    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write('<html><head><title>Print Resi</title>');
    doc.write('<style>body{font-family: Arial; font-size:12px} table{width:100%; border-collapse:collapse} th,td{border:1px solid #000; padding:5px} .qr-img{text-align:center; margin:20px auto; display:block}</style>');
    doc.write('</head><body>');
    doc.write(html);
    doc.write('</body></html>');
    doc.close();

    iframe.contentWindow.focus();
    iframe.contentWindow.print();

    setTimeout(() => document.body.removeChild(iframe), 800);
}
</script>
@endpush
