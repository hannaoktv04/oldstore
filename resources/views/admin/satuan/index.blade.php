@extends('layouts.admin')
@section('title', 'Master Satuan')

@section('content')
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Satuan</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#satuanModal">
                    <i class="ri ri-add-line"></i>Tambah Satuan
                </button>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="table-responsive">
                    <table id="satuanTable" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Satuan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($satuan as $index => $s)
                                <tr>
                                    <td class="text-center col-md-1">{{ $index + 1 }}</td>
                                    <td>{{ $s->nama_satuan }}</td>
                                    <td class="text-center col-md-2">
                                        <button
                                            class="btn btn-sm btn-icon btn-text-warning rounded-pill waves-effect edit-btn"
                                            data-id="{{ $s->id }}" data-name="{{ $s->nama_satuan }}"
                                            data-bs-toggle="modal" data-bs-target="#satuanModal">
                                            <i class="ri-pencil-line ri-20px"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect delete-btn"
                                            data-id="{{ $s->id }}" data-name="{{ $s->nama_satuan }}">
                                            <i class="ri-delete-bin-7-line ri-20px"></i>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="satuanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"><span><i class="bi bi-plus"></i></span> Tambah Satuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="satuanForm" method="POST" data-store-url="{{ route('admin.satuan.store') }}"
                    data-update-url="{{ url('admin/satuan') }}">
                    @csrf
                    <input type="hidden" id="satuanId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_satuan" class="form-label">Nama Satuan</label>
                            <input type="text" class="form-control" id="nama_satuan" name="nama_satuan" required>
                            @error('nama_satuan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#satuanTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
                }
            });

            const satuanForm = $('#satuanForm');
            const deleteForm = $('#deleteForm');

            $('#satuanModal').on('show.bs.modal', function() {
                satuanForm[0].reset();
                $('#satuanId').val('');
                $('#modalTitle').text('Tambah Satuan');

                const storeUrl = satuanForm.data('store-url');
                satuanForm.attr('action', storeUrl);
                satuanForm.find('input[name="_method"]').remove();
            });

            $(document).on('click', '.edit-btn', function() {
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
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const deleteUrlTemplate = "{{ route('admin.satuan.destroy', ['satuan' => ':id']) }}";
                const deleteUrl = deleteUrlTemplate.replace(':id', id);

                Swal.fire({
                    title: 'Anda Yakin?',
                    text: `Satuan "${name}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;
                        form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
