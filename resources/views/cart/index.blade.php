@extends('layouts.app')

@section('content')
<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h4 class="fw-bold mb-4">Keranjang Belanja</h4>

    @forelse($carts as $cart)
    <div class="card border-0 mb-3 p-3 shadow-sm">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img src="{{ asset('storage/' . ($item->photo?->image ?? 'placeholder.jpg')) }}" alt="gambar" width="80" height="80" class="rounded me-3" style="object-fit: cover;">
                <div>
                    <div class="text-muted small">{{ $cart->item->category_id ?? 'Kategori Tidak Diketahui' }}</div>
                    <div class="fw-semibold">{{ $cart->item->nama_barang }}</div>
                    <div class="text-muted small">Produk tersedia : {{ $cart->item->stok_minimum }} {{ $cart->item->satuan }}</div>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <form method="POST" action="{{ route('cart.destroy', $cart->id) }}" class="me-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger p-0">
                        <i class="bi bi-trash" style="font-size: 1.2rem;"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('cart.update', $cart->id) }}" class="d-flex align-items-center">
                    @csrf
                    @method('PUT')
                    <button type="submit" name="action" value="decrease" class="btn btn-outline-secondary btn-sm me-2">-</button>
                    <span class="mx-1">{{ $cart->qty }}</span>
                    <button type="submit" name="action" value="increase" class="btn btn-outline-secondary btn-sm ms-2">+</button>
                </form>
            </div>
        </div>
    </div>
    @empty
        <p>Keranjang kamu kosong.</p>
    @endforelse

    @if($carts->count())
        <div class="text-end mt-4">
            <form method="POST" action="{{ route('cart.checkout') }}">
                @csrf
                <button class="btn btn-success px-4 py-2">Ajukan Permintaan</button>
            </form>
        </div>
    @endif

    <script>
        setTimeout(function () {
            var alert = document.querySelector('.alert');
            if (alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 1000);
    </script>

</div>
@endsection
