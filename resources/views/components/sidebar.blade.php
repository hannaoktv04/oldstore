{{-- Sidebar --}}
<div class="col-md-3">
            <div class="card p-3 shadow-sm" style="min-height: 300px;">
                <ul class="nav flex-column sidebar-nav">
                    <li class="nav-item mb-2">
                        <a href="{{ route('kategori.index') }}" class="nav-link text-dark d-flex align-items-center">
                            <i class="bi bi-box-seam me-2"></i> Daftar Barang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.pengajuan.status', 'submitted') }}" class="nav-link text-dark d-flex align-items-center">
                            <i class="bi bi-clipboard-check me-2"></i> Cek Pengajuan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.addItem') }}" class="nav-link text-dark d-flex align-items-center">
                            <i class="bi bi-clipboard-check me-2"></i> Add Item
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.wishlist.index') }}" class="nav-link text-dark d-flex align-items-center">
                            <i class="bi bi-heart me-2"></i> Wishlist User
                        </a>
                    </li>
                </ul>
            </div>
</div>
