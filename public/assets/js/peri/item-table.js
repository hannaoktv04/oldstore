(function () {
  if (window.__itemTableInit) return;
  window.__itemTableInit = true;

  $(function () {
    $.ajaxSetup({
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    const table = $('#itemTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: row => 'Details for ' + row.data().produk
          }),
          renderer: $.fn.dataTable.Responsive.renderer.tableAll({ tableClass: 'table' })
        }
      },
      ajax: {
        url: $('#itemTable').data('source') || '/admin/items/data',
        data: d => {
          d.kategori = $('#filter-kategori').val();
          d.status   = $('#filter-status').val();
          d.stok     = $('#filter-stok').val();
        }
      },
      columns: [
        { data: null, defaultContent: '', orderable:false, searchable:false, className:'control' },
        { data: 'checkbox', name:'checkbox', orderable:false, searchable:false, className:'text-center' },
        { data: 'produk', name:'nama_barang' },
        { data: 'kategori', name:'category.categori_name' },
        { data: 'stok', name:'stocks.qty', className:'text-center' },
        { data: 'stok_minimum', name:'stok_minimum', className:'text-center' },
        { data: 'status', name:'state.is_archived', className:'text-center' },
        { data: 'action', name:'action', orderable:false, searchable:false, className:'text-center' },
      ],
      order: [[2, 'asc']],
      language: {
        search: 'Cari:', lengthMenu: '_MENU_ data',
        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
        zeroRecords: 'Data tidak ditemukan'
      }
    });

    $('#filter-kategori, #filter-status, #filter-stok').on('change', () => table.draw());

    $(document).on('click', '.btn-detail', function () {
      const id = $(this).data('id');
      $('#detailModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
      $('#detailModal').modal('show');
      $.get(`/admin/items/${id}`, (data) => {
        let photoHtml = '';
        if (data.gallery_urls?.length) {
          photoHtml = '<div class="d-flex flex-wrap gap-2 mb-3">' +
            data.gallery_urls.map(url => `
              <a href="${url}" target="_blank">
                <img src="${url}" class="img-thumbnail rounded-4" alt="Gambar Produk"
                     style="height:100px;width:100px;object-fit:cover;">
              </a>`).join('') + '</div>';
        } else {
          photoHtml = '<p>Tidak ada gambar.</p>';
        }
        const statusBadge = (data.state && data.state.is_archived)
          ? '<span class="badge bg-label-danger">Diarsip</span>'
          : '<span class="badge bg-label-success">Aktif</span>';

        $('#detailModalBody').html(`
          ${photoHtml}
          <table class="table">
            <tr><th style="width:30%;">Nama Produk</th><td>${data.nama_barang}</td></tr>
            <tr><th>Kategori</th><td>${data.category ? data.category.categori_name : 'N/A'}</td></tr>
            <tr><th>Stok Saat Ini</th><td>${data.stocks ? data.stocks.qty : 0} ${data.satuan ? data.satuan.nama_satuan : ''}</td></tr>
            <tr><th>Stok Minimum</th><td>${data.stok_minimum}</td></tr>
            <tr><th>Status</th><td>${statusBadge}</td></tr>
            <tr><th>Deskripsi</th><td>${data.deskripsi || ''}</td></tr>
          </table>
        `);
      });
    });

    const bulkOffcanvasEl = document.getElementById('bulkActionsOffcanvas');
    const bulkOffcanvas = bulkOffcanvasEl ? new bootstrap.Offcanvas(bulkOffcanvasEl) : null;

    $('#select-all-checkbox').on('click', function () {
      $('.item-checkbox').prop('checked', this.checked);
      toggleBulkActions();
    });
    $(document).on('click', '.item-checkbox', function () {
      $('#select-all-checkbox').prop('checked', $('.item-checkbox:checked').length === $('.item-checkbox').length);
      toggleBulkActions();
    });

    function toggleBulkActions() {
      const n = $('.item-checkbox:checked').length;
      $('#selected-items-count').text(n);
      if (bulkOffcanvas) n > 0 ? bulkOffcanvas.show() : bulkOffcanvas.hide();
    }
    function reloadTable() { table.ajax.reload(null, false); }

    function runBulkAction(action) {
      const ids = $('.item-checkbox:checked').map((_, el) => el.value).get();
      const actionText = action === 'hapus' ? 'menghapus' : (action === 'arsipkan' ? 'mengarsipkan' : 'mengaktifkan');
      Swal.fire({
        title: 'Anda Yakin?',
        text: `Anda akan ${actionText} ${ids.length} item.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
      }).then(res => {
        if (!res.isConfirmed) return;
        $.post($('#bulkActionRoute').val() || '{{ route("admin.items.bulkAction") }}', { ids, action })
          .done(resp => {
            if (resp.success) {
              Swal.fire('Berhasil!', resp.message, 'success');
              reloadTable();
              $('#select-all-checkbox').prop('checked', false);
              toggleBulkActions();
            }
          })
          .fail(() => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'));
      });
    }
    $('#bulk-delete').on('click', () => runBulkAction('hapus'));
    $('#bulk-archive').on('click', () => runBulkAction('arsipkan'));
    $('#bulk-unarchive').on('click', () => runBulkAction('aktifkan'));
  });
})();
