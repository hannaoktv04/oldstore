import Swal from 'sweetalert2';

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete_data").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const form = this.closest("form");

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data ini akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
