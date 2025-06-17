<!doctype html>
<html lang="en" class="light-style" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-no-customizer-starter">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Homepage | PERI</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/remixicon/remixicon.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/addItem.css') }}">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Helpers & Config -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('style')
    @stack('scripts')

</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Sidebar (optional) -->
      {{-- @include('layouts.components.sidebar') --}}

      <div class="layout-page">
        @include('components.navbar')

        <div class="content-wrapper">
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
  <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
  


  <script src="{{ asset('assets/js/main.js') }}"></script>

  <script src="{{ asset('assets/js/hero.js') }}"></script>


  <!-- Page JS -->
  @stack('scripts')
  <script src="{{ asset('assets/js/produk-detail.js') }}"></script>


</body>
</html>
