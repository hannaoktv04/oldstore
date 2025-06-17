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
                            <img
                                src="{{ asset('storage/' . ($detail->item->photo->image ?? 'placeholder.jpg')) }}"
                                class="me-3 rounded"
                                width="80" height="80"
                                style="object-fit: cover;">
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
                          <a href="#"
                            class="text-dark ms-2 btn-calendar"
                            data-request-id="{{ $request->id }}"
                            title="Ubah Tanggal">
                              <i class="bi bi-calendar"></i>
                          </a>
                      @endif
                  @elseif($request->status == 'submitted')
                      <a href="#"
                        class="text-dark btn-calendar"
                        data-request-id="{{ $request->id }}"
                        title="Pilih Tanggal">
                          <em>Atur Tanggal Pengambilan</em> <i class="bi bi-calendar"></i>
                      </a>
                  @elseif($request->status == 'rejected')
                      <strong class="text-danger"><em>Permintaan ditolak</em></strong>
                  @else
                      <strong class="text-danger"><em>Belum Dijadwalkan</em></strong>
                  @endif
              </div>


                <div class="col-12 col-md-2 col-lg-2 d-flex flex-column align-items-center text-center">
                    <a href="#" class="btn btn-outline-secondary btn-sm" title="TTD Nota">
                        <i class="bi bi-pen-fill"></i>
                    </a>
                </div>

                <div class="col-12 col-md-3 col-lg-3 d-flex flex-column justify-content-center align-items-center text-center">
                    <div>
                        @switch($request->status)
                            @case('submitted') <em class="fw-semibold">Sedang Diproses...</em> @break
                            @case('approved') <em class="fw-semibold">Barang Disetujui,<br>Menunggu Pengiriman</em> @break
                            @case('rejected') <em class="fw-semibold">Ditolak</em> @break
                            @case('delivered') <em class="fw-semibold">Dikirim</em> @break
                            @case('received') <em class="fw-semibold">Diterima</em> @break
                            @default <em class="text-muted fw-bold">{{ ucfirst($request->status) }}</em>
                        @endswitch
                        <br>
                        <a href="#"
                           class="text-success"
                           data-bs-toggle="modal"
                           data-bs-target="#statusModal-{{ $request->id }}">
                           Cek Status Pengajuan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="statusModal-{{ $request->id }}" tabindex="-1" aria-labelledby="statusModalLabel-{{ $request->id }}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 p-3">
              <div class="modal-header border-0">
                <h5 class="modal-title" id="statusModalLabel-{{ $request->id }}">Status Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <div class="modal-body px-3">
                <ul class="list-unstyled small">
                  <li class="mb-3">
                    <strong>Pesanan dilakukan</strong><br>
                    {{ \Carbon\Carbon::parse($request->created_at)->format('H:i:s, d M Y') }}
                  </li>
                  @if(in_array($request->status, ['submitted', 'approved', 'delivered', 'received']))
                    <li class="mb-3">
                      <strong>Pesanan sedang ditinjau</strong><br>
                      {{ \Carbon\Carbon::parse($request->updated_at)->format('H:i:s, d M Y') }}
                    </li>
                  @endif
                  @if(in_array($request->status, ['approved', 'delivered', 'received']))
                    <li class="mb-3">
                      <strong>Pesanan disetujui dan segera dikirimkan</strong><br>
                      {{ \Carbon\Carbon::parse($request->updated_at)->format('H:i:s, d M Y') }}
                    </li>
                  @endif
                  @if(in_array($request->status, ['delivered', 'received']) && $request->tanggal_pengambilan)
                    <li class="mb-3">
                      <strong>Pesanan sudah diterima sesuai waktu yang dijadwalkan</strong><br>
                      {{ \Carbon\Carbon::parse($request->tanggal_pengambilan)->format('H:i:s, d M Y') }}
                    </li>
                  @endif
                  @if($request->status == 'received')
                    <li class="mb-3">
                      <strong>Pesanan sudah dikonfirmasi</strong><br>
                      {{ \Carbon\Carbon::parse($request->updated_at)->format('H:i:s, d M Y') }}
                    </li>
                  @endif
                </ul>
              </div>
              <div class="modal-footer border-0">
                <a href="#" class="btn btn-outline-success btn-sm">Lihat E-Nota</a>
              </div>
            </div>
          </div>
        </div>
    @empty
        <div class="alert alert-info">Belum ada pengajuan barang.</div>
    @endforelse
</div>

<div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 340px;">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title w-100 text-center" id="calendarModalLabel">Pilih Tanggal Pengambilan</h5>
      </div>
      <div class="modal-body d-flex justify-content-center">
        <div id="calendarContainer" class="w-100" style="max-width: 240px;"></div>
      </div>
    </div>
  </div>
</div>
@endsection
