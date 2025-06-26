@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h4 class="mb-4">Status Pengajuan Anda</h4>

  @forelse ($requests as $request)
    @php
      $pengajuanNumber = str_pad($requests->count() - $loop->index, 3, '0', STR_PAD_LEFT);
    @endphp

    <div class="card border-0 mb-3 p-3 shadow-sm">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <h5 class="fw-bold mb-0">Pengajuan #{{ $pengajuanNumber }}</h5>
          <small class="text-muted">{{ \Carbon\Carbon::parse($request->tanggal_permintaan)->format('d F Y') }}</small>
        </div>
      </div>

      <div class="row gy-4 gx-2 align-items-center">
        <div class="col-12 col-md-6 col-lg-4">
          @foreach ($request->details as $detail)
          <div class="d-flex align-items-start mb-3">
            <img src="{{ asset('storage/' . ($detail->item->gallery->first() ?? 'assets/img/default.png')) }}"
              class="me-3 rounded" width="80" height="80" style="object-fit: cover;">
            <div>
              <strong>{{ $detail->item->category->categori_name ?? 'Kategori Tidak Diketahui' }}</strong><br>
              {{ $detail->item->nama_barang }}<br>
              Jumlah: {{ $detail->qty_requested }} {{ $detail->item->satuan }}
            </div>
          </div>
          @endforeach
        </div>

        <div class="col-12 col-md-4 col-lg-3 text-center">
          @if($request->tanggal_pengambilan)
              <em>{{ \Carbon\Carbon::parse($request->tanggal_pengambilan)->format('d F Y') }}</em>
              @if($request->status == 'submitted')
                <a href="#" class="text-dark ms-2 btn-calendar" data-request-id="{{ $request->id }}">
                  <i class="bi bi-calendar"></i>
                </a>
              @endif
          @elseif($request->status == 'submitted')
              <a href="#" class="text-dark btn-calendar" data-request-id="{{ $request->id }}">
                <em>Atur Tanggal Pengiriman</em> <i class="bi bi-calendar"></i>
              </a>
          @elseif($request->status == 'rejected')
              <strong class="text-danger"><em>Permintaan ditolak</em></strong>
          @else
              <strong class="text-danger"><em>Belum Dijadwalkan</em></strong>
          @endif
        </div>

        <div class="col-12 col-md-2 col-lg-2 d-flex flex-column align-items-center text-center">
          <a href="#" class="btn btn-outline-secondary btn-sm" title="Bubuhi TTD">
            <i class="bi bi-pen-fill"></i>
          </a>
        </div>

        <div class="col-12 col-md-3 col-lg-3 d-flex flex-column justify-content-center align-items-center text-center">
          <div>
            @switch($request->status)
              @case('submitted') 
                <em class="fw-semibold text-primary"><i class="bi bi-hourglass-split me-1"></i> Diproses</em> 
                @break
              @case('approved') 
                <em class="fw-semibold text-warning"><i class="bi bi-check2-square me-1"></i> Disetujui</em> 
                @break
              @case('delivered') 
                <em class="fw-semibold text-info"><i class="bi bi-truck me-1"></i> Dikirim</em> 
                @break
              @case('received') 
                @if($request->user_confirmed)
                  <em class="fw-semibold text-success"><i class="bi bi-check-circle-fill me-1"></i> Selesai</em>
                @else
                  <em class="fw-semibold text-success"><i class="bi bi-box-seam me-1"></i> Diterima</em>
                @endif
                @break
              @case('rejected') 
                <em class="fw-semibold text-danger"><i class="bi bi-x-circle me-1"></i> Ditolak</em> 
                @break
              @default 
                <em class="text-muted">{{ ucfirst($request->status) }}</em>
            @endswitch
            <br>
            <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $request->id }}">
              Cek Status Pengajuan
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="statusModal-{{ $request->id }}" tabindex="-1" aria-labelledby="statusModalLabel-{{ $request->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 p-3">
          <div class="modal-header border-0">
            <h5 class="modal-title">Status Pesanan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body px-3">
            <ul class="list-unstyled small">
              <li class="mb-3">
                <strong>✅ Pengajuan Diajukan</strong><br>
                {{ \Carbon\Carbon::parse($request->created_at)->format('H:i:s, d M Y') }}
              </li>

              @if(in_array($request->status, ['approved', 'delivered', 'received']) && $request->itemDelivery)
              <li class="mb-3">
                <strong>📦 Disetujui & Dikirim</strong><br>
                {{ \Carbon\Carbon::parse($request->itemDelivery->created_at)->format('H:i:s, d M Y') }}
              </li>
              @endif

              @if($request->tanggal_pengambilan)
              <li class="mb-3">
                <strong>📅 Jadwal Pengambilan</strong><br>
                {{ \Carbon\Carbon::parse($request->tanggal_pengambilan)->format('d M Y') }}
              </li>
              @endif

              @if($request->status === 'received' && $request->itemDelivery && $request->itemDelivery->bukti_foto)
              <li class="mb-3">
                <strong>📥 Diterima & Dikonfirmasi</strong><br>
                <a href="#" data-bs-toggle="modal" data-bs-target="#buktiModal-{{ $request->id }}">
                  Lihat Bukti Pengiriman
                </a><br>
                {{ \Carbon\Carbon::parse($request->itemDelivery->updated_at)->format('H:i:s, d M Y') }}
              </li>
              @endif

              @if($request->status == 'rejected')
              <li class="mb-3 text-danger">
                <strong>❌ Ditolak</strong><br>
                {{ $request->keterangan ?? 'Tidak ada keterangan' }}<br>
                {{ \Carbon\Carbon::parse($request->updated_at)->format('H:i:s, d M Y') }}
              </li>
              @endif
            </ul>

            @if($request->status === 'received')
              @if(!$request->user_confirmed)
              <form action="{{ route('pengajuan.konfirmasiUser', $request->id) }}" method="POST" class="text-center">
                @csrf
                <button type="submit" class="btn btn-success btn-sm mt-2">Konfirmasi Pesanan</button>
              </form>
              @else
              <div class="text-center text-success mt-2 fw-semibold">
                <i class="bi bi-check-circle-fill"></i> Pesanan sudah selesai
              </div>
              @endif
            @endif
          </div>
          <div class="modal-footer border-0 justify-content-center">
            <a href="{{ route('pengajuan.enota', $request->id) }}" class="btn btn-outline-success btn-sm">Lihat E-Nota</a>
          </div>
        </div>
      </div>
    </div>

    @if($request->itemDelivery && $request->itemDelivery->bukti_foto)
    <div class="modal fade" id="buktiModal-{{ $request->id }}" tabindex="-1" aria-labelledby="buktiModalLabel-{{ $request->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
          <div class="modal-header border-0">
            <h5 class="modal-title" id="buktiModalLabel-{{ $request->id }}">Bukti Pengiriman</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <img src="{{ asset('storage/' . $request->itemDelivery->bukti_foto) }}" alt="Bukti Pengiriman" class="img-fluid rounded shadow-sm">
          </div>
        </div>
      </div>
    </div>
    @endif

  @empty
    <div class="alert alert-info">Belum ada pengajuan barang.</div>
  @endforelse
</div>
@endsection
