document.addEventListener("DOMContentLoaded", function () {
    const itemSelect = document.getElementById("item-select");
    const unitInput = document.getElementById("unit");
    const qtyInput = document.getElementById("qty");
    const addItemBtn = document.getElementById("add-item");
    const tableBody = document.getElementById("table-body");

    itemSelect.addEventListener("change", function () {
        const selected = this.options[this.selectedIndex];
        unitInput.value = selected.getAttribute("data-satuan") || "";
    });

    addItemBtn.addEventListener("click", function () {
        const selectedId = itemSelect.value;
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const itemName = selectedOption.getAttribute("data-nama");
        const satuan = unitInput.value;
        const qty = qtyInput.value;

        if (!selectedId || !qty || qty <= 0) {
            alert("Pilih item dan isi qty dengan benar.");
            return;
        }

        const row = document.createElement("tr");
        row.innerHTML = `
        <td>
            <input type="hidden" name="item_code[]" value="${itemCode}">
        </td>
            <td>
                <input type="hidden" name="item_id[]" value="${selectedId}">
                <input type="text" class="form-control" value="${itemName}" readonly>
            </td>
            <td>
                <input type="text" name="unit[]" class="form-control" value="${satuan}" readonly>
            </td>
            <td>
                <input type="number" name="qty[]" class="form-control" value="${qty}" step="0.01" min="0.01">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-row">Hapus</button>
            </td>
        `;
        tableBody.appendChild(row);

        itemSelect.value = "";
        unitInput.value = "";
        qtyInput.value = "";
    });

    tableBody.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-row")) {
            e.target.closest("tr").remove();
        }
    });
});
