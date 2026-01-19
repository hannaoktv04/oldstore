<nav class="navbar navbar-expand-lg bg-white shadow-sm py-2 sticky-top">
  <div class="container d-flex justify-content-between align-items-center">

    <div class="d-flex align-items-center">
      @auth
        @if(auth()->user()->isAdmin())
          <button class="btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
            <i class="bi bi-list fs-4"></i>
          </button>
        @endif
      @endauth

      <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
        <img src="{{ asset('assets/img/old.jpg') }}" alt="Logo" style="height: 50px;">
        @auth
          @if(auth()->user()->isAdmin())
            <span class="fw-normal text-secondary fs-6 ms-2">Admin</span>
          @endif
        @endauth
      </a>

      <a class="nav-link fw-medium ms-3" href="{{ url('/kategori') }}">Kategori</a>
    </div>

    <form class="d-none d-lg-flex flex-grow-1 mx-3" method="GET" action="{{ route('search') }}">
      <div class="input-group w-100 position-relative">
        <input name="q" class="form-control rounded-pill ps-4" type="search"
               placeholder="Cari barang..." value="{{ request('q') }}">
        <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted">
          <i class="bi bi-search"></i>
        </span>
      </div>
    </form>

    @php
      use App\Models\Cart;

      $cartItems = auth()->check()
        ? Cart::with('item')->where('user_id', auth()->id())->get()
        : collect();

      $jumlahKeranjang = $cartItems->count();
    @endphp

    <div class="d-flex align-items-center gap-2">

      <button class="btn d-lg-none bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#searchModalMobile">
        <i class="bi bi-search fs-5"></i>
      </button>

      @auth
        @if(!auth()->user()->isAdmin())
          <div class="position-relative">
            <button id="cart-icon" class="bg-transparent border-0 p-0">
              <i class="bi bi-bag-fill fs-5"></i>
              @if($jumlahKeranjang > 0)
                <span class="badge bg-success rounded-pill position-absolute top-0 start-100 translate-middle">
                  {{ $jumlahKeranjang }}
                </span>
              @endif
            </button>

            <div id="cart-popup" class="position-absolute bg-white shadow rounded p-3 mt-2 d-none"
                 style="min-width:300px; right:0; z-index:1050;">
              <h6 class="mb-3">Keranjang</h6>

              @forelse($cartItems as $item)
                <div class="d-flex mb-2">
                  <div>
                    <strong>{{ $item->item->nama_barang }}</strong><br>
                    <small>Jumlah: {{ $item->qty }}</small>
                  </div>
                </div>
              @empty
                <p class="text-muted">Keranjang kosong</p>
              @endforelse

              <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('user.history') }}" class="btn btn-outline-success btn-sm">Status</a>
                <a href="{{ route('cart.index') }}" class="btn btn-success btn-sm">Keranjang</a>
              </div>
            </div>
          </div>
        @endif
      @endauth

      <div class="position-relative">
        <button id="user-icon" class="bg-transparent border-0 pe-3">
          <i class="bi bi-person-fill fs-4"></i>
        </button>

        <div id="user-popup" class="position-absolute end-0 mt-2 p-3 bg-white shadow rounded d-none"
             style="min-width:240px; z-index:1050;">

          @guest
            <div class="text-center mb-2">Masuk</div>
            <a href="{{ route('login') }}" class="btn btn-success w-100">Login</a>
          @else
            <div class="text-center mb-2">
              <strong>{{ auth()->user()->nama }}</strong><br>
              <small class="text-muted">
                {{ auth()->user()->isAdmin() ? 'Admin' : 'User' }}
              </small>
            </div>
            <hr>

            @if(auth()->user()->isAdmin())
              <a href="{{ route('admin.dashboard.index') }}" class="dropdown-item">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
              </a>
            @else
              <a href="{{ route('user.wishlist') }}" class="dropdown-item">
                <i class="bi bi-heart me-2"></i>Wishlist
              </a>
            @endif

            <hr>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button class="dropdown-item text-danger">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
              </button>
            </form>
          @endguest
        </div>
      </div>
    </div>
  </div>
</nav>

@push('scripts')
<script src="{{ asset('assets/js/peri/navbar.js') }}"></script>
@endpush
