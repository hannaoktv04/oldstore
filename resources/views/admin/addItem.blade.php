@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <h5 class="mb-4">Add New Item</h5>

            {{-- Flash Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show fadeout" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show fadeout" role="alert">
                    {{ session('error') }}
                </div>
            @endif


            {{-- Main Content --}}
            <div class="col-md-12">
                <form action="{{ route('admin.storeItem') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-7">
                            {{-- Item Information --}}
                            <div class="card p-4 mb-3 shadow-sm">
                                <h5>Item Information</h5>
                                <div class="mb-3">
                                    <label for="nama_barang" class="form-label">Name Item</label>
                                    <input type="text" name="nama_barang" id="nama_barang" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="kode_barang" class="form-label">Kode Item</label>
                                    <input type="text" name="kode_barang" id="kode_barang" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stock</label>
                                    <input type="number" name="stok" id="stok" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="satuan" class="form-label">Satuan</label>
                                    <select name="satuan" id="satuan" class="form-select" required>
                                        <option value="">Select Satuan</option>
                                        <option value="pcs">Pcs</option>
                                        <option value="buah">Buah</option>
                                        <option value="rim">Rim</option>
                                        <option value="pack">Pack</option>
                                        <option value="dus">Dus</option>
                                        <option value="botol">Botol</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi Product</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card p-4 mb-3 shadow-sm">
                                <h5>Upload Image</h5>
                                <div class="d-flex flex-wrap gap-2" id="imageUploadWrapper">
                                    @for ($i = 0; $i < 5; $i++)
                                        <div class="upload-box"onclick="triggerFileInput({{ $i }})">
                                            <img id="preview{{ $i }}" src="" class="img-thumbnail d-none" >
                                            <span class="bi bi-camera" id="placeholder{{ $i }}"></span>
                                            <input type="file" name="photo_Item[]" class="d-none" accept="image/*" onchange="previewImage(this, {{ $i }})" id="fileInput{{ $i }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>


                            {{-- Category --}}
                            <div class="card p-4 mb-3 shadow-sm">
                                <h5>Category</h5>
                                <div class="mb-3">
                                    <select name="category_id" id="category_id" class="form-select">
                                        <option value="">Select Category</option>
                                        @foreach (App\Models\Category::all() as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->categori_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-inline-block">
                                    <button type="button" class="btn btn-success btn-sm rounded-pill py-2 px-3"
                                        data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                        Add New Category
                                    </button>
                                </div>


                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">Add Item</button>
                                <a href="#" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Add Category --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-5">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categori_name" class="form-label">Category Name</label>
                            <input type="text" name="categori_name" id="categori_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-d" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"> Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- Script --}}
<script>
    // Auto hide flash message
    window.addEventListener('DOMContentLoaded', () => {
        const alerts = document.querySelectorAll('.fadeout');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        });
    });

function triggerFileInput(index) {
    document.getElementById(`fileInput${index}`).click();
}

function previewImage(input, index) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById(`preview${index}`);
            const placeholder = document.getElementById(`placeholder${index}`);
            img.src = e.target.result;
            img.classList.remove('d-none');
            placeholder.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }
}
</script>
