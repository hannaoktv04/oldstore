document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('flexCheckDefault');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const btnHapus = document.getElementById('btnHapusTerpilih');
    const selectedCartIds = document.getElementById('selectedCartIds');

    if (!checkAll || !btnHapus || !selectedCartIds || checkboxes.length === 0) return;

    checkAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleDeleteButton();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleDeleteButton);
    });

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
    }
});

setTimeout(function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
    });
}, 1000);

