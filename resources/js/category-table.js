if (!window.__categoryTableInitialized) {
    window.__categoryTableInitialized = true;

    $(function () {
        let table;
        if ($.fn.DataTable.isDataTable("#categoryTable")) {
            table = $("#categoryTable").DataTable();
        } else {
            table = $("#categoryTable").DataTable({
                columnDefs: [{ orderable: false, targets: [0, 3] }],
                order: [[1, "asc"]],
            });
        }

        const bar = $("#actionBarCategory");
        const bulk = $("#bulkActionFormCategory");

        function syncHidden() {
            bulk.find('input[name="selected_categories[]"]').remove();
            $(".category-checkbox:checked").each(function () {
                $("<input>", {
                    type: "hidden",
                    name: "selected_categories[]",
                    value: this.value,
                }).appendTo(bulk);
            });
        }

        function updateBar() {
            const n = $(".category-checkbox:checked").length;
            $("#selectedCountCategory").text(`${n} kategori dipilih`);
            bar.toggleClass("d-none", n === 0);
            if (n) checkFloating();
            syncHidden();
        }

        function checkFloating() {
            const info = document.getElementById("categoryTable_info");
            if (!info || bar.hasClass("d-none")) return;
            const show =
                info.getBoundingClientRect().bottom >
                window.innerHeight - bar[0].offsetHeight;
            bar.toggleClass("action-bar-fixed", show);
        }

        $(window).on("scroll resize", checkFloating);

        $("#selectAllCategory").on("click", function () {
            $(".category-checkbox").prop("checked", this.checked);
            updateBar();
        });

        $("#categoryTable").on("change", ".category-checkbox", function () {
            if (!this.checked) $("#selectAllCategory").prop("checked", false);
            updateBar();
        });

        $("#floatingSelectAllCategory").on("click", function () {
            $(".category-checkbox").prop("checked", this.checked);
            $("#selectAllCategory").prop("checked", this.checked);
            updateBar();
        });

        table.on("draw", updateBar);

        // ✅ Delegasi untuk tombol edit kategori
        $("#categoryTable").on("click", ".btnEditKategori", function () {
            const form = $("#formEditKategori");
            const input = $("#editCategoryName");

            form.attr("action", $(this).data("action"));
            input.val($(this).data("name"));
        });

        // ✅ Delegasi untuk tombol hapus kategori
        $("#categoryTable").on("click", ".btnHapusKategori", function () {
            const action = $(this).data("action");
            const nama = $(this).data("nama");

            $("#formHapusKategori").attr("action", action);
            $("#namaKategoriDihapus").text(nama);
        });

        const bulkModal = document.getElementById("modalHapusBulkKategori");
        bulkModal.addEventListener("show.bs.modal", () => {
            const selected = document.querySelectorAll(".category-checkbox:checked");
            const container = bulkModal.querySelector("#bulkCategoryIds");
            const label = bulkModal.querySelector("#jumlahKategoriDihapus");

            container.innerHTML = "";
            label.textContent = selected.length;

            selected.forEach((cb) => {
                const h = document.createElement("input");
                h.type = "hidden";
                h.name = "selected_categories[]";
                h.value = cb.value;
                container.appendChild(h);
            });
        });
    });
}
