@extends('peri::layouts.app')

@push('style')
<style>
  /* Ubah ukuran area tampilan tanda tangan di sini */
  :root{ --sig-h:110px; --sig-w:260px; }

  .sig-box{
    height:var(--sig-h);
    width:var(--sig-w);
    margin:6px auto;                 /* center */
    display:flex; align-items:center; justify-content:center;
  }
  .sig-img{
    max-height:var(--sig-h);
    max-width:var(--sig-w);
    width:auto; height:auto;
    object-fit:contain; display:block;
  }
  .sig-name{
    display:inline-block; min-width:220px;
    margin-top:6px; padding-top:4px; border-top:1px solid #000;
  }
  .sig-role{ margin-bottom:.25rem; }

  @media (max-width:576px){
    :root{ --sig-w:200px; --sig-h:90px; }
  }
  @media print{
    :root{ --sig-w:220px; --sig-h:90px; }
    .btn, a[href]{ display:none !important; }
  }
</style>
@endpush

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<div class="container py-4 d-flex justify-content-center">
  <div class="border rounded p-4 shadow-sm bg-white w-100" style="max-width: 720px;">

    <div class="text-center mb-4">
      <div class="d-flex align-items-center justify-content-center">
        <img src="{{ asset('assets/img/logo-komdigi.png') }}" alt="Kop KOMDIGI" class="me-3" style="height: 100px;">
        <div class="text-start">
          <h5 class="mb-0 fw-bold">KEMENTERIAN KOMUNIKASI DAN DIGITAL RI</h5>
          <h6 class="mb-0">SEKRETARIAT JENDERAL</h6>
          <h6 class="mb-1">BIRO SUMBER DAYA MANUSIA DAN ORGANISASI</h6>
          <small>Jl. Medan Merdeka Barat No. 9, Jakarta 10110 Telp. (021) 3865189 • www.komdigi.go.id</small>
        </div>
      </div>

      <hr class="my-3">
      <h5 class="fw-bold text-decoration-underline">MEMO - DINAS</h5>

      <div class="text-end">
        <span>Jakarta, {{ now()->format('d M Y') }}</span>
      </div>
      <div class="text-start">
        <span>Nomor: {{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}/NNNN/{{ now()->year }}</span>
      </div>
    </div>

    <div class="mb-4">
      <p>Kepada Yth. <br> Kepala Bagian Keuangan dan Rumah Tangga</p>
      <p>Berikut ini adalah permintaan pengajuan persediaan barang sebagai berikut:</p>
    </div>

    <table class="table table-bordered">
      <thead class="table-light text-center">
        <tr>
          <th style="width: 5%;">No</th>
          <th>Nama Barang</th>
          <th style="width: 15%;">Jumlah</th>
          <th style="width: 25%;">Diberikan</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($request->details as $index => $detail)
          <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $detail->item->nama_barang }}</td>
            <td class="text-center">
              {{ $detail->qty_requested }}
              {{ $detail->item->satuan->nama_satuan ?? '' }}
            </td>
            <td class="text-center">{{ $detail->qty_approved ?? 'belum disetujui' }}</td>
            <td></td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="row mt-5">
      <div class="col text-center">
        <p class="sig-role">Mengetahui,</p>
        <p>Kasubbag Rumah Tangga</p>

        <div class="sig-box">
          {{-- Jika nanti ada TTD pejabat, tampilkan di sini:
          @if(!empty($ttdKasubbag) && Storage::disk('public')->exists($ttdKasubbag))
            <img class="sig-img" src="{{ asset('storage/'.$ttdKasubbag) }}" alt="TTD Mengetahui">
          @endif
          --}}
        </div>

        <span class="sig-name">&nbsp;</span>
      </div>

      <div class="col text-center">
        <p class="sig-role">Yang Meminta,</p>
        <p>{{ $request->user->jabatan }}</p>

        <div class="sig-box">
          @if($request->ttd_path && Storage::disk('public')->exists($request->ttd_path))
            <img class="sig-img" src="{{ asset('storage/'.$request->ttd_path) }}" alt="TTD">
          @endif
        </div>

        <span class="sig-name">{{ $request->user->nama }}</span>
      </div>
    </div>

    <div class="text-start mt-5">
      <em>Dicetak pada: {{ now()->format('d M Y H:i') }}</em>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-5">
      <div class="text-start mt-4">
        @if (!empty($isAdmin) && $isAdmin)
          <a href="{{ route('admin.pengajuan.status', 'received') }}" class="btn btn-outline-success border-0">
            <i class="bi bi-arrow-left"></i> Kembali
          </a>
        @else
          <a href="{{ route('user.history') }}" class="btn btn-outline-success border-0">
            <i class="bi bi-arrow-left"></i> Kembali
          </a>
        @endif
      </div>
      <div class="text-end mt-4">
        <a href="{{ route('pengajuan.downloadNota', $request->id) }}" class="btn btn-outline-success">
          <i class="bi bi-download"></i> Unduh e-nota
        </a>
      </div>
    </div>

  </div>
</div>
@endsection
