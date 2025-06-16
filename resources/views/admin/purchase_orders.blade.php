@extends('layouts.app')

@section('title', 'Daftar Purchase Order')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Purchase Order</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse ($purchaseOrders as $po)
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">#{{ $po->nomor_po }}</h5>
                    <small class="text-muted">Tanggal: {{ \Carbon\Carbon::parse($po->tanggal_po)->format('d M Y') }}</small>
                </div>
                <span class="badge 
                    @if($po->status == 'draft') bg-secondary
                    @elseif($po->status == 'submitted') bg-warning text-dark
                    @elseif($po->status == 'received') bg-success
                    @endif">
                    {{ ucfirst($po->status) }}
                </span>
            </div>
            <div class="card-body">
                @if($po->details->isEmpty())
                    <p class="text-muted">Belum ada item dalam PO ini.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($po->details as $detail)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $detail->item->nama_barang }}</strong> <br>
                                    <small class="text-muted">{{ $detail->item->kategori }}</small>
                                </div>
                                <span>{{ $detail->qty }} {{ $detail->item->satuan }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('purchase_orders.show', $po->id) }}" class="btn btn-outline-primary btn-sm">Detail</a>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Belum ada purchase order yang tercatat.</div>
    @endforelse
</div>
@endsection
