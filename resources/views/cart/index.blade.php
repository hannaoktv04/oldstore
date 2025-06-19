@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="fw-semibold mb-4">Keranjang</h3>

    @if($jumlahKeranjang > 0)
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 mb-3 p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check">
                        <input class="form-check-input me-2 border-success"
                            style="box-shadow: none; width: 1.3em; height: 1.3em;"
                            type="checkbox"
                            value=""
                            id="flexCheckDefault">
                        <label class="form-check-label fw-semibold" for="flexCheckDefault">
                            Pilih Semua <small class="text-secondary">({{ $jumlahKeranjang }})</small>
                        </label>
                    </div>
                    <button id="btnHapusTerpilih" class="btn btn-success btn-sm d-none px-3 py-1"
                        data-bs-toggle="modal" data-bs-target="#konfirmasiHapusModal">
                        Hapus
                    </button>
                </div>
            </div>
    @endif

    @forelse($carts as $cart)
    @php
        $item = $cart->item;
        $stokHabis = $item->stok_minimum <= 0;
        $buttonStyle = $stokHabis ? 'btn-outline-secondary' : 'btn-outline-success';
        $borderColor = $stokHabis ? '#6c757d' : '#198754';
    @endphp

    <div class="card border-0 mb-3 p-3 shadow-sm position-relative {{ $stokHabis ? 'bg-light' : '' }}">
        @if($stokHabis)
            <span class="badge bg-secondary position-absolute" style="top: 10px; left: 10px; z-index: 1;">HABIS</span>
        @else
            <input class="form-check-input position-absolute item-checkbox border-success"
                style="top: 10px; left: 10px; box-shadow: none; width: 1.3em; height: 1.3em;"
                name="cart_ids[]"
                type="checkbox"
                value="{{ $cart->id }}">
        @endif
        <div class="d-flex align-items-center justify-content-between ps-7 {{ $stokHabis ? 'text-muted opacity-50' : '' }}">
            <a href="{{ route('produk.show', ['id' => $item->id]) }}"
            class="text-decoration-none {{ $stokHabis ? 'pointer-events-none' : '' }}">
                <div class="d-flex align-items-center">
                    <img src="{{ $item->photo_url }}" alt="gambar" width="80" height="80"
                        class="rounded me-3" style="object-fit: cover;">
                    <div>
                        <div class="text-muted small">{{ $item->category->categori_name ?? 'Kategori Tidak Diketahui' }}</div>
                        <div class="fw-semibold text-dark">{{ $item->nama_barang }}</div>
                        <div class="text-muted small">
                            Stok tersedia: {{ $item->stok_minimum }} {{ $item->satuan }}
                        </div>
                    </div>
                </div>
            </a>
            <div class="d-flex align-items-center">
                <form method="POST" action="{{ route('cart.destroy', $cart->id) }}" class="me-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn text-secondary">
                        <i class="bi bi-trash" style="font-size: 1.2rem;"></i>
                    </button>
                </form>
                <form method="POST" action="{{ route('cart.update', $cart->id) }}" class="d-flex align-items-center">
                    @csrf
                    @method('PUT')
                    <div class="input-group {{ $buttonStyle }} flex-nowrap flex-md-nowrap w-100"
                        style=" border: 1px solid {{ $borderColor }}; border-radius: 50px; overflow: hidden; opacity: {{ $stokHabis ? '0.5' : '1' }};">
                        <button type="submit" name="action" value="decrease"
                            class="btn {{ $buttonStyle }} px-2 py-1 border-0 qty-btn"
                            style="min-width: 6px;"
                            {{ $stokHabis ? 'disabled' : '' }}>−</button>

                        <input type="number" name="manual_qty" value="{{ $cart->qty }}"
                            min="1" max="{{ $item->stok_minimum }}"
                            class="form-control text-center border-0"
                            style="min-width: 20px; height: 32px; padding: 0 0.1rem; width: 43px;"
                            {{ $stokHabis ? 'disabled' : '' }}>

                        <button type="submit" name="action" value="increase"
                            class="btn {{ $buttonStyle }} px-2 py-1 border-0 qty-btn"
                            style="min-width: 6px;"
                            {{ $stokHabis ? 'disabled' : '' }}>+</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 mb-3 p-3 shadow-sm bg-white">
        <div class="align-items-center text-center">
            <img src="{{ asset('assets/img/cart.png') }}" alt="Cart Image">
            <h3>Wah, keranjang kamu kosong <br></h3>
            <p>Yuk, isi dengan barang-barang kebutuhanmu!</p>
            <a class="btn btn-success" href="{{ url('/home') }}" role="button">Cari Barang</a>
        </div>
    </div>
    @endforelse

    @if($carts->count())
        </div> 
        <div class="col-lg-4">
            <div class="bg-white rounded shadow-sm p-3">
                <h5 class="fw-semibold mb-3">Ringkasan Permintaan</h5>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Items</span>
                    <span class="jumlah-terpilih">0</span>
                </div>
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#tanggalModal">
                    Ajukan Permintaan
                </button>
            </div>
        </div>
    </div>
    @endif

    <div class="modal fade" id="tanggalModal" tabindex="-1" aria-labelledby="tanggalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('cart.checkout') }}" id="checkoutForm">
                @csrf
                <input type="hidden" name="cart_ids[]" id="hiddenCartIds">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tanggalModalLabel">Pilih Tanggal Pengambilan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <label for="tanggal_pengambilan" class="form-label">Tanggal Pengambilan</label>
                        <input type="date" name="tanggal_pengambilan" class="form-control" id="tanggal_pengambilan" min="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Ajukan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="konfirmasiHapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('cart.bulkDelete') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="cart_ids" id="selectedCartIds">
                <div class="modal-content align-items-center">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-semibold" id="hapusModalLabel">Hapus <span class="jumlah-terpilih">0</span> Produk?</h5>
                    </div>
                    <div class="modal-body text-secondary">
                        Item yang kamu pilih akan dihapus dari Keranjang.
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-success px-5 py-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-5 py-2">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
