$(function () {
    const table = $("#itemTable").DataTable({
        columnDefs: [{ orderable: false, targets: [0, 3, 4] }],
        order: [[1, "asc"]],
    });

    const bar = $("#actionBar");
    const bulk = $("#bulkActionForm");

    function syncHidden() {
        bulk.find('input[name="selected_items[]"]').remove();
        $(".item-checkbox:checked").each(function () {
            $("<input>", {
                type: "hidden",
                name: "selected_items[]",
                value: this.value,
            }).appendTo(bulk);
        });
    }

    function updateBar() {
        const n = $(".item-checkbox:checked").length;
        $("#selectedCount").text(`${n} produk dipilih`);
        bar.toggleClass("d-none", n === 0);
        if (n) checkFloating();
        syncHidden();
    }

    function checkFloating() {
        const info = document.getElementById("itemTable_info");
        if (!info || bar.hasClass("d-none")) return;
        const show =
            info.getBoundingClientRect().bottom >
            window.innerHeight - bar[0].offsetHeight;
        bar.toggleClass("action-bar-fixed", show);
    }

    $(window).on("scroll resize", checkFloating);

    $("#selectAll").on("click", function () {
        $(".item-checkbox").prop("checked", this.checked);
        updateBar();
    });

    $("#itemTable").on("change", ".item-checkbox", function () {
        if (!this.checked) $("#selectAll").prop("checked", false);
        updateBar();
    });

    $("#floatingSelectAll").on("click", function () {
        $(".item-checkbox").prop("checked", this.checked);
        $("#selectAll").prop("checked", this.checked);
        updateBar();
    });
    table.on("draw", updateBar);

    document.querySelectorAll(".btnHapusItem").forEach((btn) => {
        btn.addEventListener("click", () => {
            document
                .getElementById("formHapusItem")
                .setAttribute("action", btn.dataset.action);
            document.getElementById("namaItemDihapus").textContent =
                btn.dataset.nama;
        });

        const bulkModal = document.getElementById("modalHapusBulk");
        bulkModal.addEventListener("show.bs.modal", () => {
            const selected = document.querySelectorAll(
                ".item-checkbox:checked"
            );
            const container = bulkModal.querySelector("#bulkItemIds");
            const label = bulkModal.querySelector("#jumlahItemDihapus");

            container.innerHTML = "";
            label.textContent = selected.length;

            selected.forEach((cb) => {
                const h = document.createElement("input");
                h.type = "hidden";
                h.name = "selected_items[]";
                h.value = cb.value;
                container.appendChild(h);
            });
        });
    });
});
