@php
    $produkOpen =
        request()->routeIs('admin.item.create') ||
        request()->routeIs('admin.items') ||
        request()->routeIs('admin.stok.koreksi') ||
        request()->routeIs('admin.categories.index') ||
        request()->routeIs('admin.satuan.index');
    $pengajuanOpen = request()->routeIs('admin.pengajuan.status');
    $prefix = $prefix ?? 'default';
@endphp

<div class="sidebar-admin">
    {{-- Produk --}}
    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between text-dark" data-bs-toggle="collapse"
                href="#{{ $prefix }}-collapseProduk" role="button"
                aria-expanded="{{ $produkOpen ? 'true' : 'false' }}" aria-controls="{{ $prefix }}-collapseProduk">
                <span><i class="bi bi-box-seam me-2"></i> Produk</span>
                <i class="bi bi-chevron-down rotate-icon {{ $produkOpen ? 'rotate' : '' }}"></i>

            </a>
            <div class="collapse ms-3 mt-2 {{ $produkOpen ? 'show' : '' }}" id="{{ $prefix }}-collapseProduk">
                <a href="{{ route('admin.item.create') }}"
                    class="nav-link d-flex py-1 {{ request()->routeIs('admin.item.create') ? 'active' : 'text-dark' }}">
                    Tambah Produk
                </a>
                <a href="{{ route('admin.items') }}"
                    class="nav-link d-flex py-1 {{ request()->routeIs('admin.items') ? 'active' : 'text-dark' }}">
                    Daftar Produk
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link d-flex py-1 {{ request()->routeIs('admin.categories.*') ? 'active' : 'text-dark' }}">
                    Kategori
                </a>
                <a href="{{ route('admin.satuan.index') }}"
                    class="nav-link d-flex py-1 {{ request()->routeIs('admin.satuan.*') ? 'active' : 'text-dark' }}">
                    Satuan
                </a>
                <a href="{{ route('admin.stok.koreksi') }}"
                    class="nav-link d-flex py-1 {{ request()->routeIs('admin.stok.koreksi') ? 'active' : 'text-dark' }}">
                    Koreksi Stok
                </a>

            </div>
        </li>
    </ul>

    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between text-dark" data-bs-toggle="collapse"
                href="#{{ $prefix }}-collapsePengajuan" role="button"
                aria-expanded="{{ $pengajuanOpen ? 'true' : 'false' }}"
                aria-controls="{{ $prefix }}-collapsePengajuan">
                <span><i class="bi bi-clipboard me-2"></i> Riwayat Pengajuan</span>
                <i class="bi bi-chevron-down rotate-icon {{ $pengajuanOpen ? 'rotate' : '' }}"></i>
            </a>
            <div class="collapse ms-3 mt-2 {{ $pengajuanOpen ? 'show' : '' }}"
                id="{{ $prefix }}-collapsePengajuan">
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
                    class="nav-link d-flex py-1 {{ request()->fullUrlIs('*receive*') ? 'active' : 'text-dark' }}">
                    Selesai
                </a>
            </div>
        </li>
    </ul>

    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a href="{{ route('admin.purchase_orders.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('admin.purchase_orders.*') ? 'active' : 'text-dark' }}">
                <span><i class="bi bi-file-earmark-text me-2"></i> Purchase Order</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.stock_opname.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('admin.stock_opname.*') ? 'active' : 'text-dark' }}">
                <span><i class="bi bi-list-ul me-2"></i> Stock Opname</span>
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
