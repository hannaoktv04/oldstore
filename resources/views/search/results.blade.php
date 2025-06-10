@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h4 class="mb-4">Hasil Pencarian untuk: <strong>{{ $keyword }}</strong></h4>

  @if($produk->isEmpty())
    <p>Tidak ada barang ditemukan.</p>
  @else
    <div class="row">
      @foreach($produk as $item)
        <div class="col-md-3 mb-5">
            <a href="{{ route('produk.show', ['id' => $item->id]) }}" class="text-decoration-none">
                <div class="card h-100 card-3d shadow-sm">
                    <img src="{{ asset('assets/img/products/' . $item->image) }}" class="card-img-top" alt="{{ $item->nama_barang }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->nama_barang }}</h5>
                        <p class="card-text small text-muted mb-1">{{ $item->kategori }}</p>
                        <p class="card-text"><strong>{{ number_format($item->stok_minimum, 0) }}</strong> Tersisa</p>
                    </div>
                </div>
            </a>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
