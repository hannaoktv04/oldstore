@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
            });
        @endif

        @if (session('rejected'))
            Swal.fire({
                icon: 'warning',
                title: 'Ditolak',
                text: '{{ session('rejected') }}',
                confirmButtonColor: '#f0ad4e',
            });
        @endif
    });
</script>
@endpush
