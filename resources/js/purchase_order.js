document.addEventListener('DOMContentLoaded', function () {
    $('table.table').DataTable();

    document.querySelectorAll('.delete_data').forEach(el => {
        el.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            if (confirm('Hapus PO secara permanen?')) {
                alert('Dummy delete for PO ID: ' + id);
            }
        });
    });
});
