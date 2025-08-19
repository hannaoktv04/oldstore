@extends('peri::layouts.staff')

@section('content')
<div class="container">

    <h5 class="mb-4">Daftar Pengiriman Selesai</h5>

    <form method="GET" class="mb-4 d-flex align-items-center gap-2">
        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control" style="max-width: 200px;">
        <button type="submit" class="btn btn-success btn-sm">Filter</button>
        <a href="{{ route('staff-pengiriman.selesai') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </form>

    @forelse($pengiriman as $item)
        <div class="card mb-3 shadow-sm rounded-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>{{ $item->request->user->nama }}</strong>
                        <small class="text-muted">| Resi: KP{{ str_pad($item->request->id, 6, '0', STR_PAD_LEFT) }}</small>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                        <i class="bi bi-card-checklist"></i> Cek Detail
                    </button>
                </div>

                <div class="list-group list-group-flush">
                    @foreach($item->request->details as $detail)
                        <div class="list-group-item d-flex align-items-center py-2">
                            <img src="{{ $detail->item->gallery->first()
                                ? asset('storage/' . $detail->item->gallery->first())
                                : asset('assets/img/default.png')
                            }}" alt="{{ $detail->item->nama_barang }}" width="60" height="60" class="rounded border me-3" style="object-fit: cover;">

                            <div>
                                <div class="fw-semibold">{{ $detail->item->nama_barang }}</div>
                                <small class="text-muted">Jumlah: {{ $detail->qty_approved }} {{ $detail->item->satuan->nama_satuan }}</small> <br>
                                <small class="text-muted">Catatan: {{ $item->catatan ?? 'Tidak ada catatan' }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4">
                    <div class="modal-header">
                        <h5 class="modal-title">Status Pengiriman - KP{{ str_pad($item->request->id, 6, '0', STR_PAD_LEFT) }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <ul class="timeline list-unstyled">
                            <li class="mb-3">
                                <div><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Pengajuan Diajukan</strong></div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->request->tanggal_permintaan)->format('H:i:s, d M Y') }}</small>
                            </li>

                            <li class="mb-3">
                                <div><i class="bi bi-box-seam text-warning me-2"></i><strong>Disetujui</strong></div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->request->updated_at)->format('H:i:s, d M Y') }}</small>
                            </li>

                            <li class="mb-3">
                                <div><i class="bi bi-calendar-event text-primary me-2"></i><strong>Dikirimkan sesuai jadwal</strong></div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal_kirim)->format('H:i:s, d M Y') }}</small>
                            </li>

                            <li class="mb-3">
                                <div><i class="bi bi-clipboard-check text-danger me-2"></i><strong>Diterima</strong></div>

                                <button type="button" class="btn btn-link p-0 text-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#buktiModal-{{ $item->id }}">
                                    <i class="bi bi-image"></i> Lihat Bukti Pengiriman
                                </button> <br>

                                <small class="text-muted">
                                    Diantar oleh: <strong>{{ $item->staff->nama ?? 'Staff tidak ditemukan' }}</strong><br>
                                    {{ \Carbon\Carbon::parse($item->updated_at)->format('H:i:s, d M Y') }}
                                </small>
                            </li>
                        </ul>

                        @php
                            $start = \Carbon\Carbon::parse($item->request->tanggal_permintaan);
                            $end = \Carbon\Carbon::parse($item->updated_at);
                            $diff = $start->diff($end);
                            $diffText = '';

                            if ($diff->d > 0) $diffText .= $diff->d . ' hari ';
                            if ($diff->h > 0) $diffText .= $diff->h . ' jam ';
                            if ($diff->i > 0) $diffText .= $diff->i . ' menit ';
                        @endphp

                        <div class="alert alert-success mt-4 text-center">
                            <i class="bi bi-check-circle-fill me-2"></i> Pesanan sudah selesai <br>
                            <small class="d-block mt-1">Waktu proses: {{ $diffText ?: 'Kurang dari 1 menit' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($item->bukti_foto)
        <div class="modal fade" id="buktiModal-{{ $item->id }}" tabindex="-1" aria-labelledby="buktiModalLabel-{{ $item->id }}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
              <div class="modal-header border-0">
                <h5 class="modal-title" id="buktiModalLabel-{{ $item->id }}">Bukti Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body text-center">
                <img
                  src="{{ asset('storage/' . $item->bukti_foto) }}"
                  alt="Bukti Pengiriman"
                  class="img-thumbnail"
                  style="max-width: 100%; width: 100%; max-height: 300px; object-fit: contain;"
                >
              </div>
            </div>
          </div>
        </div>
        @endif

    @empty
        <div class="alert alert-info text-center">
            Belum ada pengiriman selesai.
        </div>
    @endforelse

</div>

<style>
.timeline li {
    position: relative;
    padding-left: 25px;
}

.timeline li::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 6px;
    width: 8px;
    height: 8px;
    background: #0d6efd;
    border-radius: 50%;
}
</style>
@endsection
