@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="border rounded p-4 shadow-sm bg-white">
        <h4 class="text-center mb-4">E-NOTA PENGAJUAN</h4>

        <div class="mb-3">
            <strong>No. Pengajuan:</strong> #{{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}<br>
            <strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($request->tanggal_permintaan)->format('d M Y') }}<br>
            <strong>Nama Pengguna:</strong> {{ $request->user->nama }}<br>
            <strong>Email:</strong> {{ $request->user->email }}
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($request->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->item->nama_barang }}</td>
                    <td>{{ $detail->item->category->categori_name ?? '-' }}</td>
                    <td>{{ $detail->qty_requested }}</td>
                    <td>{{ $detail->item->satuan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <strong>Status:</strong> 
            @if($request->status === 'received' && $request->user_confirmed)
                <span class="text-success fw-bold">Selesai</span>
            @else
                {{ ucfirst($request->status) }}
            @endif
        </div>

        @if($request->itemDelivery)
        <div>
            <strong>Petugas Pengiriman:</strong> {{ $request->itemDelivery->operator->name ?? '-' }}<br>
            <strong>Tanggal Kirim:</strong> {{ \Carbon\Carbon::parse($request->itemDelivery->tanggal_kirim)->format('d M Y H:i') }}
        </div>
        @endif

        <div class="text-end mt-5">
            <em>Dicetak pada: {{ now()->format('d M Y H:i') }}</em>
        </div>
    </div>
</div>
@endsection
