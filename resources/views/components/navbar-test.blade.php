<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="ri-menu-fill ri-22px"></i>
    </a>
  </div>

  <div class="navbar-nav align-items-center">
    <div class="nav-item navbar-search-wrapper mb-0 d-flex align-items-center">
      <img src="{{ asset('assets/img/sigma.png') }}" alt="Logo" style="height: 100px; margin-right: 10px;" />
    </div>
  </div>

  <ul class="navbar-nav flex-row align-items-center ms-auto">
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="ri-notification-3-line ri-22px"></i>
        <span class="badge bg-danger rounded-pill badge-notifications">3</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li class="dropdown-menu-header">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h5 class="text-body mb-0 me-auto">Notifikasi</h5>
          </div>
        </li>
        <li class="dropdown-notifications-list scrollable-container">
          <ul class="list-group list-group-flush">
            <li class="list-group-item list-group-item-action">
              <div class="d-flex">
                <div class="flex-grow-1">
                  <h6 class="mb-1">Permintaan Baru</h6>
                  <small class="text-muted">2 menit lalu</small>
                </div>
              </div>
            </li>
          </ul>
        </li>
        <li class="dropdown-menu-footer border-top">
          <a href="#" class="dropdown-item d-flex justify-content-center p-3">Lihat semua notifikasi</a>
        </li>
      </ul>
    </li>

    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="#">
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                </div>
              </div>
              <div class="flex-grow-1">
                <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                <small class="text-muted">{{ Auth::user()->roles->pluck('name')->first() }}</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('logout') }}"
             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="ri-logout-box-line me-2"></i>
            <span class="align-middle">Log Out</span>
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </li>
      </ul>
    </li>
  </ul>
</nav>
