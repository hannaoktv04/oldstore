$(document).ready(function () {
    $('#satuanTable').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
        }
    });

    const satuanForm = $('#satuanForm');
    const deleteForm = $('#deleteForm');

    $('#satuanModal').on('show.bs.modal', function () {
        satuanForm[0].reset();
        $('#satuanId').val('');
        $('#modalTitle').text('Tambah Satuan');

        const storeUrl = satuanForm.data('store-url');
        satuanForm.attr('action', storeUrl);
        satuanForm.find('input[name="_method"]').remove();
    });

    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#satuanId').val(id);
        $('#nama_satuan').val(name);
        $('#modalTitle').text('Edit Satuan');

        const updateUrl = satuanForm.data('update-url') + '/' + id;
        satuanForm.attr('action', updateUrl);

        if (!satuanForm.find('input[name="_method"]').length) {
            satuanForm.append('<input type="hidden" name="_method" value="PUT">');
        }
    });

    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        const deleteUrl = deleteForm.data('delete-url') + '/' + id;
        deleteForm.attr('action', deleteUrl);
    });
});
