document.addEventListener("DOMContentLoaded", function () {
    if (typeof window.$ === "undefined") {
        console.error("jQuery is required for crud-category.js");
        return;
    }
    const $ = window.$;
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN":
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content") || "",
            Accept: "application/json",
        },
    });
    const bulkDeleteRoute =
        document.getElementById("bulkDeleteRoute")?.value || "";
    const tableCard = document.querySelector(".card");
    const offcanvasEl = document.getElementById("categoryBulkActionOffcanvas");
    const selectAll = document.getElementById("selectAllCategory");
    let offcanvas = null;
    if (offcanvasEl && window.bootstrap?.Offcanvas) {
        offcanvas = new bootstrap.Offcanvas(offcanvasEl);
    }
    const dt = $("#categoryTable").DataTable({
        columnDefs: [{ orderable: false, targets: [0, 3] }],
        order: [[1, "asc"]],
        language: {
            search: "Cari:",
            lengthMenu: "_MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            paginate: {
                next: '<i class="ri-arrow-right-s-line"></i>',
                previous: '<i class="ri-arrow-left-s-line"></i>',
            },
        },
    });
    function checkOffcanvasPosition() {
        if (!offcanvasEl?.classList.contains("show") || !tableCard) return;
        const cardBottom = tableCard.getBoundingClientRect().bottom;
        const windowHeight = window.innerHeight;
        if (cardBottom < windowHeight)
            offcanvasEl.classList.add("offcanvas-docked");
        else offcanvasEl.classList.remove("offcanvas-docked");
    }
    function updateBulkUI() {
        const checked = document.querySelectorAll(
            ".category-checkbox:checked"
        ).length;
        const counter = document.getElementById("selectedCountCategory");
        if (counter) counter.textContent = `${checked} `;
        if (!offcanvas) return;
        if (checked > 0) offcanvas.show();
        else {
            offcanvas.hide();
            offcanvasEl?.classList.remove("offcanvas-docked");
        }
        setTimeout(checkOffcanvasPosition, 300);
    }
    window.addEventListener("scroll", checkOffcanvasPosition);
    window.addEventListener("resize", checkOffcanvasPosition);

    if (selectAll) {
        selectAll.addEventListener("click", function () {
            document.querySelectorAll(".category-checkbox").forEach((cb) => {
                cb.checked = selectAll.checked;
                cb.dispatchEvent(new Event("change"));
            });
        });
    }

    document
        .getElementById("categoryTable")
        ?.addEventListener("change", function (e) {
            if (!e.target.classList.contains("category-checkbox")) return;
            const all = document.querySelectorAll(".category-checkbox");
            const checked = document.querySelectorAll(
                ".category-checkbox:checked"
            );
            if (selectAll)
                selectAll.checked =
                    all.length > 0 && all.length === checked.length;
            updateBulkUI();
        });
    dt.on("draw", function () {
        const all = document.querySelectorAll(".category-checkbox");
        const checked = document.querySelectorAll(".category-checkbox:checked");
        if (selectAll)
            selectAll.checked = all.length > 0 && all.length === checked.length;
        updateBulkUI();
    });
    $("#modalTambahKategori form").on("submit", function (ev) {
        ev.preventDefault();
        const $form = $(this);
        const url = $form.attr("action");
        const payload = $form.serialize();

        const $input = $form.find('input[name="categori_name"]');
        $input.removeClass("is-invalid");
        $form.find(".invalid-feedback").remove();

        $.post(url, payload)
            .done(function (resp) {
                if (resp?.success && resp?.category) {
                    const cat = resp.category;
                    const routes = resp.routes || {};

                    const actionsHtml = `
              <button
                class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect btnEditKategori"
                data-name="${cat.categori_name}"
                data-action="${routes.update || "#"}"
                data-bs-toggle="modal" data-bs-target="#modalEditKategori" title="Edit">
                <i class="ri-pencil-line ri-20px text-primary"></i>
              </button>
              <button
                class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect btnHapusKategori"
                data-nama="${cat.categori_name}"
                data-action="${routes.destroy || "#"}"
                title="Hapus">
                <i class="ri-delete-bin-7-line ri-20px"></i>
              </button>
            `;

                    const newRow = [
                        `<input type="checkbox" value="${cat.id}" class="form-check-input category-checkbox">`,
                        cat.categori_name,
                        cat.items_count ?? 0,
                        actionsHtml,
                    ];

                    const rowNode = dt.row.add(newRow).draw(false).node();
                    if (rowNode) {
                        rowNode.children[0].classList.add("text-center");
                        rowNode.children[2].classList.add("text-center");
                        rowNode.children[3].classList.add("text-center");
                    }

                    if (window.Swal)
                        swalSuccess(
                            resp.message || "Kategori berhasil ditambahkan!"
                        );
                    $form[0].reset();
                    document
                        .querySelector("#modalTambahKategori .btn-close")
                        ?.click();

                    if (selectAll) selectAll.checked = false;
                    document
                        .querySelectorAll(".category-checkbox")
                        .forEach((cb) => cb.dispatchEvent(new Event("change")));
                    return;
                }
                if (window.Swal)
                    swalSuccess(
                        "Berhasil",
                        "Kategori ditambahkan.",
                        "success"
                    );
                document
                    .querySelector("#modalTambahKategori .btn-close")
                    ?.click();
                window.location.reload();
            })
            .fail(function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errs = xhr.responseJSON.errors;
                    if (errs.categori_name?.length) {
                        $input
                            .addClass("is-invalid")
                            .after(
                                `<div class="invalid-feedback">${errs.categori_name[0]}</div>`
                            );
                        $input.focus();
                    }
                    if (window.Swal)
                        swalError("Periksa kembali input Anda.", {
                            title: "Gagal",
                        });
                    return;
                }
                const msg =
                    xhr?.responseJSON?.message || "Gagal menambahkan kategori.";
                if (window.Swal) swalError(msg);
                else alert(msg);
            });
    });
    document
        .getElementById("categoryTable")
        ?.addEventListener("click", function (e) {
            const btn = e.target.closest(".btnEditKategori");
            if (!btn) return;
            const form = document.getElementById("formEditKategori");
            const input = document.getElementById("editCategoryName");
            if (form) {
                form.setAttribute(
                    "action",
                    btn.getAttribute("data-action") || ""
                );
                form.setAttribute(
                    "data-id",
                    btn.closest("tr")?.querySelector(".category-checkbox")
                        ?.value || ""
                );
            }
            if (input) input.value = btn.getAttribute("data-name") || "";
        });
    $("#formEditKategori").on("submit", function (ev) {
        ev.preventDefault();
        const $form = $(this);
        const url = $form.attr("action");
        const id = $form.data("id");
        const payload = $form.serialize() + "&_method=PUT";
        const $input = $form.find('input[name="categori_name"]');
        $input.removeClass("is-invalid");
        $form.find(".invalid-feedback").remove();

        $.post(url, payload)
            .done(function (resp) {
                const rowCheckbox = document.querySelector(
                    `.category-checkbox[value="${id}"]`
                );
                if (rowCheckbox) {
                    const tr = rowCheckbox.closest("tr");
                    const nameCell = tr?.querySelector("td:nth-child(2)");
                    const newName =
                        document.getElementById("editCategoryName")?.value ||
                        "";
                    if (nameCell) nameCell.textContent = newName;
                    const btnEdit = tr?.querySelector(".btnEditKategori");
                    const btnDel = tr?.querySelector(".btnHapusKategori");
                    if (btnEdit) btnEdit.setAttribute("data-name", newName);
                    if (btnDel) btnDel.setAttribute("data-nama", newName);
                }

                if (window.Swal)
                    swalSuccess(
                        "Berhasil",
                        resp?.message || "Kategori diperbarui.",
                        "success"
                    );
                document
                    .querySelector("#modalEditKategori .btn-close")
                    ?.click();
            }
        )
            .fail(function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errs = xhr.responseJSON.errors;
                    if (errs.categori_name?.length) {
                        $input
                            .addClass("is-invalid")
                            .after(
                                `<div class="invalid-feedback">${errs.categori_name[0]}</div>`
                            );
                        $input.focus();
                    }
                    if (window.Swal)
                        Swal.fire(
                            "Gagal",
                            "Periksa kembali input Anda.",
                            "error"
                        );
                    return;
                }
                const msg =
                    xhr?.responseJSON?.message || "Gagal memperbarui kategori.";
                if (window.Swal) swalError(msg);
                else alert(msg);
            });
    });

    document
        .getElementById("categoryTable")
        ?.addEventListener("click", function (e) {
            const btn = e.target.closest(".btnHapusKategori");
            if (!btn) return;
            e.preventDefault();

            const actionUrl = btn.getAttribute("data-action") || "";
            const categoryName = btn.getAttribute("data-nama") || "-";
            const tr = btn.closest("tr");

            const doDelete = function () {
                $.post(actionUrl, { _method: "DELETE" })
                    .done(function (resp) {
                        if (tr) dt.row(tr).remove().draw();
                        if (window.Swal)
                            swalSuccess(`Kategori "${categoryName}" dihapus.`);
                        updateBulkUI();
                    })
                    .fail(function (xhr) {
                        const msg =
                            xhr?.responseJSON?.message ||
                            "Gagal menghapus kategori.";
                        if (window.Swal) swalError(msg);
                        else alert(msg);
                    });
            };

            if (window.Swal) {
                Swal.fire({
                    title: "Anda Yakin?",
                    text: `Kategori "${categoryName}" akan dihapus permanen!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
                }).then((r) => {
                    if (r.isConfirmed) doDelete();
                });
            } else {
                if (confirm(`Hapus kategori "${categoryName}"?`)) doDelete();
            }
        });
    document
        .getElementById("bulkDeleteBtn")
        ?.addEventListener("click", function () {
            const selected = Array.from(
                document.querySelectorAll(".category-checkbox:checked")
            );
            if (selected.length === 0) {
                if (window.Swal)
                    Swal.fire(
                        "Tidak Ada yang Dipilih",
                        "Silakan pilih setidaknya satu kategori untuk dihapus.",
                        "info"
                    );
                else
                    alert(
                        "Silakan pilih setidaknya satu kategori untuk dihapus."
                    );
                return;
            }

            const ids = selected.map((cb) => cb.value);

            const proceed = function () {
                $.post(bulkDeleteRoute, { selected_categories: ids })
                    .done(function (resp) {
                        selected.forEach((cb) => {
                            const tr = cb.closest("tr");
                            if (tr) dt.row(tr).remove();
                        });
                        dt.draw();
                        if (window.Swal)
                            swalSuccess(`${ids.length} kategori dihapus.`, {
                                title: "Berhasil",
                            });
                        if (selectAll) selectAll.checked = false;
                        updateBulkUI();
                    })
                    .fail(function (xhr) {
                        const msg =
                            xhr?.responseJSON?.message ||
                            "Gagal menghapus kategori terpilih.";
                        if (window.Swal) swalError(msg);
                        else alert(msg);
                    });
            };

            if (window.Swal) {
                Swal.fire({
                    title: "Anda Yakin?",
                    text: `Anda akan menghapus ${ids.length} kategori yang dipilih.`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
                }).then((r) => {
                    if (r.isConfirmed) proceed();
                });
            } else {
                if (
                    confirm(
                        `Anda akan menghapus ${ids.length} kategori yang dipilih. Lanjutkan?`
                    )
                )
                    proceed();
            }
        });
});
