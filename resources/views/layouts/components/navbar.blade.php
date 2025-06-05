<nav class="navbar navbar-expand-lg bg-white shadow-sm py-3 sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold brand-custom fs-2" href="#">PERI</a>
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

            <form class="d-flex me-3 flex-grow-1" role="search">
                <div class="input-group">
                    <input class="form-control rounded-pill ps-4" type="search" placeholder="Cari barang yang kamu inginkan..." aria-label="Search">
                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                </div>
            </form>

            <div class="d-flex align-items-center gap-3">
                <!-- Cart -->
                <div class="position-relative">
                  <button id="cart-icon" class="icon-button text-dark bg-transparent border-0 p-0">
                    <i class="bi bi-bag-fill fs-5"></i>
                  </button>
                  <div class="position-absolute bg-white shadow rounded p-2 mt-2 d-none z-3 text-center" id="cart-popup" style="min-width: 160px; min-height: 150px; right: 0px; font-size: 14px">
                    <p class="mt-1 mb-3">Keranjang pesanan kamu kosong</p>
                    <a href="#" class="text-decoration-none fst-italic text-custom">Ajukan pesanan sekarang!</a>
                  </div>
                </div>

                <!-- User -->
                <div class="position-relative">
                  <div id="popup-overlay" class="popup-overlay d-none"></div>
                  <button id="user-icon" class="icon-button text-dark bg-transparent border-0 p-0">
                    <i class="bi bi-person-fill fs-4"></i>
                  </button>
                  <div id="user-popup" class="user-popup d-none">
                    <p class="mb-3 text-center">Masuk sebagai...</p>
                    <div class="d-flex">
                      <button onclick="window.location.href='{{ url('/user-login') }}'" class="btn w-50 border-end rounded-0">User</button>
                      <button onclick="window.location.href='{{ url('/admin-login') }}'" class="btn w-50 rounded-0">Admin</button>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</nav>
