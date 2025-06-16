@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
<style>
    .upload-box {
        width: 90px;
        height: 90px;
        border: 1px dashed #ccc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .upload-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .upload-box .bi-camera {
        font-size: 1.5rem;
        color: #888;
    }
</style>

<div class="container mt-4">
    <div class="row">
        <h5 class="mb-4">Edit Item</h5>

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
            <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-7">
                        <div class="card p-4 mb-3 shadow-sm">
                            <h5>Item Information</h5>
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Name Item</label>
                                <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="{{ $item->nama_barang }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="kode_barang" class="form-label">Kode Item</label>
                                <input type="text" name="kode_barang" id="kode_barang" class="form-control" value="{{ $item->kode_barang }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stock</label>
                                <input type="number" name="stok" id="stok" class="form-control" value="{{ $item->stok_minimum }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <select name="satuan" id="satuan" class="form-select" required>
                                    <option value="">Select Satuan</option>
                                    @foreach (["pcs","buah","rim","pack","dus","botol"] as $unit)
                                        <option value="{{ $unit }}" {{ $item->satuan === $unit ? 'selected' : '' }}>{{ ucfirst($unit) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Product</label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required>{{ $item->deskripsi }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card p-4 mb-3 shadow-sm">
                            <h5>Edit Gambar</h5>
                            <div class="d-flex flex-wrap gap-2" id="imageUploadWrapper">
                                @for ($i = 0; $i < 5; $i++)
                                    @php
                                        $imagePath = match ($i) {
                                            0 => $item->photo?->image,
                                            1 => $item->photo?->img_xl,
                                            2 => $item->photo?->img_l,
                                            3 => $item->photo?->img_m,
                                            4 => $item->photo?->img_s,
                                        };
                                    @endphp
                                    <div class="upload-box" onclick="triggerFileInput({{ $i }})">
                                        @if($imagePath)
                                            <img id="preview{{ $i }}" src="{{ asset('storage/' . $imagePath) }}">
                                        @else
                                            <span class="bi bi-camera" id="placeholder{{ $i }}"></span>
                                        @endif
                                        <input type="file" name="photo_Item[]" class="d-none" accept="image/*" onchange="previewImage(this, {{ $i }})" id="fileInput{{ $i }}">
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div class="card p-4 mb-3 shadow-sm">
                            <h5>Category</h5>
                            <div class="mb-3">
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->categori_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">Save Edit</button>
                            <a href="{{ route('admin.items') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
                if (placeholder) placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
