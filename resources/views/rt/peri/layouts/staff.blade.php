<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Dashboard Staff | PERI</title>

  <!-- Bootstrap & SweetAlert -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css">
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

  @stack('style')
</head>
<body class="bg-light">

  @include('peri::components.navbar-staff')

  <main class="py-4">
    @yield('content')
  </main>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

  @stack('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @if (session('success'))
  <script>
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: '{{ session('success') }}',
          confirmButtonColor: '#198754'
      });
  </script>
  @endif

  @if (session('info'))
  <script>
      Swal.fire({
          icon: 'info',
          title: 'Informasi',
          text: '{{ session('info') }}',
          confirmButtonColor: '#0d6efd'
      });
  </script>
  @endif

  @if (session('error'))
  <script>
      Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: '{{ session('error') }}',
          confirmButtonColor: '#dc3545'
      });
  </script>
  @endif


</body>
</html>
