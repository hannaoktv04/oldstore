@if(config('app.env') !== 'testing')
<script>
// ---------------------------
// SWEETALERT MIXIN + HELPERS
// ---------------------------
const swalBase = Swal.mixin({
  confirmButtonText: 'OK',
  allowOutsideClick: false,
  showCancelButton: false,
});

// helper cepat
window.swalSuccess = (text, opts={}) =>
  swalBase.fire({
    icon: 'success',
    title: 'Berhasil',
    text,
    showConfirmButton: false,
    showCancelButton: false,
    timer: 2000,
    timerProgressBar: true,
    allowOutsideClick: true,
    allowEscapeKey: true,
    ...opts
  });

window.swalError = (text, opts={}) =>
  swalBase.fire({ icon:'error', title:'Error', text, ...opts });

window.swalWarn = (text, opts={}) =>
  swalBase.fire({ icon:'warning', title:'Peringatan', text, ...opts });

window.swalInfo = (text, opts={}) =>
  swalBase.fire({ icon:'info', title:'Info', text, ...opts });

window.swalConfirm = (text='Yakin?', opts={}) =>
  swalBase.fire({
    icon:'warning',
    title: opts.title || 'Apakah Anda yakin?',
    text,
    showCancelButton: true,
    confirmButtonText: opts.confirmText || 'Ya, lanjutkan',
    cancelButtonText:  opts.cancelText  || 'Batal',
    ...opts
  }).then(r => r.isConfirmed);

window.apiJson = async (url, opts = {}) => {
  const headers = {
    Accept: 'application/json',
    ...(opts.headers || {})
  };

  // auto CSRF (Laravel)
  const csrf = document.querySelector('meta[name="csrf-token"]');
  if (csrf && !headers['X-CSRF-TOKEN']) headers['X-CSRF-TOKEN'] = csrf.content;

  const res = await fetch(url, { ...opts, headers });

  let data = null;
  try { data = await res.clone().json(); } catch (_) {}

  // sesi habis
  if (res.status === 419) {
    await swalError('Sesi Anda habis. Silakan muat ulang halaman.', { confirmButtonText:'Muat ulang' });
    location.reload(); return Promise.reject(new Error('Session expired'));
  }

  // validasi server (422) -> gabungkan pesan
  if (res.status === 422 && data && data.errors) {
    const details = Object.values(data.errors).flat().join('\n');
    throw new Error(data.message ? `${data.message}\n${details}` : details);
  }

  // gagal lain
  if (!res.ok || (data && data.success === false)) {
    const msg = (data && (data.message || data.error)) || `HTTP ${res.status}`;
    throw new Error(msg);
  }

  return data ?? {};
};

// ---------------------------
// FLASH SESSION (opsional):
// tampilkan swal dari session
// ---------------------------
@if(Session::has('success'))
  swalSuccess(@json(Session::get('success')));
@endif

@if(Session::has('error'))
  swalError(@json(Session::get('error')));
@endif

@if(Session::has('warning'))
  swalWarn(@json(Session::get('warning')));
@endif
</script>
@endif

