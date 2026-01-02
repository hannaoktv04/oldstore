<style>
  .nav-link {
    transition: all 0.2s ease-in-out;
    font-size: 0.9rem;
    padding: 6px 10px;
  }
  .nav-link:hover { color: #a19d53ff !important; }

  .navbar-brand img { height: 36px; }
  .navbar-brand span { font-size: 0.85rem; color: #6c757d; }

  .ri, [class^="ri-"], [class*=" ri-"] { font-size: 1.1rem; }

  .offcanvas-body .nav-link {
    font-size: 0.9rem;
    padding: 8px 12px;
    transition: 0.2s;
  }
  .offcanvas-body .nav-link:hover {
    background: #f8f9fa;
    border-radius: 6px;
  }
</style>

<nav class="navbar navbar-expand-lg bg-white shadow-sm py-2 sticky-top">
  <div class="container">

    <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold text-dark" href="{{ route('staff-pengiriman.dashboard') }}">
      <img src="{{ asset('assets/img/peri.png') }}" alt="PERI Logo">
      <span>Staff Pengiriman</span>
    </a>

    <button class="btn border-0 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
      <i class="ri-menu-3-line fs-4"></i>
    </button>

    <div class="d-none d-lg-flex align-items-center gap-3">

      <a href="{{ route('staff-pengiriman.waiting') }}" class="nav-link text-dark d-flex align-items-center gap-1">
        <i class="ri-hourglass-2-line text-warning"></i> <span>Waiting</span>
      </a>

      <a href="{{ route('staff-pengiriman.onprogress') }}" class="nav-link text-dark d-flex align-items-center gap-1">
        <i class="ri-timer-2-line text-primary"></i> <span>On Progress</span>
      </a>

      <a href="{{ route('staff-pengiriman.selesai') }}" class="nav-link text-dark d-flex align-items-center gap-1">
        <i class="ri-check-circle-line text-success"></i> <span>Selesai</span>
      </a>

      <div class="dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 text-dark" href="#" data-bs-toggle="dropdown">
          <i class="ri-user-3-line"></i> <span>{{ Auth::user()->nama }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end rounded-3 shadow-sm">
          <li class="px-3 py-2 small text-muted">Role: <strong>{{ Auth::user()->role }}</strong></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-house me-2"></i>Homepage</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item text-danger">
                <i class="ri-logout-box-r-line me-2"></i>Logout
              </button>
            </form>
          </li>
        </ul>
      </div>

    </div>

  </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel" style="width: 250px;">
  <div class="offcanvas-header">
    <h6 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h6>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column gap-3">

    <a href="{{ route('staff-pengiriman.waiting') }}" class="nav-link d-flex align-items-center gap-2 text-dark">
      <i class="ri-hourglass-2-line text-warning"></i> Waiting
    </a>

    <a href="{{ route('staff-pengiriman.onprogress') }}" class="nav-link d-flex align-items-center gap-2 text-dark">
      <i class="ri-timer-2-line text-primary"></i> On Progress
    </a>

    <a href="{{ route('staff-pengiriman.selesai') }}" class="nav-link d-flex align-items-center gap-2 text-dark">
      <i class="ri-check-double-line text-success"></i> Selesai
    </a>

    <div class="dropdown mt-3">
      <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-dark" href="#" data-bs-toggle="dropdown">
        <i class="ri-user-3-line"></i> {{ Auth::user()->nama }}
      </a>
      <ul class="dropdown-menu shadow-sm rounded-3">
        <li class="px-3 py-2 small text-muted">Role: <strong>{{ Auth::user()->role }}</strong></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-truck me-2"></i>Homepages</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="ri-logout-box-r-line me-2"></i>Logout
            </button>
          </form>
        </li>
      </ul>
    </div>

  </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/peri/navbar.js') }}"></script>
@endpush
