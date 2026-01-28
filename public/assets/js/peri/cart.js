document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('flexCheckDefault');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const btnHapus = document.getElementById('btnHapusTerpilih');
    const totalHargaEl = document.getElementById('totalHarga');
    const checkoutForm = document.getElementById('checkoutForm');

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function toggleSelection() {
        const checked = Array.from(checkboxes).filter(cb => cb.checked);
        
        // Update tombol hapus
        if (btnHapus) btnHapus.classList.toggle('d-none', checked.length === 0);
        
        // Update Master Checkbox
        if (checkAll) checkAll.checked = (checked.length === checkboxes.length);

        updateTotalHarga(checked);
    }

    function updateTotalHarga(checkedCheckboxes) {
        let total = 0;
        checkedCheckboxes.forEach(cb => {
            const row = cb.closest('.card');
            const harga = parseInt(row.dataset.harga) || 0;
            const qty = parseInt(row.dataset.qty) || 0;
            total += harga * qty;
        });

        if (totalHargaEl) {
            totalHargaEl.textContent = "Rp " + formatRupiah(total);
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleSelection();
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', toggleSelection));

    // Handle Klik "Beli Sekarang" / Checkout
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Stop form sementara

            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            
            if (selected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Produk',
                    text: 'Silakan pilih minimal satu item untuk dibeli.',
                });
                return;
            }

            // Gabungkan ID yang dipilih (misal: "11,14")
            const ids = selected.map(cb => cb.value).join(',');

            // Arahkan ke checkoutPage dengan parameter 'ids'
            window.location.href = `${this.action}?ids=${ids}`;
        });
    }
});