@extends('peri::layouts.admin')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="mb-0">Daftar Satuan</h4>
      <button id="addSatuanBtn" type="button" class="btn btn-primary">
        <i class="ri ri-add-line"></i> Tambah Satuan
      </button>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table id="satuanTable" class="table table-hover w-100">
          <thead>
            <tr>
              <th class="text-center">No</th>
              <th>Nama Satuan</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody id="satuanTbody">
            @foreach ($satuan as $index => $s)
              <tr data-row-id="{{ $s->id }}">
                <td class="text-center col-md-1">{{ $index + 1 }}</td>
                <td class="nama-satuan">{{ $s->nama_satuan }}</td>
                <td class="text-center col-md-2">
                  <button type="button"
                          class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect edit-btn"
                          data-id="{{ $s->id }}"
                          data-name="{{ $s->nama_satuan }}"
                          title="Edit">
                    <i class="ri-pencil-line ri-20px text-primary"></i>
                  </button>

                  <button type="button"
                          class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect delete-btn"
                          data-id="{{ $s->id }}"
                          data-name="{{ $s->nama_satuan }}"
                          title="Hapus">
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
</div>

<div class="modal fade" id="satuanModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Tambah Satuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="satuanForm"
            method="POST"
            action="{{ route('admin.satuan.store') }}"
            data-store-url="{{ route('admin.satuan.store') }}"
            data-update-url-base="{{ url('admin/satuan') }}">
        @csrf
        <input type="hidden" id="satuanId" name="id">
        <div class="modal-body">
          <div class="mb-3">
            <label for="nama_satuan" class="form-label">Nama Satuan</label>
            <input type="text" class="form-control" id="nama_satuan" name="nama_satuan" required>
            <div class="invalid-feedback d-block d-none" id="namaSatuanError"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/peri/crud-satuan.js') }}"></script>
@endpush
