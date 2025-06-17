@props(['pengajuan'])

@php
    $id = $pengajuan->id;
    $no = str_pad($id, 3, '0', STR_PAD_LEFT);
@endphp

<div class="mb-5 p-3 border rounded">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0">Pengajuan #{{ $no }}</h5>
            <small class="text-muted">
                Pemohon: <strong>{{ $pengajuan->user->nama ?? 'Tidak diketahui' }}</strong>
                &middot;
                Diajukan: {{ \Carbon\Carbon::parse($pengajuan->tanggal_permintaan)->format('d F Y') }}
            </small>
        </div>
    </div>

    <div class="row gy-4 gx-2 align-items-center">
        {{-- Detail Barang --}}
        <div class="col-12 col-md-6 col-lg-4">
            @foreach ($pengajuan->details as $detail)
                <div class="d-flex align-items-start mb-3">
                    <img src="{{ $detail->item->photo_url }}" class="me-3 rounded" width="80" height="80" style="object-fit: cover;">
                    <div>
                        <strong>{{ $detail->item->category->categori_name ?? 'Kategori Tidak Diketahui' }}</strong><br>
                        {{ $detail->item->nama_barang }}<br>
                        Jumlah: {{ $detail->qty_requested }} {{ $detail->item->satuan }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Jadwal --}}
        <div class="col-12 col-md-4 col-lg-5 d-flex flex-column justify-content-center text-center text-md-center">
            <small class="text-muted">Jadwal Pengambilan</small>
            @if(optional($pengajuan->itemDelivery)->tanggal_kirim)
                <strong>{{ \Carbon\Carbon::parse($pengajuan->itemDelivery->tanggal_kirim)->format('d F Y') }}</strong>
            @else
                <strong class="text-danger">Belum Dijadwalkan</strong>
            @endif
        </div>

        {{-- Aksi --}}
        <div class="col-12 col-md-2 col-lg-3 d-flex flex-column align-items-center text-center text-md-end ms-md-auto">
            <small class="text-muted d-block mb-1">Aksi</small>
            <x-pengajuan-actions :pengajuan="$pengajuan" />
        </div>

    {{-- Modals --}}
    @include('admin.modals.approve', ['pengajuan' => $pengajuan])
    @include('admin.modals.reject',  ['pengajuan' => $pengajuan])
</div>
