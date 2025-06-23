@extends('layouts.admin')

@section('title', 'Edit Item')

@section('content')
<div class="container mt-4">
    <h5 class="mb-4">Edit Item</h5>
    @foreach (['success'=>'success','error'=>'danger'] as $k=>$t)
        @if(session($k))
            <div class="alert alert-{{ $t }} alert-dismissible fade show" role="alert">
                {{ session($k) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card shadow-sm p-4 mb-4">
            <h6 class="fw-semibold mb-3">Foto Item</h6>
            <div class="mb-3 d-flex flex-wrap gap-2">
                <img src="{{ asset('storage/'.$item->photo_url) }}" class="rounded" style="width:72px;height:72px;object-fit:cover">
                <label class="upload-box upload-trigger">
                    <input type="file" name="photo_Item[]" class="d-none" multiple accept="image/*">
                    <div class="upload-placeholder"><i class="bi bi-image fs-2 text-secondary"></i></div>
                </label>
            </div>
            <small class="text-muted">Tambah hingga 5 gambar ( JPEG/PNG ≤ 2 MB ).</small>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h6 class="fw-semibold mb-3">Informasi Item</h6>

            <div class="mb-3">
                <label class="form-label">Nama Item</label>
                <input type="text" name="nama_barang" class="form-control"
                       value="{{ old('nama_barang', $item->nama_barang) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Kode Item</label>
                <input type="text" name="kode_barang" class="form-control text-uppercase"
                       value="{{ old('kode_barang', $item->kode_barang) }}" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Satuan</label>
                    <select name="satuan" class="form-select" required>
                        @foreach(['pcs','buah','rim','pack','dus','botol'] as $sat)
                            <option value="{{ $sat }}" {{ $item->satuan === $sat ? 'selected' : '' }}>
                                {{ ucfirst($sat) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        @foreach(App\Models\Category::orderBy('categori_name')->get() as $cat)
                            <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->categori_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="form-control" maxlength="500" required>{{ old('deskripsi', $item->deskripsi) }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-3 mb-4">
            <a href="{{ route('admin.items') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save2 me-1"></i> Update
            </button>
        </div>
    </form>

    @include('admin.modals.cropperImg')
</div>
@endsection
