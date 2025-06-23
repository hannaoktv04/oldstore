@php
    $produkOpen = request()->routeIs('admin.addItem') || request()->routeIs('admin.items');
    $pengajuanOpen = request()->routeIs('admin.pengajuan.status');
    $stokOpen = request()->routeIs('admin.stok.koreksi') || request()->routeIs('admin.stok.opname');
@endphp

<div class="sidebar-admin">
    <h6 class="text-uppercase fw-bold mb-3">Dashboard</h6>

    {{-- Produk --}}
    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a id="toggleProduk" class="nav-link d-flex justify-content-between text-dark" data-bs-toggle="collapse"
               href="#collapseProduk" role="button" aria-expanded="{{ $produkOpen ? 'true' : 'false' }}"
               aria-controls="collapseProduk">
                <span><i class="bi bi-box-seam me-2"></i> Produk</span>
                <i class="bi bi-chevron-down rotate-icon {{ $produkOpen ? 'rotate' : '' }}"></i>
            </a>
            <div class="collapse ms-3 mt-2 {{ $produkOpen ? 'show' : '' }}" id="collapseProduk">
                <a href="{{ route('admin.addItem') }}"
                   class="nav-link d-flex py-1 {{ request()->routeIs('admin.addItem') ? 'active' : 'text-dark' }}">
                    Tambah Produk
                </a>
                <a href="{{ route('admin.items') }}"
                   class="nav-link d-flex py-1 {{ request()->routeIs('admin.items') ? 'active' : 'text-dark' }}">
                    Daftar Produk
                </a>
            </div>
        </li>
    </ul>

    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a id="togglePengajuan" class="nav-link d-flex justify-content-between text-dark" data-bs-toggle="collapse"
               href="#collapsePengajuan" role="button" aria-expanded="{{ $pengajuanOpen ? 'true' : 'false' }}"
               aria-controls="collapsePengajuan">
                <span><i class="bi bi-clipboard me-2"></i> Riwayat Pengajuan</span>
                <i class="bi bi-chevron-down rotate-icon {{ $pengajuanOpen ? 'rotate' : '' }}"></i>
            </a>
            <div class="collapse ms-3 mt-2 {{ $pengajuanOpen ? 'show' : '' }}" id="collapsePengajuan">
                <a href="{{ route('admin.pengajuan.status', 'submitted') }}"
                   class="nav-link d-flex py-1 {{ request()->fullUrlIs('*submitted*') ? 'active' : 'text-dark' }}">
                    Pengajuan Baru
                </a>
                <a href="{{ route('admin.pengajuan.status', 'approved') }}"
                   class="nav-link d-flex py-1 {{ request()->fullUrlIs('*approved*') ? 'active' : 'text-dark' }}">
                    Perlu Dikirim
                </a>
                <a href="{{ route('admin.pengajuan.status', 'delivered') }}"
                   class="nav-link d-flex py-1 {{ request()->fullUrlIs('*delivered*') ? 'active' : 'text-dark' }}">
                    Sedang Dikirim
                </a>
                <a href="{{ route('admin.pengajuan.status', 'received') }}"
                   class="nav-link d-flex py-1 {{ request()->fullUrlIs('*received*') ? 'active' : 'text-dark' }}">
                    Selesai
                </a>
            </div>
        </li>
    </ul>

    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a id="toggleStok" class="nav-link d-flex justify-content-between text-dark" data-bs-toggle="collapse"
               href="#collapseStok" role="button" aria-expanded="{{ $stokOpen ? 'true' : 'false' }}"
               aria-controls="collapseStok">
                <span><i class="bi bi-boxes me-2"></i> Stok</span>
                <i class="bi bi-chevron-down rotate-icon {{ $stokOpen ? 'rotate' : '' }}"></i>
            </a>
            <div class="collapse ms-3 mt-2 {{ $stokOpen ? 'show' : '' }}" id="collapseStok">
                <a href="{{ route('admin.stok.koreksi') }}"
                   class="nav-link d-flex py-1 {{ request()->routeIs('admin.stok.koreksi') ? 'active' : 'text-dark' }}">
                    Koreksi Stok
                </a>
                <a href="#"
                   class="nav-link d-flex py-1 text-dark">
                    Stok Opname
                </a>
            </div>
        </li>
    </ul>

    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a href="{{ route('admin.categories.index') }}"
               class="nav-link d-flex align-items-center {{ request()->routeIs('admin.categories.*') ? 'active' : 'text-dark' }}">
                <span><i class="bi bi-folder2-open me-2"></i> Kategori</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.wishlist.index') }}"
               class="nav-link d-flex align-items-center {{ request()->routeIs('admin.wishlist.index') ? 'active' : 'text-dark' }}">
                <span><i class="bi bi-journal-check me-2"></i> Wishlist</span>
            </a>
        </li>
    </ul>
</div>
