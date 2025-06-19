document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('flexCheckDefault');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const btnHapus = document.getElementById('btnHapusTerpilih');
    const selectedCartIds = document.getElementById('selectedCartIds');
    const checkoutForm = document.getElementById('checkoutForm');
    const checkoutSubmitBtn = document.querySelector('#tanggalModal .btn-success');
    const jumlahTerpilihEls = document.querySelectorAll('.jumlah-terpilih');


    if (checkAll && btnHapus && selectedCartIds && checkboxes.length > 0) {
        checkAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleDeleteButton();
        });

        checkboxes.forEach(cb => cb.addEventListener('change', toggleDeleteButton));

        function toggleDeleteButton() {
            const checked = Array.from(checkboxes).filter(cb => cb.checked);
            if (checked.length > 0) {
                btnHapus.classList.remove('d-none');
                selectedCartIds.value = checked.map(cb => cb.value).join(',');
            } else {
                btnHapus.classList.add('d-none');
                selectedCartIds.value = '';
            }
            checkAll.checked = checked.length === checkboxes.length;

            jumlahTerpilihEls.forEach(el => {
                el.textContent = checked.length;
            });
        }
    }

    if (checkoutForm && checkoutSubmitBtn) {
        checkoutForm.addEventListener('submit', function (e) {
            document.querySelectorAll('input[name="cart_ids[]"]').forEach(el => el.remove());

            const selected = Array.from(checkboxes).filter(cb => cb.checked);

            if (selected.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu item di keranjang.');
                return;
            }

            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'cart_ids[]';
                input.value = cb.value;
                checkoutForm.appendChild(input);
            });
        });
    }

    setTimeout(function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        });
    }, 1000);
});
