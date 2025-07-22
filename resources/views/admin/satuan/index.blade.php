@extends('layouts.admin')
@section('title', 'Master Satuan')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Satuan</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#satuanModal">
                <i class="bi bi-plus me-2"></i>Tambah Satuan
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table id="satuanTable" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($satuan as $index => $s)
                        <tr>
                            <td class="text-center col-md-1">{{ $index + 1 }}</td>
                            <td>{{ $s->nama_satuan }}</td>
                            <td class="text-center col-md-2">
                                <div class="d-flex justify-content-center gap-2">
                                    <button
                                        class="btn btn-sm btn-outline-warning edit-btn d-flex align-items-center justify-content-center"
                                        data-id="{{ $s->id }}" data-name="{{ $s->nama_satuan }}" data-bs-toggle="modal"
                                        data-bs-target="#satuanModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger delete-btn d-flex align-items-center justify-content-center"
                                        data-id="{{ $s->id }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
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
    <div class="modal-dialog">
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

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus satuan ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display:inline" data-delete-url="{{ url('admin/satuan') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/satuan.js') }}"></script>
@endpush
