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
                `<button type="button" class="btn btn-sm btn-danger remove-row">Hapus</button>`,
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
