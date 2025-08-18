@extends('peri::layouts.admin')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex flex-wrap justify-content-start gap-3">
                    <div class="product_category">
                        <select id="filter-kategori" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->categori_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="product_status">
                        <select id="filter-status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="arsip">Diarsip</option>
                        </select>
                    </div>
                    <div class="product_stock">
                        <select id="filter-stok" class="form-select form-select-sm">
                            <option value="">Semua Stok</option>
                            <option value="aman">Aman</option>
                            <option value="menipis">Menipis</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i>Tambah Barang
                    </a>
                </div>
            </div>
            <hr>
            <div class="table-responsive px-3 mb-2">
                <table class="table table-hover" id="itemTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th><input type="checkbox" id="select-all-checkbox" class="form-check-input"></th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Stok Min.</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="offcanvas offcanvas-bottom" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
            id="bulkActionsOffcanvas" aria-labelledby="bulkActionsOffcanvasLabel">
            <div class="offcanvas-body">
                <div class="container d-flex flex-wrap justify-content-between align-items-center">
                    <div class="text-dark">
                        <span id="selected-items-count" class="fw-semibold">0</span> item terpilih.
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-danger" id="bulk-delete">Hapus</button>
                        <button class="btn btn-warning" id="bulk-archive">Arsipkan</button>
                        <button class="btn btn-success" id="bulk-unarchive">Aktifkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Detail Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailModalBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#itemTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                var data = row.data();
                                return 'Details for ' + data.produk;
                            }
                        }),
                        renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                            tableClass: 'table'
                        })
                    }
                },
                ajax: {
                    url: '{!! route('admin.items.data') !!}',
                    data: function(d) {
                        d.kategori = $('#filter-kategori').val();
                        d.status = $('#filter-status').val();
                        d.stok = $('#filter-stok').val();
                    }
                },
                columns: [{
                        data: null,
                        defaultContent: '',
                        orderable: false,
                        searchable: false,
                        className: 'control'
                    },
                    {
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'produk',
                        name: 'nama_barang'
                    },
                    {
                        data: 'kategori',
                        name: 'category.categori_name'
                    },
                    {
                        data: 'stok',
                        name: 'stocks.qty',
                        className: 'text-center'
                    },
                    {
                        data: 'stok_minimum',
                        name: 'stok_minimum',
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        name: 'state.is_archived',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [2, 'asc']
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "_MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    zeroRecords: "Data tidak ditemukan"
                }
            });

            $('#filter-kategori, #filter-status, #filter-stok').on('change', function() {
                table.draw();
            });

            function reloadTable() {
                table.ajax.reload(null, false);
            }

            $(document).on('click', '.btn-detail', function() {
                var itemId = $(this).data('id');
                $('#detailModalBody').html(
                    '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                );
                $('#detailModal').modal('show');
                $.get('/admin/items/' + itemId, function(data) {
                    var photoHtml = '';
                    if (data.gallery_urls && data.gallery_urls.length > 0) {
                        photoHtml = '<div class="d-flex flex-wrap gap-2 mb-3">';
                        data.gallery_urls.forEach(function(url) {
                            photoHtml += `
                                <a href="${url}" target="_blank">
                                    <img src="${url}" class="img-thumbnail rounded-4" alt="Gambar Produk" style="height: 100px; width: 100px; object-fit: cover;">
                                </a>
                            `;
                        });
                        photoHtml += '</div>';
                    } else {
                        photoHtml = '<p>Tidak ada gambar.</p>';
                    }
                    var statusBadge = data.state && data.state.is_archived ?
                        '<span class="badge bg-label-danger">Diarsip</span>' :
                        '<span class="badge bg-label-success">Aktif</span>';

                    var html = `
                ${photoHtml}
                <table class="table">
                    <tr><th style="width: 30%;">Nama Produk</th><td>${data.nama_barang}</td></tr>
                    <tr><th>Kategori</th><td>${data.category ? data.category.categori_name : 'N/A'}</td></tr>
                    <tr><th>Stok Saat Ini</th><td>${data.stocks ? data.stocks.qty : 0} ${data.satuan ? data.satuan.nama_satuan : ''}</td></tr>
                    <tr><th>Stok Minimum</th><td>${data.stok_minimum}</td></tr>
                    <tr><th>Status</th><td>${statusBadge}</td></tr>
                    <tr><th>Deskripsi</th><td>${data.deskripsi}</td></tr>
                </table>
            `;
                    $('#detailModalBody').html(html);
                });
            });

            $(document).on('click', '.btn-delete', function() {
                var itemId = $(this).data('id');
                var itemName = $(this).data('nama');

                Swal.fire({
                    title: 'Anda Yakin?',
                    text: `Item "${itemName}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Hapus!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/items/' + itemId,
                            type: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Terhapus!', response.message, 'success');
                                    reloadTable();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-archive', function() {
                var itemId = $(this).data('id');
                $.post('/admin/items/' + itemId + '/toggle-archive', function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        reloadTable();
                    }
                });
            });
            var bulkActionsOffcanvasElement = document.getElementById('bulkActionsOffcanvas');
            var bulkOffcanvas = new bootstrap.Offcanvas(bulkActionsOffcanvasElement);

            $('#select-all-checkbox').on('click', function() {
                $('.item-checkbox').prop('checked', this.checked);
                toggleBulkActions();
            });

            $(document).on('click', '.item-checkbox', function() {
                if ($('.item-checkbox:checked').length === $('.item-checkbox').length) {
                    $('#select-all-checkbox').prop('checked', true);
                } else {
                    $('#select-all-checkbox').prop('checked', false);
                }
                toggleBulkActions();
            });

            function toggleBulkActions() {
                var checkedCount = $('.item-checkbox:checked').length;
                $('#selected-items-count').text(checkedCount);
                if (checkedCount > 0) {
                    bulkOffcanvas.show();
                } else {
                    bulkOffcanvas.hide();
                }
            }

            function runBulkAction(action) {
                var selectedIds = [];
                $('.item-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                var actionText = (action === 'hapus') ? 'menghapus' : (action === 'arsipkan' ? 'mengarsipkan' :
                    'mengaktifkan');

                Swal.fire({
                    title: 'Anda Yakin?',
                    text: `Anda akan ${actionText} ${selectedIds.length} item.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.items.bulkAction') }}',
                            type: 'POST',
                            data: {
                                ids: selectedIds,
                                action: action
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    reloadTable();
                                    $('#select-all-checkbox').prop('checked', false);
                                    toggleBulkActions();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            }

            $('#bulk-delete').on('click', function() {
                runBulkAction('hapus');
            });
            $('#bulk-archive').on('click', function() {
                runBulkAction('arsipkan');
            });
            $('#bulk-unarchive').on('click', function() {
                runBulkAction('aktifkan');
            });
        });
    </script>
@endpush
