@extends('peri::layouts.admin')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar Sesi Stock Opname</h4>
                <a href="{{ route('admin.stock_opname.create') }}" class="btn btn-primary">
                    <i class="ri ri-add-line"> </i> Mulai Sesi
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="opnameTable">
                        <thead>
                            <tr class="text-center">
                                <th>Periode</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Admin</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sessions as $session)
                                <tr>
                                    <td>{{ $session->periode_bulan }}</td>
                                    <td>{{ $session->tanggal_mulai->format('Y-m-d') }}</td>
                                    <td>{{ $session->tanggal_selesai ? $session->tanggal_selesai->format('Y-m-d') : '-' }}
                                    </td>
                                    <td>{{ $session->opener->nama ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $session->status === 'aktif' ? 'success' : ($session->status === 'selesai' ? 'secondary' : 'warning') }}">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    </td>
                                    <td class="text-wrap text-start">{{ $session->catatan }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-icon rounded-pill waves-effect" type="button"
                                            data-bs-strategy="fixed" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-2-line fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if ($session->status === 'aktif')
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.stock_opname.edit', $session) }}">
                                                        Edit
                                                    </a>
                                                </li>
                                                 <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.stock_opname.endSession', $session) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="tanggal_selesai"
                                                            value="{{ now()->toDateString() }}">
                                                        <button type="submit" class="dropdown-item">
                                                            </i>Tutup Sesi
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.stock_opname.destroy', $session) }}"
                                                        method="POST" class="form-delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger btn-delete">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            @elseif ($session->status === 'menunggu')
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.stock_opname.edit', $session) }}">
                                                        Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.stock_opname.destroy', $session) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger btn-delete">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.stock_opname.show', $session) }}">
                                                        Lihat
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/peri/crud-stockOpname.js') }}"></script>
@endpush