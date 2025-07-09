<!doctype html>
<html lang="en" class="light-style" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Homepage | PERI</title>

    <!-- Fonts & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('style')
</head>
<body>

@if (isset($opnameAktif) && $opnameAktif)
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Stok Opname Sedang Berlangsung',
            text: 'Pengajuan tidak dapat dilakukan saat stok opname berlangsung.',
            confirmButtonText: 'Mengerti'
        });
    </script>
@endif

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <div class="layout-page">
            @include('components.navbar')

            @if (isset($opnameAktif) && $opnameAktif)
                @php
                    $hasTime = strlen($opnameDimulai) > 10;
                    $opnameStartFull = \Carbon\Carbon::parse($hasTime ? $opnameDimulai : $opnameDimulai . ' 00:00:00')->toIso8601String();
                @endphp

                <div class="opname-banner bg-success text-white py-2 w-100">
                    <div class="ticker">
                        <div class="ticker-move">
                            @for ($i = 0; $i < 3; $i++)
                            <span class="ticker-text">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                Stok Opname sedang berlangsung — Pengajuan tidak dapat dilakukan.
                                Sudah berlangsung selama: <span id="opnameDuration">--</span>
                            </span>
                            @endfor
                        </div>
                    </div>
                </div>

                <style>
                    .opname-banner {
                        position: fixed;
                        top: 74px; 
                        left: 0;
                        right: 0;
                        z-index: 1040;
                    }
                    .ticker {
                        overflow: hidden;
                        white-space: nowrap;
                        position: relative;
                    }
                    .ticker-move {
                        display: inline-block;
                        white-space: nowrap;
                        animation: scroll-left 20s linear infinite;
                    }
                    .ticker-text {
                        display: inline-block;
                        padding-right: 4rem;
                        font-weight: 500;
                        font-size: 0.95rem;
                    }
                    @keyframes scroll-left {
                        0%   { transform: translateX(0%); }
                        100% { transform: translateX(-50%); }
                    }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const opnameStart = new Date("{{ $opnameStartFull }}").getTime();
                        setInterval(() => {
                            const now = new Date().getTime();
                            const elapsed = now - opnameStart;

                            const days = Math.floor(elapsed / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((elapsed % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);

                            const display = `${days > 0 ? days + ' hari ' : ''}${String(hours).padStart(2, '0')} jam ${String(minutes).padStart(2, '0')} menit ${String(seconds).padStart(2, '0')} detik`;
                            document.querySelectorAll('#opnameDuration').forEach(el => el.textContent = display);
                        }, 1000);
                    });
                </script>
            @endif

            <div class="content-wrapper" style="margin-top: {{ isset($opnameAktif) && $opnameAktif ? '38px' : '0' }};">
                <div class="container-xxl flex-grow-1 container-p-y">
                    @yield('content')
                </div>

                @if(!(Auth::check() && Auth::user()->role === 'admin'))
                    @include('components.footer')
                @endif

                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>

<!-- Core JS -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
@stack('scripts')
</body>
</html>