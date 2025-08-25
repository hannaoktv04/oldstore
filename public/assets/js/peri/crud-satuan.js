document.addEventListener('DOMContentLoaded', function () {
  if (typeof $ === 'undefined') {
    console.error('jQuery tidak ditemukan. Muat jQuery sebelum crud-satuan.js');
    return;
  }

  const useSwal = {
    success: (text, opts={}) => {
      if (typeof window.swalSuccess === 'function') return window.swalSuccess(text, opts);
      if (typeof Swal !== 'undefined') return Swal.fire({ icon: 'success', title: 'Berhasil', text, ...opts });
    },
    error: (text, opts={}) => {
      if (typeof window.swalError === 'function') return window.swalError(text, opts);
      if (typeof Swal !== 'undefined') return Swal.fire({ icon: 'error', title: 'Error', text, ...opts });
    },
    warn: (text, opts={}) => {
      if (typeof window.swalWarn === 'function') return window.swalWarn(text, opts);
      if (typeof Swal !== 'undefined') return Swal.fire({ icon: 'warning', title: 'Peringatan', text, ...opts });
    },
    confirm: (text='Yakin?', opts={}) => {
      if (typeof window.swalConfirm === 'function') return window.swalConfirm(text, opts);

      if (typeof Swal !== 'undefined') {
        return Swal.fire({
          icon: 'warning',
          title: opts.title || 'Apakah Anda yakin?',
          text,
          showCancelButton: true,
          confirmButtonText: opts.confirmText || 'Ya, lanjutkan',
          cancelButtonText:  opts.cancelText  || 'Batal',
          allowOutsideClick: false,
          ...(opts || {})
        }).then(r => !!r.isConfirmed);
      }
      return Promise.resolve(window.confirm(text));
    }
  };

  const csrf = document.querySelector('meta[name="csrf-token"]');
  if (!csrf) { console.error('Meta CSRF token tidak ditemukan'); return; }
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrf.getAttribute('content') } });

  const $tableEl      = $('#satuanTable');
  const $tbody        = $('#satuanTbody');
  const $modal        = $('#satuanModal');
  const $form         = $('#satuanForm');
  const $idInput      = $('#satuanId');
  const $namaInput    = $('#nama_satuan');
  const $errNama      = $('#namaSatuanError');
  const storeUrl      = $form.data('store-url');
  const updateUrlBase = $form.data('update-url-base') || $form.data('update-url');

  const table = $tableEl.DataTable({
    columnDefs: [{ orderable: false, targets: [2] }],
    order: [[0, 'asc']],
    language: {
      search: "Cari:",
      lengthMenu: "_MENU_ data",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data",
      infoFiltered: "(difilter dari _MAX_ total data)",
      zeroRecords: "Data tidak ditemukan",
      paginate: { previous: "<", next: ">" }
    }
  });

  function esc(s) {
    return String(s).replace(/[&<>"'`=\/]/g, c =>
      ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[c])
    );
  }

  function setModeAdd() {
    $form[0].reset();
    $idInput.val('');
    $('#modalTitle').html('Tambah Satuan');
    $form.attr('action', storeUrl);
    $form.find('input[name="_method"]').remove();
    $errNama.addClass('d-none').text('');
  }

  function setModeEdit(id, name) {
    $idInput.val(id);
    $namaInput.val(name);
    $('#modalTitle').html('Edit Satuan');
    $form.attr('action', `${updateUrlBase}/${id}`);
    if (!$form.find('input[name="_method"]').length) {
      $form.append('<input type="hidden" name="_method" value="PUT">');
    }
    $errNama.addClass('d-none').text('');
  }

  $('#addSatuanBtn').on('click', function () {
    setModeAdd();
    $modal.modal('show');
  });

  $(document).on('click', '.edit-btn, .edit-btn *', function (e) {
    const btn = e.target.closest('.edit-btn');
    if (!btn) return;
    setModeEdit(btn.getAttribute('data-id'), btn.getAttribute('data-name'));
    $modal.modal('show');
  });

  $modal.on('show.bs.modal', function (e) {
    const raw = e.relatedTarget || null;
    const isEditTrigger = !!(raw && raw.closest && raw.closest('.edit-btn'));
    if (!isEditTrigger) setModeAdd();
  });
  $form.on('submit', function (e) {
    e.preventDefault();

    $errNama.addClass('d-none').text('');
    const id     = $idInput.val();
    const isEdit = !!id;
    const url    = isEdit ? `${updateUrlBase}/${id}` : storeUrl;
    const payload = { nama_satuan: $namaInput.val() };

    $('#submitBtn').attr('disabled', true);

    $.ajax({
      url: url,
      type: 'POST',
      data: isEdit ? { _method: 'PUT', ...payload } : payload,
      success: function (res) {
        $modal.modal('hide');

        if (!res || res.status !== 'ok') {
          useSwal.warn('Respons server tidak sesuai.');
          return;
        }

        const data = res.data;

        if (isEdit) {
          const $row = $tbody.find(`tr[data-row-id="${data.id}"]`);
          if ($row.length) {
            $row.find('.nama-satuan').text(data.nama_satuan);
            $row.find('.edit-btn').attr('data-name', data.nama_satuan);
            $row.find('.delete-btn').attr('data-name', data.nama_satuan);
            $row.addClass('table-success');
            setTimeout(() => $row.removeClass('table-success'), 900);
          }
          useSwal.success(res.message || 'Satuan berhasil diperbarui.');
        } else {
          const nextNumber = table.rows().count() + 1;
          const actionBtns = `
            <button type="button"
                    class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect edit-btn"
                    data-id="${data.id}" data-name="${esc(data.nama_satuan)}" title="Edit">
              <i class="ri-pencil-line ri-20px text-primary"></i>
            </button>
            <button type="button"
                    class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect delete-btn"
                    data-id="${data.id}" data-name="${esc(data.nama_satuan)}" title="Hapus">
              <i class="ri-delete-bin-7-line ri-20px"></i>
            </button>
          `;

          const newRowNode = table.row.add([
            `<div class="text-center">${nextNumber}</div>`,
            `<span class="nama-satuan">${esc(data.nama_satuan)}</span>`,
            `<div class="text-center">${actionBtns}</div>`
          ]).draw(false).node();

          $(newRowNode).attr('data-row-id', data.id);
          useSwal.success(res.message || 'Satuan berhasil ditambahkan.');
        }
      },
      error: function (xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
          const errs = xhr.responseJSON.errors;
          if (errs.nama_satuan && errs.nama_satuan.length) {
            $errNama.removeClass('d-none').text(errs.nama_satuan[0]);
          }
          useSwal.warn('Periksa isian Anda.');
        } else {
          useSwal.error('Terjadi kesalahan server.');
        }
      },
      complete: function () {
        $('#submitBtn').attr('disabled', false);
      }
    });
  });
  $(document).on('click', '.delete-btn, .delete-btn *', function (ev) {
    const btn = ev.target.closest('.delete-btn');
    if (!btn) return;

    const id   = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const deleteUrl = `${updateUrlBase}/${id}`;

    useSwal
      .confirm(`Satuan "${name}" akan dihapus permanen!`, {
        title: 'Anda Yakin?',
        confirmText: 'Ya, Hapus!',
        cancelText: 'Batal'
      })
      .then((ok) => {
        if (!ok) return;

        $.ajax({
          url: deleteUrl,
          type: 'POST',
          data: { _method: 'DELETE' },
          success: function (res) {
            if (res && res.status === 'ok') {
              const $row = $tbody.find(`tr[data-row-id="${id}"]`);
              table.row($row).remove().draw(false);
              table.rows().every(function (rowIdx) {
                const cell = this.cell(rowIdx, 0).node();
                cell.innerHTML = `<div class="text-center">${rowIdx + 1}</div>`;
              });

              useSwal.success(res.message || 'Satuan berhasil dihapus.');
            } else {
              useSwal.error('Tidak dapat menghapus data.');
            }
          },
          error: function () {
            useSwal.error('Terjadi kesalahan saat menghapus.');
          }
        });
      });
  });
});
