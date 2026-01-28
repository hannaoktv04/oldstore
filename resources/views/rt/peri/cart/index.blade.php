@extends('peri::layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-semibold mb-0">Keranjang</h3>
        @if($jumlahKeranjang > 0)
        <a href="{{ url('/kategori') }}" class="btn btn-outline-success btn-sm border-0">
            ← Kembali ke Kategori
        </a>
        @endif
    </div>

    @if($jumlahKeranjang > 0)
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 mb-3 p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check">
                        <input class="form-check-input me-2 border-success"
                               style="box-shadow: none; width: 1.3em; height: 1.3em;"
                               type="checkbox"
                               id="flexCheckDefault"
                               {{ $opnameAktif ? 'disabled' : '' }}>
                        <label class="form-check-label fw-semibold" for="flexCheckDefault">
                            Pilih Semua <small class="text-secondary">({{ $jumlahKeranjang }})</small>
                        </label>
                    </div>
                    <button id="btnHapusTerpilih" class="btn btn-success btn-sm d-none px-3 py-1"
                            data-bs-toggle="modal" data-bs-target="#konfirmasiHapusModal"
                            {{ $opnameAktif ? 'disabled' : '' }}>
                        Hapus
                    </button>
                </div>
            </div>
    @endif

    @forelse($carts as $cart)
    @php
        $item = $cart->item;
        $stokHabis = ($item->stok ?? 0) <= 0;
        $nonaktif = $stokHabis || $opnameAktif;
        $buttonStyle = $nonaktif ? 'btn-outline-secondary' : 'btn-outline-success';
        $borderColor = $nonaktif ? '#6c757d' : '#a19d53ff';
    @endphp

    <div class="card border-0 mb-3 p-3 shadow-sm position-relative {{ $nonaktif ? 'bg-light grayscale-card' : '' }}"
        data-harga="{{ $item->harga }}"
        data-qty="{{ $cart->qty }}">
        @if($stokHabis)
            <span class="badge bg-secondary position-absolute" style="top: 10px; left: 10px;">HABIS</span>
        @elseif($opnameAktif)
            <span class="badge bg-warning text-dark position-absolute" style="top: 10px; left: 10px;">OPNAME</span>
        @else
            <input class="form-check-input position-absolute item-checkbox border-success"
                   style="top: 10px; left: 10px; width: 1.3em; height: 1.3em;"
                   type="checkbox"
                   value="{{ $cart->id }}">
        @endif

        <div class="d-flex align-items-center justify-content-between ps-7 {{ $nonaktif ? 'text-muted' : '' }}">
            <a href="{{ $nonaktif ? '#' : route('produk.show', ['id' => $item->id]) }}"
               class="text-decoration-none {{ $nonaktif ? 'pointer-events-none' : '' }}">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('storage/' . $item->photo_url) }}" alt="gambar"
                         width="80" height="80" class="rounded me-3" style="object-fit: cover;">
                    <div>
                        <div class="text-muted small">{{ $item->category->categori_name ?? '-' }}</div>
                        <div class="fw-semibold text-dark">{{ $item->nama_barang }}</div>
                        @if(!empty($cart->size))
                            <div class="small text-muted">
                                Ukuran: <span class="fw-bold text-dark">{{ $cart->size }}</span>
                            </div>
                        @endif
                        <div class="text-success small fw-semibold">
                            Harga: Rp {{ number_format($item->harga ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </a>

            <div class="d-flex align-items-center">
                <form method="POST" action="{{ route('cart.destroy', $cart->id) }}" class="me-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn text-secondary"
                            {{ $opnameAktif ? 'disabled' : '' }}>
                        <i class="bi bi-trash" style="font-size: 1.2rem;"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('cart.update', $cart->id) }}" class="d-flex align-items-center">
                    @csrf
                    {{-- JANGAN pakai @method('PUT') karena route mendukung POST --}}
                    
                    <div class="input-group {{ $buttonStyle }} flex-nowrap w-100"
                        style="border: 1px solid {{ $borderColor }}; border-radius: 50px; overflow: hidden;">
                        
                        {{-- Tombol Kurang --}}
                        <button type="submit" name="action" value="decrease"
                                class="btn {{ $buttonStyle }} px-2 py-1 border-0"
                                {{ $nonaktif ? 'disabled' : '' }}>−</button>
                        
                        {{-- Input Angka: Pastikan name="qty" --}}
                        <input type="number" name="qty" value="{{ $cart->qty }}"
                            min="1" max="{{ $item->stok ?? 0 }}"
                            class="form-control text-center border-0 bg-white"
                            style="width: 43px; height: 32px; padding: 0 0.1rem;"
                            {{ $nonaktif ? 'disabled' : '' }}>
                        
                        {{-- Tombol Tambah --}}
                        <button type="submit" name="action" value="increase"
                                class="btn {{ $buttonStyle }} px-2 py-1 border-0"
                                {{ $nonaktif ? 'disabled' : '' }}>+</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 mb-3 p-3 shadow-sm bg-white text-center justify-content-center align-items-center">
        <img src="{{ asset('assets/img/cart.png') }}" alt="Cart Image" style="width: 150px; height: auto;">
        <h3>Wah, keranjang kamu kosong</h3>
        <p>Yuk, isi dengan barang-barang kebutuhanmu!</p>
        <a class="btn btn-success" href="{{ url('/home') }}">Cari Barang</a>
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

                <!-- Total Harga -->
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Harga</span>
                    <span id="totalHarga" class="fw-semibold">
                        Rp 0
                    </span>
                </div>


                <form method="GET" action="{{ route('cart.checkoutPage') }}" id="checkoutForm">
                    @csrf
                    <button type="submit" class="btn btn-success w-100"
                            {{ $opnameAktif ? 'disabled' : '' }}>
                        Beli Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="modal fade" id="konfirmasiHapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('cart.bulkDelete') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="cart_ids" id="selectedCartIds">
                <div class="modal-content align-items-center">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-semibold">Hapus <span class="jumlah-terpilih">0</span> Produk?</h5>
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

@push('scripts')
<script src="{{ asset('assets/js/peri/cart.js') }}"></script>
@endpush
