document.addEventListener("DOMContentLoaded", function () {

    if (typeof $ === "undefined") {
        console.error("jQuery belum dimuat sebelum item-table.js");
        return;
    }

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const $tableEl = $("#itemTable");
    const dataUrl = $tableEl.data("source");

    const table = $tableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            type: "GET",
            data: function (d) {
                d.kategori = $("#filter-kategori").val();
            },
            error: function (xhr) {
                console.error("DT Ajax error", xhr.status, xhr.responseText);
                if (typeof Swal !== "undefined") {
                    Swal.fire("Error", `(${xhr.status}) ${xhr.statusText}`, "error");
                }
            },
        },
        columns: [
            { data: "produk", name: "nama_barang" },
            { data: "kategori", name: "category.categori_name" },
            { data: "stok", name: "stok", className: "text-center" },
            { data: "harga", name: "harga", className: "text-center" },
            { data: "size", name: "sizes.size", className: "text-center" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
                className: "text-center",
            },
        ],
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
    });

    $("#filter-kategori").on("change", function () {
        table.draw();
    });

    function reloadTable() {
        table.ajax.reload(null, false);
    }

    $(document).on("click", ".btn-detail", function () {
        const itemId = $(this).data("id");
        $("#detailModalBody").html(
            '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
        );
        $("#detailModal").modal("show");

        $.get("/admin/items/" + itemId, function (data) {
            let photoHtml = "";
            if (data.gallery_urls && data.gallery_urls.length > 0) {
                photoHtml =
                    '<div class="d-flex flex-wrap gap-2 mb-3">' +
                    data.gallery_urls
                        .map(
                            (url) => `
            <a href="${url}" target="_blank">
              <img src="${url}" class="img-thumbnail rounded-4" alt="Gambar Produk"
                   style="height:100px;width:100px;object-fit:cover;">
            </a>`
                        )
                        .join("") +
                    "</div>";
            } else {
                photoHtml = "<p>Tidak ada gambar.</p>";
            }

            $("#detailModalBody").html(`
                ${photoHtml}
                <table class="table">
                  <tr><th style="width:30%;">Nama Produk</th><td>${data.nama_barang ?? ""}</td></tr>
                  <tr><th>Kategori</th><td>${data.category ? data.category.categori_name : "N/A"}</td></tr>
                  <tr><th>Stok</th><td>${data.stok ?? 0}</td></tr>
                  <tr><th>Harga</th><td>${data.harga ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data.harga) : '-'}</td></tr>
                  <tr><th>Size</th><td>${data.sizes ? data.sizes.map(s => s.size).join(", ") : "-"}</td></tr>
                  <tr><th>Deskripsi</th><td>${data.deskripsi ?? ""}</td></tr>
                </table>
            `);
        });
    });

    $(document).on("click", ".btn-delete", function () {
        const itemId = $(this).data("id");
        const itemName = $(this).data("nama") || "";

        Swal.fire({
            title: "Anda Yakin?",
            text: `Item "${itemName}" akan dihapus permanen!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: "/admin/items/" + itemId,
                type: "DELETE",
                success: function (resp) {
                    if (resp.success) {
                        Swal.fire("Terhapus!", resp.message, "success");
                        reloadTable();
                    }
                },
                error: function () {
                    Swal.fire("Gagal!", "Terjadi kesalahan.", "error");
                },
            });
        });
    });

});
