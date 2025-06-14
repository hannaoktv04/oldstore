@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Riwayat Log Stok Barang</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Barang</th>
                <th>Tipe</th>
                <th>Qty</th>
                <th>Sumber</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                <td>{{ $log->item->nama_barang }}</td>
                <td><span class="badge bg-{{ $log->tipe === 'out' ? 'danger' : 'success' }}">{{ strtoupper($log->tipe) }}</span></td>
                <td>{{ $log->qty }}</td>
                <td>{{ strtoupper($log->sumber) }} #{{ $log->sumber_id }}</td>
                <td>{{ $log->deskripsi }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
</div>
@endsection
