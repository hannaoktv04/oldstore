@extends('peri::layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6 mb-6">
        <div class="col-lg-3 col-sm-6">
            <div class="card h-100 cursor-pointer" onclick="window.location.href='{{ route('admin.pengajuan.status', 'submitted') }}'">
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

        <div class="col-lg-3 col-sm-6">
            <div class="card h-100 cursor-pointer" onclick="window.location.href='{{ route('admin.pengajuan.status', 'approved') }}'">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="ri-box-3-line ri-24px"></i>
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

        <div class="col-lg-3 col-sm-6">
            <div class="card h-100 cursor-pointer" onclick="window.location.href='{{ route('admin.pengajuan.status', 'delivered') }}'">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-danger rounded">
                                <i class="ri-truck-line ri-24px"></i>
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

        <div class="col-lg-3 col-sm-6">
            <div class="card h-100 cursor-pointer" onclick="window.location.href='{{ route('admin.pengajuan.status', 'received') }}'">
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
    </div>

    <div class="row g-6 mb-6">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-1">Inventaris Barang</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="inventarisFilter" data-bs-toggle="dropdown" aria-expanded="false">
                            @if($inventarisFilter == 'all') Semua Barang 
                            @elseif($inventarisFilter == 'critical') Stok Menipis 
                            @else Stok Habis 
                            @endif
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="inventarisFilter">
                            <li><a class="dropdown-item" href="?inventaris=all">Semua Barang</a></li>
                            <li><a class="dropdown-item" href="?inventaris=critical">Stok Menipis</a></li>
                            <li><a class="dropdown-item" href="?inventaris=out_of_stock">Stok Habis</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-4 mb-4">
                        <div class="d-flex align-items-center gap-3 cursor-pointer" onclick="window.location.href='?inventaris=all'">
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
                        <div class="d-flex align-items-center gap-3 cursor-pointer" onclick="window.location.href='?inventaris=critical'">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-warning rounded">
                                    <i class="ri-alert-line ri-24px"></i>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0">{{ $stokKritis }}</h5>
                                <p class="mb-0">Stok Menipis</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 cursor-pointer" onclick="window.location.href='?inventaris=out_of_stock'">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-danger rounded">
                                    <i class="ri-close-circle-line ri-24px"></i>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0">{{ $stokHabis }}</h5>
                                <p class="mb-0">Stok Habis</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="mb-3">
                            @if($inventarisFilter == 'all')
                                Produk Terbaru
                            @elseif($inventarisFilter == 'critical')
                                Produk dengan Stok Menipis
                            @else
                                Produk dengan Stok Habis
                            @endif
                        </h6>
                        <div class="d-flex flex-column gap-2">
                            @php
                                $produkToShow = [];
                                if ($inventarisFilter == 'all') {
                                    $produkToShow = $produkTerbaru->take(5);
                                } elseif ($inventarisFilter == 'critical') {
                                    $produkToShow = $produkStokKritis->take(5);
                                } else {
                                    $produkToShow = $produkStokHabis->take(5);
                                }
                            @endphp
                            
                            @forelse($produkToShow as $produk)
                            <div class="d-flex align-items-center gap-2">
                                @if($produk->photo)
                                    <img src="{{ asset('storage/' . $produk->photo->image) }}" alt="{{ $produk->nama_barang }}" width="50" height="50" class="rounded" style="object-fit: cover;">
                                @else
                                    <span class="avatar-initial rounded bg-label-secondary">{{ substr($produk->nama_barang, 0, 1) }}</span>
                                @endif
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-medium">{{ $produk->nama_barang }}</p>
                                    <small class="text-muted">{{ $produk->category->categori_name }}</small>
                                </div>
                                <span class="badge 
                                    @if($produk->stocks->qty <= 0) bg-label-danger
                                    @elseif($produk->stocks->qty <= $produk->stok_minimum) bg-label-warning
                                    @else bg-label-success @endif">
                                    {{ $produk->stocks->qty }}
                                </span>
                            </div>
                            @empty
                            <div class="text-center py-3">
                                <p class="text-muted">Tidak ada produk</p>
                            </div>
                            @endforelse
                        </div>
                        
                        @if(($inventarisFilter == 'all' && $totalBarang > 5) || 
                        ($inventarisFilter == 'critical' && $stokKritis > 5) || 
                        ($inventarisFilter == 'out_of_stock' && $stokHabis > 5))
                        <div class="text-end mt-2">
                            <a href="{{ route('admin.items.index') }}?filter={{ $inventarisFilter }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
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
    </div>

    <div class="row g-6 mb-6">
        <div class="col-lg-6" id="topProdukSection">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Top 5 Produk</h5>
                        <p class="card-subtitle mb-0">
                            Tahun {{ $tahunDipilih }} 
                            @if($bulanDipilih != 'all')
                                - Bulan {{ $bulanList[$bulanDipilih] }}
                            @endif
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="bulanProdukDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                @if($bulanDipilih == 'all') Semua Bulan @else {{ $bulanList[$bulanDipilih] }} @endif
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="bulanProdukDropdown">
                                <li><a class="dropdown-item" href="?tahun={{ $tahunDipilih }}&bulan=all&section=produk">Semua Bulan</a></li>
                                @foreach($bulanList as $key => $bulan)
                                    <li><a class="dropdown-item" href="?tahun={{ $tahunDipilih }}&bulan={{ $key }}&section=produk">{{ $bulan }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProduk->take(5) as $index => $produk)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($produk->item->photo)
                                        <img src="{{ asset('storage/' . $produk->item->photo->image) }}" alt="{{ $produk->item->nama_barang }}" width="50" height="50" class="rounded" style="object-fit: cover;">
                                    @endif
                                    {{ $produk->item->nama_barang }}
                                </td>
                                <td>{{ $produk->item->category->categori_name }}</td>
                                <td class="text-end">{{ $produk->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6" id="topWishlistSection">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Top 5 Wishlist</h5>
                        <p class="card-subtitle mb-0">
                            Tahun {{ $tahunDipilih }} 
                            @if($bulanDipilih != 'all')
                                - Bulan {{ $bulanList[$bulanDipilih] }}
                            @endif
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="bulanWishlistDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                @if($bulanDipilih == 'all') Semua Bulan @else {{ $bulanList[$bulanDipilih] }} @endif
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="bulanWishlistDropdown">
                                <li><a class="dropdown-item" href="?tahun={{ $tahunDipilih }}&bulan=all&section=wishlist">Semua Bulan</a></li>
                                @foreach($bulanList as $key => $bulan)
                                    <li><a class="dropdown-item" href="?tahun={{ $tahunDipilih }}&bulan={{ $key }}&section=wishlist">{{ $bulan }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th class="text-end">Permintaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topWishlist->take(5) as $index => $wishlist)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $wishlist->nama_barang }}
                                </td>
                                <td>{{ $wishlist->category->categori_name }}</td>
                                <td class="text-end">{{ $wishlist->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-lg-6" id="topStaffSection">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Top 5 Staff Pengiriman</h5>
                        <p class="card-subtitle mb-0">
                            Tahun {{ $tahunDipilih }} 
                            @if($bulanDipilih != 'all')
                                - Bulan {{ $bulanList[$bulanDipilih] }}
                            @endif
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="bulanStaffDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                @if($bulanDipilih == 'all') Semua Bulan @else {{ $bulanList[$bulanDipilih] }} @endif
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="bulanStaffDropdown">
                                <li><a class="dropdown-item" href="?tahun={{ $tahunDipilih }}&bulan=all&section=staff">Semua Bulan</a></li>
                                @foreach($bulanList as $key => $bulan)
                                    <li><a class="dropdown-item" href="?tahun={{ $tahunDipilih }}&bulan={{ $key }}&section=staff">{{ $bulan }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Staff</th>
                                <th class="text-end">Jumlah Pengiriman</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topStaffPengiriman->take(5) as $index => $staff)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $staff->staff->nama ?? '-' }}
                                </td>
                                <td class="text-end">{{ $staff->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6" id="topUserSection">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Top 5 User Paling Banyak Meminta</h5>
                        <p class="card-subtitle mb-0">
                            Tahun {{ $tahunDipilih }} 
                            @if($bulanDipilih != 'all')
                                - Bulan {{ $bulanList[$bulanDipilih] }}
                            @endif
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="bulanUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                @if($bulanDipilih == 'all') Semua Bulan @else {{ $bulanList[$bulanDipilih] }} @endif
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="bulanUserDropdown">
                                <li><a class="dropdown-item" href="?tahun={{ $tahunDipilih }}&bulan=all&section=user">Semua Bulan</a></li>
                                @foreach($bulanList as $key => $bulan)
                                    <li><a class="dropdown-item" href="?tahun={{ $tahunDipilih }}&bulan={{ $key }}&section=user">{{ $bulan }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama User</th>
                                <th class="text-end">Jumlah Permintaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topUserRequest->take(5) as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $user->user->nama }}
                                </td>
                                <td class="text-end">{{ $user->total }}</td>
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

@push('styles')
<style>
    .table img, .avatar-initial {
        width: 30px;
        height: 30px;
        object-fit: cover;
    }
    .avatar-initial {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script>
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

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const section = urlParams.get('section');
        
        if (section && document.querySelector(`#${section}Section`)) {
            document.querySelector(`#${section}Section`).scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
</script>
@endpush