@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="row g-3">
                @php
                    $cards = [
                        ['route' => 'submitted', 'icon' => 'send-check-fill', 'color' => 'success', 'title' => 'Pengajuan Baru', 'count' => $pengajuanBaru],
                        ['route' => 'approved', 'icon' => 'truck', 'color' => 'warning', 'title' => 'Perlu Dikirim', 'count' => $perluDikirim],
                        ['route' => 'delivered', 'icon' => 'box-seam', 'color' => 'info', 'title' => 'Konfirmasi Pengiriman', 'count' => $pengajuanSelesai],
                        ['route' => 'rejected', 'icon' => 'x-circle-fill', 'color' => 'danger', 'title' => 'Ditolak', 'count' => $pembatalan],
                    ];
                @endphp
                <div class="d-flex gap-3 no-horizontal-scroll pb-2 flex-nowrap">
                    @foreach ($cards as $card)
                        <a href="{{ route('admin.pengajuan.status', $card['route']) }}"
                           class="card text-decoration-none text-dark shadow-sm hover-3d border-0 bg-light mt-2"
                           style="min-width: 230px; flex: 0 0 auto;">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                <div class="mb-2 text-{{ $card['color'] }}">
                                    <i class="bi bi-{{ $card['icon'] }} display-6"></i>
                                </div>
                                <h6 class="text-muted mb-1">{{ $card['title'] }}</h6>
                                <h2 class="fw-bold">{{ $card['count'] }}</h2>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
                <h5 class="fw-bold">Grafik Barang Keluar</h5>
                <form method="GET">
                    <div class="d-flex gap-2">
                        <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach(range(1, 12) as $b)
                                <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}"
                                    {{ $bulanDipilih == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                        <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                            @for ($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}" {{ $tahunDipilih == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>
            <canvas id="combinedChart" height="100"></canvas>

            <div class="mt-5">
                <h5 class="fw-bold mb-3">Produk Paling Banyak Diminta - {{ $tahunDipilih }}</h5>
                <div class="d-flex gap-3 overflow-auto pb-3 flex-nowrap no-horizontal-scroll">
                    @forelse ($topProduk->take(12) as $produk)
                        <div class="card border-0 bg-white shadow-sm mt-2 hover-3d transition-hover" style="width: 230px; flex: 0 0 auto;">
                            <div class="card-body d-flex flex-column align-items-center text-center p-3">
                                <div class="rounded overflow-hidden mb-2" style="height: 200px; width: 100%; background-color: #f9f9f9;">
                                    <img src="{{ asset('storage/' . ($produk->item->photo->image ?? 'placeholder.jpg')) }}"
                                        alt="{{ $produk->item->nama_barang }}"
                                        class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="w-100 mb-2" style="min-height: 50px;">
                                    <h6 class="text-truncate mb-0" title="{{ $produk->item->nama_barang }}">
                                        {{ $produk->item->nama_barang }}
                                    </h6>
                                    <small class="text-muted">{{ $produk->item->category->categori_name ?? 'Tanpa Kategori' }}</small>
                                </div>
                                <div class="mt-auto">
                                    <span class="badge bg-success">Total: {{ $produk->total }} {{ $produk->item->satuan }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada data produk keluar.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-5">
                <h5 class="fw-bold mb-3">Wishlist Terbanyak - {{ $tahunDipilih }}</h5>
                <div class="d-flex gap-3 no-horizontal-scroll pb-5 flex-nowrap">
                    @forelse ($topWishlist as $wishlist)
                        <div class="card text-decoration-none text-dark shadow-sm hover-3d border-0 bg-white mt-2 p-2 ml-4"
                            style="min-width: 230px; flex: 0 0 auto;">
                            <h6 class="mb-1 text-truncate">{{ $wishlist->nama_barang }}</h6>
                            <small class="text-muted mb-2">{{ $wishlist->category->categori_name ?? 'Tanpa Kategori' }}</small>
                            <div class="mt-auto mb-2">
                                <span class="badge bg-success">Total :  {{ $wishlist->total }}  usulan</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada wishlist yang diajukan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('combinedChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']) !!},
            datasets: [
                {
                    label: 'Barang Keluar Bulanan',
                    data: {!! json_encode(array_replace(array_fill(0, 12, 0), $barangKeluarPerBulan)) !!},
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3
                },
                {
                    label: 'Rata-rata Harian per Bulan',
                    data: {!! json_encode($rataHarianPerBulan ?? array_fill(0, 12, 0)) !!},
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    ticks: {
                        autoSkip: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
});
</script>
@endsection
