<nav class="navbar navbar-expand-lg bg-white shadow-sm py-2 sticky-top">
    <div class="container d-flex justify-content-between align-items-center">

        <div class="d-flex align-items-center gap-3">
            <a class="navbar-brand fw-bold brand-custom fs-3" href="{{ url('/') }}">
                @if(Auth::check() && Auth::user()->role === 'admin')
                PERI <span class="fw-normal text-secondary fs-6">Admin</span>
                @else
                PERI
                @endif
            </a>
            <a class="nav-link fw-medium" href="{{ url('/kategori') }}">Kategori</a>
        </div>

        <form class="d-none d-lg-flex me-3 flex-grow-1 mx-3" role="search" method="GET" action="{{ route('search') }}">
            <div class="input-group position-relative w-100 align-items-center">
                <input name="q" class="form-control rounded-pill ps-4" type="search"
                    placeholder="Cari barang yang kamu inginkan..." aria-label="Search" value="{{ request('q') }}">
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted">
                    <i class="bi bi-search"></i>
                </span>
            </div>
        </form>

        <div class="d-flex align-items-center gap-2">

            <button class="btn d-lg-none text-dark bg-transparent border-0" type="button" data-bs-toggle="modal"
                data-bs-target="#searchModalMobile">
                <i class="bi bi-search fs-5"></i>
            </button>

            @auth
            @php
            $cartItems = \App\Models\Cart::where('user_id', Auth::id())->get();
            $jumlahKeranjang = $cartItems->count();
            @endphp
            <div class="position-relative">
                <button id="cart-icon" class="icon-button text-dark bg-transparent border-0 p-0">
                    <i class="bi bi-bag-fill fs-5"></i>
                    @if($jumlahKeranjang > 0)
                    <span class="badge bg-success rounded-pill position-absolute top-0 start-100 translate-middle">
                        {{ $jumlahKeranjang }}
                    </span>
                    @endif
                </button>
                <div id="cart-popup" class="position-absolute text-dark bg-white shadow rounded p-3 mt-2 z-3 d-none"
                    style="min-width: 315px; right: 0px; font-size: 14px;">
                    <h6 class="mb-3">Barang yang ada di Keranjang</h6>
                    @forelse($cartItems as $item)
                    <div class="d-flex align-items-start mb-2">
                        <img src="{{ asset('storage/' . $item->item->photo_url) }}"
                            alt="{{ $item->item->nama_barang }}" width="50" class="me-2 rounded">
                        <div>
                            <small>{{ $item->item->category->categori_name ?? 'Kategori Tidak Diketahui' }}</small><br>
                            <strong>{{ $item->item->nama_barang }}</strong><br>
                            <small>Jumlah: {{ $item->qty }} {{ $item->item->satuan }}</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">Keranjang kamu kosong.</p>
                    @endforelse
                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('user.history') }}" class="btn btn-outline-success btn-sm">Status
                            Pengajuan</a>
                        <a href="{{ route('cart.index') }}" class="btn btn-success btn-sm">Lihat Keranjang</a>
                    </div>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}" class="text-dark text-decoration-none">
                <i class="bi bi-bag-fill fs-5"></i>
            </a>
            @endauth

            <div class="dropdown">
                <button id="user-icon" class="icon-button text-dark bg-transparent border-0 pe-3 dropdown-toggle "
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-fill fs-4 "></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow p-3" style="min-width: 250px;">
                    @guest
                    <li class="text-center mb-2">Masuk sebagai...</li>
                    <li class="d-flex">
                        <button onclick="window.location.href='{{ url('/login') }}'"
                            class="btn w-50 border-end rounded-0">User</button>
                        <button onclick="window.location.href='{{ url('/login') }}'"
                            class="btn w-50 rounded-0">Admin</button>
                    </li>
                    @else
                    <li class="text-center mb-2">
                        <strong>{{ Auth::user()->nama }}</strong><br>
                        <small class="text-muted">{{ Auth::user()->role }}</small>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    @if(Auth::user()->role === 'admin')
                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i
                                class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                    @else
                    <li><a class="dropdown-item" href="{{ route('user.wishlist') }}"><i class="bi bi-heart me-2"></i>My
                            Wishlist</a></li>
                    @endif
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i
                                    class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="modal fade" id="searchModalMobile" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="GET" action="{{ route('search') }}" class="modal-content border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="searchModalLabel">Cari Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="search" name="q" class="form-control" placeholder="Cari barang..." required>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100">Cari Sekarang</button>
            </div>
        </form>
    </div>
</div>
