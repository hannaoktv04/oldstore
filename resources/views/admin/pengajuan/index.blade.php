@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <h4 class="mb-4">Daftar Pengajuan - Status: {{ ucfirst($status) }}</h4>

        @forelse ($pengajuans as $pengajuan)
            @php
                $id = $pengajuan->id;
                $no = str_pad($id, 3, '0', STR_PAD_LEFT);

                $filteredDetails = $pengajuan->details->filter(function ($detail) use ($status) {
                    if ($status === 'submitted') {
                        return true;
                    } else {
                        return $detail->qty_approved > 0;
                    }
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
                                &middot;
                                Diajukan: {{ \Carbon\Carbon::parse($pengajuan->tanggal_permintaan)->format('d F Y') }}
                            </small>
                        </div>
                    </div>

                    <div class="row gy-4 gx-2 align-items-center">
                        <div class="col-12 col-md-6 col-lg-4">
                            @foreach ($filteredDetails as $detail)
                                <div class="d-flex align-items-start mb-3">
                                    <img src="{{ $detail->item->gallery->first()
                                        ? asset('storage/' . $detail->item->gallery->first())
                                        : asset('assets/img/default.png') }}"
                                        class="me-3 rounded border" width="80" height="80" style="object-fit: cover;"
                                        alt="{{ $detail->item->nama_barang }}">
                                    <div>
                                        <strong>{{ $detail->item->category->categori_name ?? 'Kategori Tidak Diketahui' }}</strong><br>
                                        {{ $detail->item->nama_barang }}<br>
                                        Jumlah:
                                        @if ($status === 'submitted')
                                            {{ $detail->qty_requested }}
                                        @else
                                            {{ $detail->qty_approved }}
                                        @endif
                                        {{ $detail->item->satuan->nama_satuan ?? 'Satuan Tidak Diketahui' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-12 col-md-4 col-lg-5 text-center text-md-center">
                            @if ($status === 'received')
                                <small class="text-muted">Tanggal Selesai</small><br>
                                @if ($completedDate)
                                    <strong>{{ \Carbon\Carbon::parse($completedDate)->format('d F Y, H:i') }}</strong>
                                @else
                                    <strong class="texta-danger">Belum Selesai</strong>
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

                        <div
                            class="col-12 col-md-2 col-lg-3 d-flex flex-column align-items-center text-md-end ms-md-auto gap-2">
                            <x-pengajuan-actions :pengajuan="$pengajuan" :status="$status" />
                        </div>
                    </div>

                    @if ($status === 'submitted')
                        @include('admin.modals.approve', ['pengajuan' => $pengajuan])
                        @include('admin.modals.reject', ['pengajuan' => $pengajuan])
                    @endif

                    @if ($status === 'approved')
                        @include('admin.modals.assign', [
                            'pengajuan' => $pengajuan,
                            'staff_pengiriman' => $staff_pengiriman,
                        ])
                    @endif
                    @if ($status === 'received')
                        @include('admin.modals.history', ['pengajuan' => $pengajuan])
                    @endif

                </div>
            @endif
        @empty
            <div class="alert alert-info">Belum ada pengajuan dengan status "{{ $status }}".</div>
        @endforelse

        <a href="{{ route('admin.dashboard.index') }}" class="btn btn-success">Kembali ke Dashboard</a>
    </div>

    @include('partials.alert')
@endsection
