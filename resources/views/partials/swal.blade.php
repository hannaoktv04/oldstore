@if(config('app.env') !== 'testing')
<script>
    @if(Session::has('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sukses',
            text: '{{ Session::get('success') }}',
            confirmButtonText: 'OK',
            timer: 3000
        });
    @endif

    @if(Session::has('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ Session::get('error') }}',
            confirmButtonText: 'OK'
        });
    @endif

    @if(Session::has('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: '{{ Session::get('warning') }}',
            confirmButtonText: 'OK',
            timer: 5000
        });
    @endif

    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        if(jqxhr.status === 419) {
            Swal.fire({
                icon: 'error',
                title: 'Sesi Habis',
                text: 'Sesi Anda telah habis, silakan refresh halaman',
                confirmButtonText: 'Refresh',
                allowOutsideClick: false
            }).then(() => {
                location.reload();
            });
            return;
        }
        if(jqxhr.responseJSON && jqxhr.responseJSON.message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: jqxhr.responseJSON.message
            });
        }
    });

    window.showConfirmation = function(options) {
        return Swal.fire({
            title: options.title || 'Apakah Anda yakin?',
            text: options.text || '',
            icon: options.icon || 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: options.confirmText || 'Ya, lanjutkan!',
            cancelButtonText: options.cancelText || 'Batal',
            ...options
        });
    };
</script>
@endif