@extends('layouts.admin')

@section('content')
<div class="container py-4 position-relative">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Daftar Kategori</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                <i class="ri-add-line me-1"></i> Tambah Kategori
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="categoryTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center sorting_disabled" style="width: 5%;"><input type="checkbox"
                                    id="selectAllCategory" class="form-check-input"></th>
                            <th>Nama Kategori</th>
                            <th class="text-center" style="width: 15%;">Jumlah Item</th>
                            <th class="text-center sorting_disabled" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                        <tr>
                            <td class="text-center"><input type="checkbox" value="{{ $category->id }}"
                                    class="form-check-input category-checkbox"></td>
                            <td>{{ $category->categori_name }}</td>
                            <td class="text-center">{{ $category->items_count }}</td>
                            <td class="text-center">
                                <button
                                    class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect btnEditKategori"
                                    data-name="{{ $category->categori_name }}"
                                    data-action="{{ route('admin.categories.update', $category->id) }}"
                                    data-bs-toggle="modal" data-bs-target="#modalEditKategori" title="Edit">
                                    <i class="ri-pencil-line ri-20px text-primary"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect btnHapusKategori"
                                    data-nama="{{ $category->categori_name }}"
                                    data-action="{{ route('admin.categories.destroy', $category->id) }}" title="Hapus">
                                    <i class="ri-delete-bin-7-line ri-20px"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-bottom" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
        id="categoryBulkActionOffcanvas">
        <div class="offcanvas-body">
            <div class="container d-flex flex-wrap justify-content-between align-items-center">
                <div class="text-dark">
                    <span id="selectedCountCategory" class="fw-semibold">0</span> kategori dipilih.
                </div>
                <div>
                    <button type="button" class="btn btn-danger" id="bulkDeleteBtn">Hapus Terpilih
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Kategori Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newCategory" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" name="categori_name" id="newCategory" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
        <form id="formEditKategori" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="editCategoryName" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="editCategoryName" name="categori_name" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
        </div>

    </div>
</div>
@endsection


@push('scripts')
<script>
    $(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const table = $("#categoryTable").DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [0, 3]
                }],
                order: [
                    [1, "asc"]
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "_MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        previous: "<",
                        next: ">"
                    }
                }
            });
            const categoryBulkActionOffcanvasEl = document.getElementById('categoryBulkActionOffcanvas');
            const bulkOffcanvas = new bootstrap.Offcanvas(categoryBulkActionOffcanvasEl);
            const tableCard = $('.card');

            function checkOffcanvasPosition() {
                if (!categoryBulkActionOffcanvasEl.classList.contains('show')) return;
                const cardBottom = tableCard[0].getBoundingClientRect().bottom;
                const windowHeight = window.innerHeight;

                if (cardBottom < windowHeight) {
                    $(categoryBulkActionOffcanvasEl).addClass('offcanvas-docked');
                } else {
                    $(categoryBulkActionOffcanvasEl).removeClass('offcanvas-docked');
                }
            }
            $(window).on('scroll resize', checkOffcanvasPosition);

            function updateBulkActionUI() {
                const checkedCount = $(".category-checkbox:checked").length;
                $("#selectedCountCategory").text(`${checkedCount} `);

                if (checkedCount > 0) {
                    bulkOffcanvas.show();

                } else {
                    bulkOffcanvas.hide();
                    $(categoryBulkActionOffcanvasEl).removeClass('offcanvas-docked');
                }
                setTimeout(checkOffcanvasPosition, 300);
            }

            $("#selectAllCategory").on("click", function() {
                $(".category-checkbox").prop("checked", this.checked).trigger('change');
            });

            $("#categoryTable").on("change", ".category-checkbox", function() {
                if (!this.checked) {
                    $("#selectAllCategory").prop("checked", false);
                } else if ($(".category-checkbox:checked").length === $(".category-checkbox").length) {
                    $("#selectAllCategory").prop("checked", true);
                }
                updateBulkActionUI();
            });

            table.on("draw", function() {
                const allCheckboxes = $(".category-checkbox");
                const checkedCheckboxes = allCheckboxes.filter(":checked");
                $("#selectAllCategory").prop("checked", allCheckboxes.length > 0 && allCheckboxes.length ===
                    checkedCheckboxes.length);
                updateBulkActionUI();
            });

            $("#categoryTable").on("click", ".btnEditKategori", function() {
                $("#formEditKategori").attr("action", $(this).data("action"));
                $("#editCategoryName").val($(this).data("name"));
            });

            $("#categoryTable").on("click", ".btnHapusKategori", function() {
                $("#formHapusKategori").attr("action", $(this).data("action"));
                $("#namaKategoriDihapus").text($(this).data("nama"));
            });
            $("#categoryTable").on("click", ".btnHapusKategori", function(e) {
                e.preventDefault();
                const actionUrl = $(this).data("action");
                const categoryName = $(this).data("nama");

                Swal.fire({
                    title: 'Anda Yakin?',
                    text: `Kategori "${categoryName}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = actionUrl;
                        form.innerHTML = `
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            $('#bulkDeleteBtn').on('click', function() {
                const selected = $(".category-checkbox:checked");
                if (selected.length === 0) {
                    Swal.fire('Tidak Ada yang Dipilih',
                        'Silakan pilih setidaknya satu kategori untuk dihapus.', 'info');
                    return;
                }

                Swal.fire({
                    title: 'Anda Yakin?',
                    text: `Anda akan menghapus ${selected.length} kategori yang dipilih.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('admin.categories.bulkDelete') }}";

                        let inputs = `<input type="hidden" name="_token" value="${csrfToken}">`;
                        selected.each(function() {
                            inputs +=
                                `<input type="hidden" name="selected_categories[]" value="${$(this).val()}">`;
                        });
                        form.innerHTML = inputs;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
</script>
@endpush
