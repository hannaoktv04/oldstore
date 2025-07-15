@extends('layouts.admin')

@section('title', 'Detail Stok Opname')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Opname Bulan: {{ $session->periode_bulan }}
            <span class="badge bg-{{ $session->status == 'aktif' ? 'success' : 'secondary' }}">{{ $session->status }}</span>
        </h4>
        <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <p><strong>Tanggal Mulai:</strong> {{ $session->tanggal_mulai }}</p>
            </div>
            <div class="col-md-4">
                <p><strong>Tanggal Selesai:</strong> {{ $session->tanggal_selesai ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <p><strong>Dibuka Oleh:</strong> {{ $session->user->nama }}</p>
            </div>
        </div>

        <form action="{{ route('admin.stock_opname.submit', $session->id) }}" method="POST">
            @csrf
            <table class="table table-bordered" id="opname-table">
                <thead>
                    <tr class="table-light">
                        <th>No.</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Stok Sistem</th>
                        <th>Stok Fisik</th>
                        <th>Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $item)
                        @php
                            $stokSistem = $item->stock->qty ?? 0;
                            $stokFisik = old("qty_fisik.{$item->id}", $item->opname_value);
                            $selisih = $stokFisik - $stokSistem;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>{{ number_format($stokSistem, 2) }}</td>
                            <td>
                                <input type="number" name="qty_fisik[{{ $item->id }}]" step="0.01" class="form-control form-control-sm qty-fisik" data-id="{{ $item->id }}" value="{{ $stokFisik }}" required>
                            </td>
                            <td>
                                <span class="selisih-text" id="selisih-{{ $item->id }}">{{ number_format($selisih, 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success">Simpan dan Ajukan</button>
            </div>
        </form>

        @if($session->status === 'aktif')
        <hr>
        <div class="mt-4 p-3 bg-light rounded">
            <h5 class="mb-3">Selesaikan Sesi Stock Opname</h5>
            <form action="{{ route('admin.stock_opname.end') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $session->id }}">

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" required
                               min="{{ \Carbon\Carbon::parse($session->tanggal_mulai)->format('Y-m-d') }}"
                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <button type="submit" class="btn btn-success"
                                onclick="return confirm('Anda yakin ingin menyelesaikan sesi ini? Transaksi akan diaktifkan kembali.')">
                            <i class="fas fa-check-circle"></i> Selesaikan Sesi
                        </button>
                    </div>
                </div>
                <small class="text-muted">* Menyelesaikan sesi akan mengaktifkan kembali transaksi</small>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.qty-fisik').forEach(input => {
        input.addEventListener('input', function () {
            const row = this.closest('tr');
            const sistem = parseFloat(row.children[4].innerText.replace(/,/g, ''));
            const fisik = parseFloat(this.value) || 0;
            const selisih = (fisik - sistem).toFixed(2);
            row.querySelector('.selisih-text').innerText = selisih;
        });
    });
</script>
@endsection
