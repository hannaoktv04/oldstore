@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6 mb-6">
        <!-- Pengajuan Baru -->
        <div class="col-lg-3 col-sm-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="ri-file-text-line ri-24px"></i>
                            </div>
                        </div>
                        <div class="card-info">
                            <h5 class="mb-0">{{ $pengajuanBaru }}</h5>
                            <p class="mb-0">Pengajuan Baru</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perlu Dikirim -->
        <div class="col-lg-3 col-sm-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="ri-truck-line ri-24px"></i>
                            </div>
                        </div>
                        <div class="card-info">
                            <h5 class="mb-0">{{ $perluDikirim }}</h5>
                            <p class="mb-0">Perlu Dikirim</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengajuan Selesai -->
        <div class="col-lg-3 col-sm-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="ri-checkbox-circle-line ri-24px"></i>
                            </div>
                        </div>
                        <div class="card-info">
                            <h5 class="mb-0">{{ $pengajuanSelesai }}</h5>
                            <p class="mb-0">Pengajuan Selesai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pembatalan -->
        <div class="col-lg-3 col-sm-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-danger rounded">
                                <i class="ri-close-circle-line ri-24px"></i>
                            </div>
                        </div>
                        <div class="card-info">
                            <h5 class="mb-0">{{ $sedangDikirim }}</h5>
                            <p class="mb-0">Sedang Dikirim</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6">
        <!-- Total Barang & Stok Kritis -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Inventaris Barang</h5>
                    <div class="d-flex align-items-center card-subtitle">
                        <div class="me-2">Total {{ $totalBarang }} Barang</div>
                        @if($stokKritis > 0)
                        <div class="d-flex align-items-center text-danger">
                            <p class="mb-0 fw-medium">{{ $stokKritis }} Stok Kritis</p>
                            <i class="ri-alert-line ri-20px"></i>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-info rounded">
                                    <i class="ri-box-3-line ri-24px"></i>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0">{{ $totalBarang }}</h5>
                                <p class="mb-0">Total Barang</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-warning rounded">
                                    <i class="ri-alert-line ri-24px"></i>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0">{{ $stokKritis }}</h5>
                                <p class="mb-0">Stok Kritis</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Barang Keluar -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Barang Keluar</h5>
                        <p class="card-subtitle mb-0">Tahun {{ $tahunDipilih }}</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="tahunDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Tahun {{ $tahunDipilih }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="tahunDropdown">
                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                <li><a class="dropdown-item" href="?tahun={{ $year }}&bulan={{ $bulanDipilih }}">{{ $year }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="grafikBulanan"></div>
                </div>
            </div>
        </div>

        <!-- Top Produk -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Top 10 Produk</h5>
                    <p class="card-subtitle mb-0">Tahun {{ $tahunDipilih }}</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProduk as $produk)
                            <tr>
                                <td>
                                    @if($produk->item->photo)
                                        <img src="{{ asset('storage/' . $produk->item->photo) }}" alt="{{ $produk->item->name }}" width="30" class="rounded me-2">
                                    @endif
                                    {{ $produk->item->name }}
                                </td>
                                <td>{{ $produk->item->category->name }}</td>
                                <td class="text-end">{{ $produk->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Wishlist -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Top 10 Wishlist</h5>
                    <p class="card-subtitle mb-0">Tahun {{ $tahunDipilih }}</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th class="text-end">Permintaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topWishlist as $wishlist)
                            <tr>
                                <td>{{ $wishlist->nama_barang }}</td>
                                <td>{{ $wishlist->category->name }}</td>
                                <td class="text-end">{{ $wishlist->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Grafik Bulanan
    var grafikBulananOptions = {
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '55%',
            }
        },
        dataLabels: {
            enabled: false
        },
        colors: ['#7367F0'],
        series: [{
            name: 'Barang Keluar',
            data: @json($grafikBulanan)
        }],
        xaxis: {
            categories: @json(array_values($bulanList)),
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " barang"
                }
            }
        }
    };

    var grafikBulanan = new ApexCharts(
        document.querySelector("#grafikBulanan"),
        grafikBulananOptions
    );
    grafikBulanan.render();
</script>
@endpush