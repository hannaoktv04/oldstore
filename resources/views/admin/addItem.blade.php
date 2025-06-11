@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <h5 class="mb-4">Add New Item</h5>

        {{-- Main Content --}}
        <div class="col-md-12">
            <form action="#" method="POST" enctype="multipart/form-data">
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
                                    <option value="pcs">pcs</option>
                                    <option value="lembar">lembar</option>
                                    <option value="meter">meter</option>
                                    <option value="liter">liter</option>
                                    <option value="box">box</option>
                                    <option value="kg">kg</option>
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
                            <div class="mb-3">
                                <label for="photo_Item" class="form-label">Product Images</label>
                                <input type="file" name="photo_Item[]" id="photo_Item" class="form-control" multiple>
                                <small class="text-muted">You can upload multiple images (main, thumbnail, detail)</small>
                            </div>
                            {{-- Preview Images (optional) --}}
                            <div class="d-flex gap-2 mt-2" id="previewContainer">
                                <!-- Preview thumbnails appear here (JavaScript needed) -->
                            </div>
                        </div>
                        {{-- Category Selection --}}
                        <div class="card p-4 mb-3 shadow-sm">
                            <h5>Category</h5>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Item Category</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">Select Category</option>
                                    @foreach(App\Models\Category::all() as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->categori_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                Add Category
                            </button>
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
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoryForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categori_name" class="form-label">Category Name</label>
                        <input type="text" name="categori_name" id="categori_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">✓ Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Script --}}
<script src="{{ asset('assets/js/addCategory.js') }}"></script>
<script>
    // Optional: image preview
    document.getElementById('photo_Item')?.addEventListener('change', function(e) {
        const previewContainer = document.getElementById('previewContainer');
        previewContainer.innerHTML = '';

        Array.from(e.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('img-thumbnail');
                img.style.width = '80px';
                img.style.height = '80px';
                img.style.objectFit = 'cover';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
