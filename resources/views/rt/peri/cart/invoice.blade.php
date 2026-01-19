@extends('peri::layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">Invoice Pesanan</h4>
                        @php
                            $badgeClass = 'bg-warning';
                            if($order->payment_status == 'success') $badgeClass = 'bg-success text-white';
                            if($order->payment_status == 'expired' || $order->payment_status == 'cancel') $badgeClass = 'bg-danger text-white';
                        @endphp
                        <span class="badge {{ $badgeClass }} p-2">
                            {{ strtoupper($order->payment_status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-2 small uppercase fw-bold">Nomor Pesanan:</h6>
                            <p class="fw-bold text-primary">{{ $order->order_number }}</p>
                            
                            <h6 class="text-muted mb-2 small uppercase fw-bold">Tanggal Pesanan:</h6>
                            <p>{{ $order->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h6 class="text-muted mb-2 small uppercase fw-bold">Alamat Pengiriman:</h6>
                            <p class="mb-0 fw-bold">{{ $order->user->name }}</p>
                            <p class="text-muted small">
                                {{ $order->full_address }}<br>
                                {{ $order->district_code }}, {{ $order->city_code }}<br>
                                {{ $order->province_code }}, {{ $order->postal_code }}
                            </p>
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-borderless">
                            <thead class="border-bottom text-muted small">
                                <tr>
                                    <th>PRODUK</th>
                                    <th class="text-center">JUMLAH</th>
                                    <th class="text-end">HARGA</th>
                                    <th class="text-end">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr class="border-bottom">
                                    <td class="py-3">
                                        <span class="fw-semibold">{{ $item->item->nama_barang }}</span>
                                    </td>
                                    <td class="text-center py-3">{{ $item->quantity }}</td>
                                    <td class="text-end py-3">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-end py-3">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-lg-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal Produk</span>
                                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Ongkos Kirim ({{ strtoupper($order->courier) }})</span>
                                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold fs-5">Total Bayar</span>
                                <span class="fw-bold fs-5 text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-center d-grid gap-2 d-md-block">
                        <a href="{{ route('user.history') }}" class="btn btn-outline-secondary px-4 me-md-2">Lihat Riwayat</a>
                        
                        @if($order->payment_status == 'pending')
                            <button class="btn btn-primary px-5 fw-bold" id="pay-button">Bayar Sekarang</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Menggunakan sandbox.midtrans.com untuk development, ganti ke app.midtrans.com untuk production --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        const payButton = document.getElementById('pay-button');
        if(payButton) {
            payButton.onclick = function () {
                // SnapToken diambil dari kolom snap_token di tabel orders
                window.snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function (result) {
                        /* Terpanggil saat pembayaran sukses */
                        window.location.href = "{{ route('user.history') }}";
                    },
                    onPending: function (result) {
                        /* Terpanggil saat user menutup popup tanpa bayar (tapi transaksi sudah dibuat) */
                        location.reload();
                    },
                    onError: function (result) {
                        /* Terpanggil saat terjadi error sistem */
                        alert("Pembayaran gagal, silahkan coba lagi.");
                    },
                    onClose: function () {
                        /* Terpanggil saat user menutup popup */
                        console.log('User closed the popup without finishing the payment');
                    }
                });
            };
        }
    </script>
@endpush