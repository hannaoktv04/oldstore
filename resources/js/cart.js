document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('flexCheckDefault');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const btnHapus = document.getElementById('btnHapusTerpilih');
    const selectedCartIds = document.getElementById('selectedCartIds');
    const jumlahTerpilihEls = document.querySelectorAll('.jumlah-terpilih');

    if (checkAll && checkboxes.length > 0) {
        checkAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleDeleteButton();
        });

        checkboxes.forEach(cb => cb.addEventListener('change', toggleDeleteButton));

        function toggleDeleteButton() {
            const checked = Array.from(checkboxes).filter(cb => cb.checked);
            btnHapus.classList.toggle('d-none', checked.length === 0);
            selectedCartIds.value = checked.map(cb => cb.value).join(',');
            checkAll.checked = checked.length === checkboxes.length;
            jumlahTerpilihEls.forEach(el => el.textContent = checked.length);
        }
    }

    const checkoutForm = document.getElementById('checkoutForm');
    const tanggalInput = document.getElementById('tanggal_pengambilan_input');
    const tanggalHidden = document.getElementById('tanggal_pengambilan');

    if (checkoutForm && tanggalInput && tanggalHidden) {
        checkoutForm.addEventListener('submit', function (e) {
            document.querySelectorAll('input[name="cart_ids[]"]').forEach(el => el.remove());
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            if (selected.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu item di keranjang.');
                return;
            }

            tanggalHidden.value = tanggalInput.value;

            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'cart_ids[]';
                input.value = cb.value;
                checkoutForm.appendChild(input);
            });
        });
    }
});
