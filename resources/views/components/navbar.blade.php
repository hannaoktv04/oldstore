<nav class="navbar navbar-expand-lg bg-white shadow-sm py-2 sticky-top">
  <div class="container d-flex justify-content-between align-items-center">

    <div class="d-flex align-items-center">
      @if(Auth::check() && Auth::user()->hasRole('admin'))
        <button class="btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
          <i class="bi bi-list fs-4"></i>
        </button>
      @endif

      <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
        <img src="{{ asset('assets/img/peri.png') }}" alt="PERI Logo" style="height: 50px;">
        @if(Auth::user()->hasRole('admin'))
          <span class="fw-normal text-secondary fs-6 ms-2">Admin</span>
        @endif
      </a>

      <a class="nav-link fw-medium ms-3" href="{{ url('/kategori') }}">Kategori</a>
    </div>

    <form class="d-none d-lg-flex flex-grow-1 mx-3" method="GET" action="{{ route('search') }}">
      <div class="input-group position-relative w-100">
        <input name="q" class="form-control rounded-pill ps-4" type="search" placeholder="Cari barang..." value="{{ request('q') }}">
        <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted">
          <i class="bi bi-search"></i>
        </span>
      </div>
    </form>

    @php
      use App\Models\Cart;
      use App\Models\StockNotification;
      $cartItems = Auth::check() ? Cart::where('user_id', Auth::id())->get() : collect();
      $jumlahKeranjang = $cartItems->count();
      $notifikasiProdukBaru = Auth::check() && Auth::user()->hasRole('pegawai')
        ? StockNotification::where('seen', false)->with('item')->latest()->get()
        : collect();
    @endphp

    <div class="d-flex align-items-center gap-2">
      <button class="btn d-lg-none text-dark bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#searchModalMobile">
        <i class="bi bi-search fs-5"></i>
      </button>

      @auth
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
                <img src="{{ asset('storage/' . ($item->item->photo->image ?? 'placeholder.jpg')) }}" width="50" class="me-2 rounded">
                <div>
                  <small>{{ $item->item->category->categori_name ?? 'Kategori Tidak Diketahui' }}</small><br>
                  <strong>{{ $item->item->nama_barang }}</strong><br>
                  <small>Jumlah: {{ $item->qty }} {{ $item->item->satuan->nama_satuan }}</small>
                </div>
              </div>
            @empty
              <p class="text-muted">Keranjang kamu kosong.</p>
            @endforelse
            <div class="d-flex justify-content-between mt-3">
              <a href="{{ route('user.history') }}" class="btn btn-outline-success btn-sm">Status Pengajuan</a>
              <a href="{{ route('cart.index') }}" class="btn btn-success btn-sm">Lihat Keranjang</a>
            </div>
          </div>
        </div>
      @endauth

      @if(Auth::check() && Auth::user()->hasRole('pegawai'))
        <div class="position-relative">
          <button class="icon-button text-dark bg-transparent border-0 p-0 position-relative" id="notif-icon">
            <i class="bi bi-bell-fill fs-5"></i>
            @if($notifikasiProdukBaru->count() > 0)
              <span class="badge bg-success rounded-pill position-absolute top-0 start-100 translate-middle">
                {{ $notifikasiProdukBaru->count() }}
              </span>
            @endif
          </button>
          <div id="notif-popup"
               class="position-absolute text-dark bg-white shadow rounded p-3 mt-2 z-3 d-none"
               style="min-width: 300px; right: 0px; font-size: 14px;">
            <h6 class="mb-2">Produk Tersedia Kembali</h6>
            @forelse($notifikasiProdukBaru as $notif)
              <div class="mb-2 small d-flex align-items-start">
                <i class="bi bi-box-seam text-success me-2 mt-1"></i>
                <div>
                  <strong>{{ $notif->item->nama_barang }}</strong><br>
                  <small class="text-muted">Sekarang sudah tersedia</small>
                </div>
              </div>
            @empty
              <div class="text-muted">Tidak ada notifikasi.</div>
            @endforelse
            @if($notifikasiProdukBaru->count() > 0)
              <div class="mt-3">
                <form action="{{ route('notifikasi.markSeen') }}" method="POST">
                  @csrf
                  <button class="btn btn-outline-secondary btn-sm w-100" type="submit">Tandai Sudah Dibaca</button>
                </form>
              </div>
            @endif
          </div>
        </div>
      @endif

      <div class="position-relative">
        <button id="user-icon" class="icon-button text-dark bg-transparent border-0 pe-3">
          <i class="bi bi-person-fill fs-4"></i>
        </button>
        <div id="user-popup" class="position-absolute end-0 mt-2 p-3 rounded shadow bg-white d-none" style="min-width: 250px; z-index: 1050;">
          @guest
            <div class="text-center mb-2">Masuk sebagai...</div>
            <div class="d-flex">
              <button onclick="window.location.href='{{ url('/login') }}'" class="btn w-50 border-end rounded-0">User</button>
              <button onclick="window.location.href='{{ url('/login') }}'" class="btn w-50 rounded-0">Admin</button>
            </div>
          @else
            <div class="text-center text-dark mb-2">
              <strong>{{ Auth::user()->nama }}</strong><br>
              <small class="text-muted">{{ Auth::user()->role }}</small>
            </div>
            <hr>
           @if(Auth::user()->hasRole('admin'))
              <a href="{{ route('admin.dashboard') }}"
                class="dropdown-item d-block bg-white nav-link {{ Request::routeIs('admin.dashboard') ? 'text-success fw-semibold' : 'text-dark' }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
              </a>
            @else
              <a href="{{ route('user.wishlist') }}"
                class="dropdown-item d-block bg-white nav-link {{ Request::routeIs('user.wishlist') ? 'text-success fw-semibold' : 'text-dark' }}">
                <i class="bi bi-heart me-2"></i>My Wishlist
              </a>
            @endif
            <hr>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item bg-white text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
            </form>
          @endguest
        </div>
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

