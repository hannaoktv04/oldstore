@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h4 class="fw-bold mb-4">Keranjang Belanja</h4>

    @forelse($carts as $cart)
    <div class="card mb-3 p-3 shadow-sm">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Gambar -->
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/products/' . $cart->item->image) }}" alt="gambar" width="80" height="80" class="rounded me-3" style="object-fit: cover;">
                <div>
                    <div class="text-muted small">{{ $cart->item->kategori ?? 'Kategori Tidak Diketahui' }}</div>
                    <div class="fw-semibold">{{ $cart->item->nama_barang }}</div>
                    <div class="fst-italic text-muted">{{ $cart->item->warna ?? '-' }}</div>
                </div>
            </div>

            <!-- Aksi -->
            <div class="d-flex align-items-center">
                <!-- Tombol Hapus -->
                <form method="POST" action="{{ route('cart.destroy', $cart->id) }}" class="me-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger p-0">
                        <i class="bi bi-trash" style="font-size: 1.2rem;"></i>
                    </button>
                </form>

                <!-- Kuantitas -->
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

</div>
@endsection
