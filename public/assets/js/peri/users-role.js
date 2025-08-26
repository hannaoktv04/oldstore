(function () {
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.jQuery === 'undefined') {
      console.error('jQuery belum dimuat sebelum users-index.js');
      return;
    }
    const $ = window.jQuery;
    const $tbl = $('#usersTable');
    if ($tbl.length) {
      const hasDT = !!($.fn && typeof $.fn.DataTable === 'function');
      if (!hasDT) {
        console.error('DataTables belum dimuat. Pastikan JS & CSS DataTables sudah di-include.');
      } else {
        if ($.fn.DataTable.isDataTable($tbl)) {
          $tbl.DataTable().destroy();
        }
        $tbl.DataTable({
          responsive: true, 
          language: {
            search: 'Cari:',
            lengthMenu: '_MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            paginate: {
              next: '<i class="ri-arrow-right-s-line"></i>',
              previous: '<i class="ri-arrow-left-s-line"></i>',
            },
          },
          columnDefs: [
            { orderable: false, targets: [4, 5, 6] }
          ]
        });
      }
    }
    const hasSelect2 = !!($.fn && typeof $.fn.select2 === 'function');
    if (!hasSelect2) {
      console.warn('Select2 belum dimuat. Lewati inisialisasi select2.');
      return;
    }
    $('.select2-roles').each(function () {
      const $modalContent = $(this).closest('.modal').find('.modal-content');
      $(this).select2({
        placeholder: 'Pilih role',
        width: '100%',
        dropdownParent: $modalContent.length ? $modalContent : $(document.body)
      });
    });
    $('.modal').on('shown.bs.modal', function () {
      $(this).find('.select2-roles').each(function () {
        const $el = $(this);
        if ($el.data('select2')) $el.select2('destroy');
        $el.select2({
          placeholder: 'Pilih role',
          width: '100%',
          dropdownParent: $el.closest('.modal').find('.modal-content')
        });
      });
    });
    $('.modal').on('hidden.bs.modal', function () {
      $(this).find('.select2-roles').each(function () {
        const $el = $(this);
        if ($el.data('select2')) $el.select2('destroy');
      });
    });
  });
})();
