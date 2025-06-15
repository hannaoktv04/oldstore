document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.setujui-semua').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const targetInput = document.querySelector(this.dataset.target);
            if (this.checked) {
                targetInput.value = this.dataset.jumlah;
                targetInput.setAttribute('readonly', true);
            } else {
                targetInput.removeAttribute('readonly');
                targetInput.value = '';
            }
        });
    });
});
