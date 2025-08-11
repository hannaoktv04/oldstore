@extends('layouts.admin')

@section('title', 'Create Purchase Order')

@section('content')
<div class="card px-1 py-3">
    <div class="card-header">
        <h4 class="card-title">Create New Purchase Order</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.purchase_orders.store') }}" method="POST">
            @csrf

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="fw-semibold">Kode PO</label>
                    <input type="text" name="nomor_po" class="form-control bg-light text-muted" value="{{ $nomor_po }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="fw-semibold">Tanggal PO</label>
                    <input type="date" name="tanggal_po" class="form-control" value="{{ old('tanggal_po') }}" required>
                </div>
            </div>

            <hr>
            <h5 class="mt-3">Tambah Item</h5>
            <div class="row align-items-end mb-3">
                <div class="col-md-4">
                    <label class="fw-semibold">Item</label>
                    <select class="form-control" id="item-select">
                        <option value="">Pilih Item</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" data-kode="{{ $item->kode_barang }}"
                                data-nama="{{ $item->nama_barang }}" data-satuan="{{ $item->satuan->nama_satuan }}" data-stok="{{ $item->stocks->qty ?? 0}}">
                                {{ $item->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="fw-semibold">Satuan</label>
                    <input type="text" class="form-control" id="unit" readonly>
                </div>
                <div class="col-md-2">
                    <label class="fw-semibold">stok akhir</label>
                    <input type="number" class="form-control" id="stok-akhir" readonly>
                </div>
                <div class="col-md-2">
                    <label class="fw-semibold">Qty</label>
                    <input type="number" class="form-control" id="qty" step="0.01" min="0.01">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-lg btn-outline-primary w-full" id="add-item">
                        <span class="bi bi-plus"></span> Tambah Item
                    </button>
                </div>
            </div>

            <table class="table table-bordered" id="po-table">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Kode Item</th>
                        <th class="text-center">Nama Item</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const itemSelect = document.getElementById("item-select");
    const unitInput = document.getElementById("unit");
    const qtyInput = document.getElementById("qty");
    const addItemBtn = document.getElementById("add-item");
    const poTable = document.getElementById("po-table");
    const tableBody = document.getElementById("table-body");

    if (!itemSelect || !unitInput || !qtyInput || !addItemBtn || !poTable || !tableBody) return;

    const dataTable = $.fn.DataTable.isDataTable(poTable)
        ? $(poTable).DataTable()
        : $(poTable).DataTable({
              searching: false,
              paging: false,
              info: false,
              ordering: false,
              language: {
                  emptyTable: "Belum ada item ditambahkan.",
              },
          });
    itemSelect.addEventListener("change", function () {
        const selected = this.options[this.selectedIndex];
        unitInput.value = selected?.getAttribute("data-satuan") || "";

        const stokAkhirInput = document.getElementById("stok-akhir");
    if (stokAkhirInput) {
        stokAkhirInput.value = selected?.getAttribute("data-stok") || 0;
    }
    });

    addItemBtn.addEventListener("click", function () {
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const itemId = selectedOption?.value;
        const qty = parseFloat(qtyInput.value);

        if (!itemId || itemId === "") {
            alert("Silakan pilih item terlebih dahulu!");
            return;
        }

        if (isNaN(qty) || qty <= 0) {
            alert("Qty harus diisi dan lebih dari 0!");
            return;
        }

        const isDuplicate = Array.from(document.querySelectorAll('input[name="item_id[]"]')).some(
            (input) => input.value === itemId
        );
        if (isDuplicate) {
            alert("Item ini sudah ditambahkan!");
            return;
        }

        const itemCode = selectedOption.getAttribute("data-kode");
        const itemName = selectedOption.getAttribute("data-nama");
        const satuan = selectedOption.getAttribute("data-satuan");

        dataTable.row
            .add([
                `<input type="hidden" name="item_code[]" value="${itemCode}">${itemCode}`,
                `<input type="hidden" name="item_id[]" value="${itemId}"><input type="hidden" name="item_name[]" value="${itemName}">${itemName}`,
                `<input type="hidden" name="unit[]" value="${satuan}">${satuan}`,
                `<input type="number" name="qty[]" class="form-control" value="${qty}" step="0.01" min="0.01" required>`,
                `<button type="button" class="btn btn-lg btn-danger remove-row">Hapus</button>`,
            ])
            .draw();
        itemSelect.value = "";
        unitInput.value = "";
        qtyInput.value = "";
    });
    tableBody.addEventListener("click", function (e) {
        const row = e.target.closest("tr");
        if (!row) return;
        if (e.target.classList.contains("remove-row")) {
            dataTable.row(row).remove().draw();
        }
        if (e.target.classList.contains("remove-item")) {
            row.remove();
        }
    });
});

</script>

@endpush

