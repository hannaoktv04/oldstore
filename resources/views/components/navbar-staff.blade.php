<nav class="navbar navbar-expand-lg bg-white shadow-sm py-2 sticky-top">
  <div class="container d-flex justify-content-between align-items-center">

    <!-- Logo & Brand -->
    <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold text-dark" href="{{ route('staff-pengiriman.dashboard') }}">
      <img src="{{ asset('assets/img/peri.png') }}" alt="PERI Logo" style="height: 44px;">
      <span class="text-muted fs-6">Staff Pengiriman</span>
    </a>

    <!-- User Dropdown -->
    <div class="dropdown">
      <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle fs-5"></i>
        <span class="d-none d-sm-inline">{{ Auth::user()->nama }}</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm p-2" style="min-width: 180px;">
        <li class="px-3 py-2">
          <small class="text-muted">Role:</small><br>
          <strong>{{ Auth::user()->role }}</strong>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item" href="{{ route('staff-pengiriman.dashboard') }}">
            <i class="bi bi-truck me-2"></i>My Progress
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="bi bi-box-arrow-right me-2"></i>Logout
            </button>
          </form>
        </li>
      </ul>
    </div>

  </div>
</nav>
