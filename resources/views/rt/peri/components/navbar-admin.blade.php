<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="ri-menu-fill ri-22px"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        
        <div class="navbar-nav align-items-center">
            </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-4 me-xl-1">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                    href="javascript:void(0);" data-bs-toggle="dropdown" id="notificationDropdown">
                    <i class="ri-notification-2-line ri-22px"></i>
                    <span
                        class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border d-none"
                        id="notifIndicator"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="notificationDropdown">
                    <li class="dropdown-menu-header border-bottom py-50">
                        <div class="dropdown-header d-flex align-items-center py-2">
                            <h6 class="mb-0 me-auto">Notifikasi Sistem</h6>
                            <div class="d-flex align-items-center">
                                <span class="badge rounded-pill bg-label-primary fs-xsmall me-2 d-none"
                                    id="notifCountBadge">0</span>
                                <a href="javascript:void(0)" class="btn btn-text-secondary rounded-pill btn-icon"
                                    id="markAllAsRead">
                                    <i class="ri-mail-open-line text-heading ri-20px"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                        <ul class="list-group list-group-flush" id="notificationList">
                            <li class="list-group-item text-center py-4 loading-notifications">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mb-0 mt-2">Memuat notifikasi...</p>
                            </li>
                        </ul>
                    </li>
                    <li class="border-top">
                        <div class="d-grid p-4">
                            <a class="btn btn-primary btn-sm d-flex justify-content-center" href="#">
                                <small class="align-middle">Lihat semua notifikasi</small>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="../../assets/img/avatars/jay.jpg" alt class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ri-logout-box-r-line align-middle ri-22px me-3"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
            </ul>
    </div>
</nav>