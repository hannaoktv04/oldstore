@extends('peri::layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Riwayat Aktivitas Anda</h4>

    {{-- BAGIAN PESANAN BELANJA (MIDTRANS) --}}
    @if($orders->count() > 0)
        <h6 class="text-muted mb-3"><i class="bi bi-cart-fill"></i> Pesanan Belanja (Midtrans)</h6>
        @foreach ($orders as $order)
            <div class="card border-0 mb-3 p-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $order->order_number }}</h5>
                        <small class="text-muted">{{ $order->created_at->format('d F Y, H:i') }}</small>
                    </div>
                    <div>
                        {{-- Logika Status Pending/Success --}}
                        @if($order->payment_status == 'pending')
                            <em class="fw-bold text-warning"><i class="bi bi-hourglass-split"></i> Menunggu Pembayaran</em>
                        @elseif($order->payment_status == 'success')
                            <em class="fw-bold text-success"><i class="bi bi-check-circle-fill"></i> Pembayaran Berhasil</em>
                        @else
                            <em class="fw-bold text-danger"><i class="bi bi-x-circle"></i> {{ strtoupper($order->payment_status) }}</em>
                        @endif
                    </div>
                </div>

                <div class="row align-items-center mt-2">
                    <div class="col-md-7">
                        @foreach ($order->items as $item)
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ asset('storage/' . ($item->item->photo_url ?? 'assets/img/default.png')) }}"
                                    class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                <div>
                                    <span class="small fw-semibold">{{ $item->item->nama_barang }}</span><br>
                                    <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-5 text-end">
                        <div class="mb-2">
                            <small class="text-muted">Total Bayar:</small>
                            <span class="fw-bold text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        
                        {{-- Tombol bayar hanya muncul jika pending --}}
                        @if($order->payment_status == 'pending')
                            <a href="{{ route('cart.invoice', $order->id) }}" class="btn btn-sm btn-primary px-4">Bayar Sekarang</a>
                        @else
                            <a href="{{ route('cart.invoice', $order->id) }}" class="btn btn-sm btn-outline-secondary px-4">Detail Invoice</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        <hr class="my-4">
    @endif

    {{-- BAGIAN PENGAJUAN BARANG (EXISTING) --}}
    @if($requests->count() > 0)
        <h6 class="text-muted mb-3"><i class="bi bi-clipboard-check"></i> Pengajuan Barang</h6>
        @foreach ($requests as $request)
            {{-- Pakai kode card pengajuan yang sudah kamu miliki sebelumnya --}}
            <div class="card border-0 mb-3 p-3 shadow-sm bg-light">
                {{-- Konten requests kamu di sini --}}
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Pengajuan #{{ str_pad($loop->revindex + 1, 3, '0', STR_PAD_LEFT) }}</h6>
                    <span class="badge bg-info text-dark">{{ strtoupper($request->status) }}</span>
                </div>
            </div>
        @endforeach
    @endif

    @if($orders->isEmpty() && $requests->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-bag-x fs-1 text-muted"></i>
            <p class="mt-3">Belum ada riwayat pesanan atau pengajuan.</p>
        </div>
    @endif
</div>
@endsection