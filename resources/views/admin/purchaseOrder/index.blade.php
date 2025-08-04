@extends('layouts.admin')

@section('title', 'Daftar Purchase Order')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar Pengajuan Barang</h4>
                <a href="{{ route('admin.purchase_orders.create') }}" class="btn btn-flat btn-primary ">
                    <span class="ri ri-add-line"> Create New</span>
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="poTable" class="table table-bordered w-100 table-hover">
                        <colgroup>
                            <col width="5%">
                            <col width="15%">
                            <col width="20%">
                            <col width="15%">
                            <col width="10%">
                            <col width="30%">
                            <col width="5%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode PO</th>
                                <th>Jumlah Item</th>
                                <th>Status</th>
                                <th>Admin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchaseOrders as $index => $po)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->tanggal_po)->format('d M Y') }}</td>
                                    <td>{{ $po->nomor_po }}</td>
                                    <td class="text-end">{{ $po->details->count() }}</td>
                                    <td class="text-center">
                                        @switch($po->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @break

                                            @case('submitted')
                                                <span class="badge bg-warning text-dark">Submitted</span>
                                            @break

                                            @case('received')
                                                <span class="badge bg-success">Received</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>{{ $po->creator->nama ?? '-' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-icon rounded-pill waves-effect" type="button" data-bs-strategy="fixed"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-2-line fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if ($po->status === 'draft')
                                                <li>
                                                    <form action="{{ route('admin.purchase_orders.submit', $po->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="ri-send-plane-line text-primary"></i> Submit
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.purchase_orders.edit', $po->id) }}">
                                                        <i class="ri-edit-2-line text-primary"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.purchase_orders.show', $po->id) }}">
                                                        View
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.purchase_orders.destroy', $po->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item text-danger delete_data"
                                                            style="cursor: pointer;">
                                                            <i class="ri-delete-bin-7-line"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            @elseif ($po->status === 'submitted')
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.purchase_orders.receive', $po->id) }}">
                                                        Receive
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.purchase_orders.show', $po->id) }}">
                                                        View
                                                    </a>
                                                </li>
                                            @elseif ($po->status === 'received')
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.purchase_orders.show', $po->id) }}">
                                                        View
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada data purchase order.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endsection

        @push('scripts')
            <script>
                $(document).ready(function() {
                    $('#poTable').DataTable({
                        responsive: true,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data yang tersedia",
                            zeroRecords: "Data tidak ditemukan",
                            paginate: {
                                previous: "<",
                                next: ">",
                            }
                        },
                        columnDefs: [{
                            orderable: false,
                            targets: [4, 6]
                        }],
                        order: [
                            [1, 'desc']
                        ]
                    });
                });
            </script>
        @endpush
