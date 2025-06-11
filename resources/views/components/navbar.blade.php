<nav class="navbar navbar-expand-lg bg-white shadow-sm py-3 sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold brand-custom fs-2" href="{{ url('/home') }}">PERI</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
      aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link fw-medium" href="{{ url('/kategori') }}">Kategori</a>
        </li>
      </ul>

      <form class="d-flex me-3 flex-grow-1" role="search" method="GET" action="{{ route('search') }}">
        <div class="input-group position-relative">
          <input name="q" class="form-control rounded-pill ps-4" type="search" placeholder="Cari barang yang kamu inginkan..." aria-label="Search" value="{{ request('q') }}">
          <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted">
            <i class="bi bi-search"></i>
          </span>
        </div>
      </form>

      <!-- Cart dan User Icon -->
      <div class="d-flex align-items-center gap-3">

        @guest
          <div class="position-relative">
            <button id="cart-icon" class="icon-button text-dark bg-transparent border-0 p-0">
              <i class="bi bi-bag-fill fs-5"></i>
            </button>
            <div class="position-absolute bg-white shadow rounded p-2 mt-2 d-none z-3 text-center" id="cart-popup" style="min-width: 160px; min-height: 150px; right: 0px; font-size: 14px">
              <p class="mt-1 mb-3">Keranjang pesanan kamu kosong</p>
              <a href="{{ route('login') }}" class="text-decoration-none fst-italic text-custom">Ajukan pesanan sekarang!</a>
            </div>
          </div>
        @else
        
          @if(Auth::user()->role === 'pegawai')
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

            <div id="cart-popup" class="position-absolute text-dark bg-white shadow rounded p-3 mt-2 z-3 d-none" style="min-width: 315px; right: 0px; font-size: 14px;">
              <h6 class="mb-3">Barang yang ada di Keranjang</h6>

              @forelse($cartItems as $item)
                <div class="d-flex align-items-start mb-2">
                  <img src="{{ asset('assets/img/products/' . $item->item->image) }}" alt="{{ $item->item->nama_barang }}" width="50" class="me-2 rounded">
                  <div>
                    <small>{{ $item->item->kategori }}</small><br>
                    <strong>{{ $item->item->nama_barang }}</strong><br>
                    <small>Jumlah: {{ $item->qty }} {{ $item->item->satuan }}</small>
                  </div>
                </div>
              @empty
                <p class="text-muted">Keranjang kamu kosong.</p>
              @endforelse


              <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('item_requests.history') }}" class="btn btn-outline-success btn-sm">Status Pengajuan</a>
                <a href="{{ route('cart.index') }}" class="btn btn-success btn-sm">Lihat Keranjang</a>
              </div>
            </div>
          </div>
        @endif
        @endguest

        <!-- User dropdown -->
        <div class="dropdown">
          <button id="user-icon" class="icon-button text-dark bg-transparent border-0 p-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-fill fs-4"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow p-3" style="min-width: 250px;">
            @guest
              <li class="text-center mb-2">Masuk sebagai...</li>
              <li class="d-flex">
                <button onclick="window.location.href='{{ url('/login') }}'" class="btn w-50 border-end rounded-0">User</button>
                <button onclick="window.location.href='{{ url('/login') }}'" class="btn w-50 rounded-0">Admin</button>
              </li>
            @else
              <li class="text-center mb-2">
                <strong>{{ Auth::user()->nama }}</strong><br>
                <small class="text-muted">{{ Auth::user()->role }}</small>
              </li>
              <li><hr class="dropdown-divider"></li>

              @if(Auth::user()->role === 'admin')
                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
              @else
                <li><a class="dropdown-item" href="{{ route('user.setting') }}"><i class="bi bi-gear-fill me-2"></i>Setting</a></li>
              @endif

              <li><hr class="dropdown-divider"></li>
              <li>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                </form>
              </li>
            @endguest
          </ul>
        </div>

      </div>
    </div>
  </div>
</nav>
