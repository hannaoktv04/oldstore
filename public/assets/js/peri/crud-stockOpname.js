document.addEventListener("DOMContentLoaded", function () {
    if (typeof $ === "undefined") {
        console.error(
            "jQuery tidak ditemukan. Muat jQuery sebelum stockopname.js"
        );
        return;
    }

    if (!$.fn || !$.fn.DataTable) {
        console.error(
            "DataTables tidak ditemukan. Muat plugin DataTables sebelum stockopname.js"
        );
    }

    const csrf = $('meta[name="csrf-token"]').attr("content");
    if (csrf) {
        $.ajaxSetup({ headers: { "X-CSRF-TOKEN": csrf } });
    } else {
        console.warn("Meta CSRF token tidak ditemukan di <head>.");
    }

    function initDataTable(selector, options) {
        const $el = $(selector);
        if (!$el.length || !$.fn || !$.fn.DataTable) return;

        if ($.fn.dataTable.isDataTable($el)) {
            $el.DataTable().clear().destroy();
        }

        $el.DataTable(
            Object.assign(
                {
                    responsive: true,
                    processing: true,
                    autoWidth: false,
                    order: [[0, "asc"]],
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
                },
                options || {}
            )
        );
    }
    initDataTable("#opnameTable");
    initDataTable("#itemsTable");

    $(document).on("input", ".qty-fisik", function () {
        const fisik = parseInt($(this).val(), 10) || 0;
        const sistem = parseInt($(this).data("sistem"), 10) || 0;
        const selisih = fisik - sistem;

        const tr = $(this).closest("tr");
        const selisihCell = tr.find(".selisih");
        const inputSelisih = tr.find(".input-selisih");

        selisihCell.text(selisih);
        inputSelisih.val(selisih);

        selisihCell.removeClass("text-danger text-success text-muted");
        if (selisih < 0) selisihCell.addClass("text-danger");
        else if (selisih > 0) selisihCell.addClass("text-success");
        else selisihCell.addClass("text-muted");

        if ($(this).val() === "") {
            selisihCell.text("-");
            inputSelisih.val("");
            selisihCell.removeClass("text-danger text-success text-muted");
        }
    });

    $(document).on("click", ".btn-delete-opname", function (e) {
        e.preventDefault();
        const $btn = $(this);
        const url = $btn.data("url"); // mis: route('admin.stock_opname.destroy', id)

        if (!url) {
            console.error("data-url untuk delete tidak ditemukan pada tombol.");
            return;
        }

        if (typeof Swal === "undefined") {
            if (confirm("Hapus data ini?")) {
                $.post(url, { _method: "DELETE" })
                    .done(() => location.reload())
                    .fail((xhr) =>
                        alert(
                            "Gagal menghapus: " +
                                (xhr.responseJSON?.message || xhr.statusText)
                        )
                    );
            }
            return;
        }

        Swal.fire({
            title: "Hapus data?",
            text: "Tindakan ini tidak dapat dibatalkan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.post(url, { _method: "DELETE" })
                .done(() => {
                    Swal.fire({
                        icon: "success",
                        title: "Terhapus",
                        timer: 1200,
                        showConfirmButton: false,
                    });
                    if (
                        $("#opnameTable").length &&
                        $.fn.dataTable.isDataTable("#opnameTable")
                    ) {
                        $("#opnameTable").DataTable().ajax
                            ? $("#opnameTable").DataTable().ajax.reload()
                            : location.reload();
                    } else {
                        location.reload();
                    }
                })
                .fail((xhr) => {
                    const msg =
                        xhr.responseJSON?.message || "Terjadi kesalahan.";
                    Swal.fire({
                        icon: "error",
                        title: "Gagal menghapus",
                        text: msg,
                    });
                });
        });
    });
});
