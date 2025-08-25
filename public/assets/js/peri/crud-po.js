(function () {
  document.addEventListener("DOMContentLoaded", function () {
    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
    const hasDT = !!(window.jQuery && jQuery.fn && typeof jQuery.fn.DataTable === "function");

    const swalAlert = (opts) => Swal && Swal.fire ? Swal.fire(opts) : alert(opts?.text || opts || "Alert");
    const swalConfirm = async (text, extra = {}) => {
      if (!(window.Swal && Swal.fire)) {
        return confirm(text || "Are you sure?");
      }
      const res = await Swal.fire({
        title: extra.title || "Apakah Anda yakin?",
        text,
        icon: extra.icon || "warning",
        showCancelButton: true,
        confirmButtonText: extra.confirmText || "Ya",
        cancelButtonText: extra.cancelText || "Batal",
        confirmButtonColor: extra.confirmButtonColor || "#d33",
        cancelButtonColor: extra.cancelButtonColor || "#3085d6",
        ...extra,
      });
      return !!res.isConfirmed;
    };
    const itemSelect = $("#item-select") || $("#item_id");
    const unitInput = $("#unit");
    const qtyInput = $("#qty") || $("#qty_fisik");
    const addItemBtn = $("#add-item");
    const poTable = $("#po-table") || $("#poTable");
    const tableBody = poTable ? (poTable.tBodies[0] || poTable.querySelector("tbody")) : null;

    let dataTable = null;
    if (poTable && hasDT) {
      dataTable = jQuery.fn.DataTable.isDataTable(poTable)
        ? jQuery(poTable).DataTable()
        : jQuery(poTable).DataTable({
            searching: false,
            paging: false,
            info: false,
            ordering: false,
            language: { emptyTable: "Belum ada item ditambahkan." },
          });
    }

    if (itemSelect) {
      itemSelect.addEventListener("change", function () {
        const opt = this.options[this.selectedIndex];
        const satuan = opt?.getAttribute("data-satuan") || "";
        if (unitInput) unitInput.value = satuan;

        const stokAkhirInput = $("#stok-akhir");
        if (stokAkhirInput) stokAkhirInput.value = opt?.getAttribute("data-stok") || 0;
      });
    }

    if (addItemBtn && itemSelect && qtyInput && tableBody) {
      addItemBtn.addEventListener("click", function () {
        const opt = itemSelect.options[itemSelect.selectedIndex];
        const itemId = opt?.value;
        const qtyVal = parseFloat(qtyInput.value);

        if (!itemId) {
          swalAlert({ icon: "error", title: "Oops", text: "Silakan pilih item terlebih dahulu!" });
          return;
        }
        if (isNaN(qtyVal) || qtyVal <= 0) {
          swalAlert({ icon: "error", title: "Oops", text: "Qty harus diisi dan lebih dari 0!" });
          return;
        }

        const duplicate = $$('input[name="item_id[]"]').some((i) => i.value === itemId);
        if (duplicate) {
          swalAlert({ icon: "error", title: "Duplikat", text: "Item ini sudah ditambahkan!" });
          return;
        }

        const itemCode = opt.getAttribute("data-kode") || "";
        const itemName = opt.getAttribute("data-nama") || "";
        const satuan = opt.getAttribute("data-satuan") || "";

        const rowTds = [
          `<input type="hidden" name="item_code[]" value="${itemCode}">${itemCode}`,
          `<input type="hidden" name="item_id[]" value="${itemId}"><input type="hidden" name="item_name[]" value="${itemName}">${itemName}`,
          `<input type="hidden" name="unit[]" value="${satuan}">${satuan}`,
          `<input type="number" name="qty[]" class="form-control" value="${qtyVal}" step="0.01" min="0.01" required>`,
          `<button type="button" class="btn text-center btn-sm btn-danger remove-row">Hapus</button>`,
        ];

        if (dataTable) {
          dataTable.row.add(rowTds).draw();
        } else {
          const tr = document.createElement("tr");
          tr.innerHTML = rowTds.map((td) => `<td>${td}</td>`).join("");
          tableBody.appendChild(tr);
        }
        itemSelect.value = "";
        if (unitInput) unitInput.value = "";
        qtyInput.value = "";
      });
    }

    if (tableBody) {
      tableBody.addEventListener("click", (e) => {
        const btn = e.target.closest(".remove-row");
        if (!btn) return;
        const row = btn.closest("tr");
        if (dataTable) {
          dataTable.row(row).remove().draw();
        } else if (row) {
          row.remove();
        }
      });
    }

    document.addEventListener("click", async (e) => {
      const btn = e.target.closest(".delete_data");
      if (!btn) return;
      e.preventDefault();
      const form = btn.closest("form");
      if (!form) return;
      const ok = await swalConfirm("Data ini akan dihapus permanen!", {
        confirmText: "Ya, Hapus!",
        cancelText: "Batal",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
      });
      if (ok) form.submit();
    });
    const printButton = $("#print");
    const printOut = $("#print_out");
    if (printButton && printOut) {
      printButton.addEventListener("click", function () {
        const newWin = window.open("", "", "width=900,height=800");
        newWin.document.write(`
          <html>
          <head>
            <title>Purchase Order</title>
            <link href="/css/app.css" rel="stylesheet" />
          </head>
          <body>
            <h3 class="text-center">Purchase Order</h3>
            ${printOut.innerHTML}
          </body>
          </html>
        `);
        newWin.document.close();
        setTimeout(() => {
          newWin.print();
          newWin.close();
        }, 300);
      });
    }
    $$("form[data-nested='true']").forEach((f) => {
      if (!f.parentElement) return;
      document.body.appendChild(f); 
    });
  });
})();
