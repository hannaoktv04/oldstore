@extends('peri::layouts.app')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-4">Checkout Pesanan</h3>

    <form method="POST" action="{{ route('cart.checkout') }}" id="checkoutForm">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Alamat Pengiriman</h5>
                    
                    <div class="row">
                        {{-- PROVINSI --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Provinsi</label>
                            <select id="province" name="province_code" class="form-select" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>

                        {{-- KOTA --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kota / Kabupaten</label>
                            <select id="destination_city" name="city_code" class="form-select" required>
                                <option value="">Pilih Kota</option>
                            </select>
                        </div>

                        {{-- KECAMATAN --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kecamatan</label>
                            <select id="district" name="district_code" class="form-select" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>

                        {{-- DESA --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Desa / Kelurahan</label>
                            <select id="destination_village" name="village_code" class="form-select" required>
                                <option value="">Pilih Desa</option>
                            </select>
                        </div>
                    </div>

                    {{-- ALAMAT LENGKAP --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Lengkap</label>
                        <textarea name="full_address" class="form-control" rows="3" 
                                  placeholder="Nama jalan, nomor rumah, RT/RW, dsb." required></textarea>
                    </div>

                    <div class="row">
                        {{-- KODE POS --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kode Pos</label>
                            <input type="text" name="postal_code" class="form-control" placeholder="Contoh: 40123" required>
                        </div>

                        {{-- BERAT --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Berat Total (Gram)</label>
                            <input type="number" id="weight" name="weight" class="form-control" value="1000" min="1" readonly>
                            <small class="text-muted text-italic">*Berat otomatis dihitung dari sistem</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3"><i class="bi bi-truck text-primary me-2"></i>Metode Pengiriman</h5>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Pilih Ekspedisi</label>
                            <select id="expedition" name="courier" class="form-select" required>
                                <option value="">Pilih Ekspedisi</option>
                                <option value="jne">JNE (Reguler)</option>
                                <option value="sicepat">SiCepat (Reguler)</option>
                                <option value="tiki">TIKI (Reguler)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="button" id="btnHitung" class="btn btn-outline-primary w-100">
                                Hitung Ongkir
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-3">Ringkasan Belanja</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal Produk</span>
                        <span class="fw-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Ongkos Kirim</span>
                        <span id="ongkirValue" class="fw-semibold">Rp 0</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total Bayar</span>
                        <span id="totalValue" class="fw-bold fs-5 text-primary">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Hidden Inputs untuk dikirim ke Controller --}}
                    <input type="hidden" name="ongkir" id="ongkirInput" value="0">
                    {{-- Mengambil ID keranjang dari variabel $carts --}}
                    <input type="hidden" name="cart_ids" value="{{ $carts->pluck('id')->implode(',') }}">

                    <button type="submit" id="btnCheckout" class="btn btn-success btn-lg w-100 fw-bold" disabled>
                        Buat Pesanan
                    </button>
                    
                    <p class="text-muted small text-center mt-3">
                        *Klik "Hitung Ongkir" terlebih dahulu untuk mengaktifkan tombol beli.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const provinceEl   = document.getElementById('province');
    const cityEl       = document.getElementById('destination_city');
    const districtEl   = document.getElementById('district');
    const villageEl    = document.getElementById('destination_village');
    const expeditionEl = document.getElementById('expedition');
    const weightEl     = document.getElementById('weight');
    const btnHitung    = document.getElementById('btnHitung');
    const btnCheckout  = document.getElementById('btnCheckout');
    const ongkirValue  = document.getElementById('ongkirValue');
    const ongkirInput  = document.getElementById('ongkirInput');
    const totalValue   = document.getElementById('totalValue');

    const subtotal = {{ $subtotal }};

    function resetHasilOngkir() {
        ongkirValue.textContent = 'Rp 0';
        totalValue.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        ongkirInput.value = 0;
        btnCheckout.disabled = true;
    }

    // LOAD DATA WILAYAH
    fetch('/cart/api/regions/provinces')
        .then(res => res.json())
        .then(res => {
            res.data.forEach(p => provinceEl.innerHTML += `<option value="${p.code}">${p.name}</option>`);
        });

    provinceEl.addEventListener('change', () => {
        cityEl.innerHTML = '<option value="">Pilih kota</option>';
        resetHasilOngkir();
        if (!provinceEl.value) return;
        fetch(`/cart/api/regions/cities/${provinceEl.value}`).then(res => res.json()).then(res => {
            res.data.forEach(c => cityEl.innerHTML += `<option value="${c.code}">${c.name}</option>`);
        });
    });

    cityEl.addEventListener('change', () => {
        districtEl.innerHTML = '<option value="">Pilih kecamatan</option>';
        resetHasilOngkir();
        if (!cityEl.value) return;
        fetch(`/cart/api/regions/districts/${cityEl.value}`).then(res => res.json()).then(res => {
            res.data.forEach(d => districtEl.innerHTML += `<option value="${d.code}">${d.name}</option>`);
        });
    });

    districtEl.addEventListener('change', () => {
        villageEl.innerHTML = '<option value="">Pilih desa</option>';
        resetHasilOngkir();
        if (!districtEl.value) return;
        fetch(`/cart/api/regions/villages/${districtEl.value}`).then(res => res.json()).then(res => {
            res.data.forEach(v => villageEl.innerHTML += `<option value="${v.code}">${v.name}</option>`);
        });
    });

    // HITUNG ONGKIR
    btnHitung.addEventListener('click', async () => {
        const courier = expeditionEl.value;
        const village = villageEl.value;
        const weight  = weightEl.value;

        if (!village || !courier) {
            alert('Harap lengkapi alamat dan pilih ekspedisi.');
            return;
        }

        btnHitung.disabled = true;
        btnHitung.innerText = 'Loading...';

        try {
            const res = await fetch("{{ route('cart.calcOngkir') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    destination_village_code: village,
                    weight: weight,
                    courier: courier
                })
            });

            const response = await res.json();
            btnHitung.disabled = false;
            btnHitung.innerText = 'Hitung Ongkir';

            if (response.is_success) {
                const price = Number(response.data.price);
                ongkirValue.textContent = 'Rp ' + price.toLocaleString('id-ID');
                ongkirInput.value = price;
                totalValue.textContent = 'Rp ' + (subtotal + price).toLocaleString('id-ID');
                btnCheckout.disabled = false;
            } else {
                alert(response.message);
                resetHasilOngkir();
            }
        } catch (err) {
            btnHitung.disabled = false;
            btnHitung.innerText = 'Hitung Ongkir';
            alert('Terjadi kesalahan koneksi.');
        }
    });
});
</script>
@endpush