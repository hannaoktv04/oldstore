$(function () {
    const table = $("#categoryTable").DataTable({
        columnDefs: [{ orderable: false, targets: [0, 3] }],
        order: [[1, "asc"]],
    });

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

    // Edit kategori
    document.querySelectorAll(".btnEditKategori").forEach((btn) => {
        btn.addEventListener("click", () => {
            const form = document.getElementById("formEditKategori");
            const input = document.getElementById("editCategoryName");
            form.setAttribute("action", btn.dataset.action);
            input.value = btn.dataset.name;
        });
    });

    // Hapus kategori
    document.querySelectorAll(".btnHapusKategori").forEach((btn) => {
        btn.addEventListener("click", () => {
            document
                .getElementById("formHapusKategori")
                .setAttribute("action", btn.dataset.action);
            document.getElementById("namaKategoriDihapus").textContent =
                btn.dataset.nama;
        });
    });

    // Modal hapus bulk
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
