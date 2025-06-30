document.addEventListener("DOMContentLoaded", function () {
    // Hitung selisih otomatis
    document.querySelectorAll(".qty-fisik").forEach((input) => {
        input.addEventListener("input", function () {
            const sistem = parseFloat(this.getAttribute("data-sistem")) || 0;
            const fisik = parseFloat(this.value) || 0;
            const selisih = fisik - sistem;

            const selisihCell = this.closest("tr").querySelector(".selisih");
            selisihCell.textContent = selisih.toFixed(2);
            if (selisih > 0) {
                selisihCell.style.color = "green";
            } else if (selisih < 0) {
                selisihCell.style.color = "red";
            } else {
                selisihCell.style.color = "black";
            }
        });
    });

    // Aktifkan DataTables
    $("#item-table").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: {
                next: "Berikutnya",
                previous: "Sebelumnya",
            },
        },
    });
});
