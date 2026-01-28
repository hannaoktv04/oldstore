document.addEventListener('DOMContentLoaded', function () {
    const qtyInput = document.getElementById('qty');
    const formPesanQty = document.getElementById('formPesanQty');
    const formTambahQty = document.getElementById('formTambahQty');
    const formPesanSize = document.getElementById('formPesanSize');
    const formTambahSize = document.getElementById('formTambahSize');
    const thumbnails = document.querySelectorAll('.thumbnail-click');
    const mainImage = document.getElementById('mainImage');
    const formPesan = document.getElementById('formPesan');
    const formTambah = document.getElementById('formTambah');


    function syncQty() {
        if (!qtyInput) return;
        const qty = qtyInput.value;
        if (formPesanQty) formPesanQty.value = qty;
        if (formTambahQty) formTambahQty.value = qty;
    }

    function syncSize() {
        const selectedSize = document.querySelector('input[name="size_selection"]:checked');
        if (selectedSize) {
            if (document.getElementById('formPesanSize')) {
                document.getElementById('formPesanSize').value = selectedSize.value;
            }
            if (document.getElementById('formTambahSize')) {
                document.getElementById('formTambahSize').value = selectedSize.value;
            }
            return true;
        }
        return false;
    }

    function showAlert(containerId, message, type = 'warning') {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show mt-2 py-1 px-2" role="alert" style="font-size: 0.8rem;">
                ${message}
            </div>
        `;
        setTimeout(() => { container.innerHTML = ''; }, 3000);
    }

    window.ubahQty = function (change) {
        if (!qtyInput) return;
        const max = parseInt(qtyInput.max) || 999;
        let current = parseInt(qtyInput.value) || 1;
        current += change;

        if (current < 1) current = 1;
        if (current > max) {
            current = max;
            showAlert('qtyAlertContainer', "Jumlah tidak boleh melebihi stok.");
        }

        qtyInput.value = current;
        syncQty();
    };

    if (qtyInput) {
        qtyInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            let val = parseInt(this.value);
            const max = parseInt(this.max);

            if (val > max) {
                this.value = max;
                showAlert('qtyAlertContainer', "Jumlah tidak boleh melebihi stok.");
            } else if (val < 1 || isNaN(val)) {
                this.value = 1;
            }
            syncQty();
        });
    }

    if (formPesan) {
        formPesan.addEventListener('submit', function (e) {
            if (!syncSize()) {
                e.preventDefault();
                showAlert('sizeAlertContainer', "Silakan pilih ukuran terlebih dahulu!", "danger");
                return;
            }
            syncQty();
            const btn = document.getElementById('btnPesanLangsung');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Memproses...`;
            }
        });
    }

    if (formTambah) {
        formTambah.addEventListener('submit', function (e) {
            if (!syncSize()) {
                e.preventDefault();
                showAlert('sizeAlertContainer', "Silakan pilih ukuran terlebih dahulu!", "danger");
                return;
            }
            syncQty();
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Menambahkan...`;
            }
        });
    }

    if (thumbnails.length && mainImage) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function () {
                const fullSrc = this.getAttribute('data-full');
                if (fullSrc) mainImage.src = fullSrc;
                thumbnails.forEach(t => t.classList.remove('border-primary', 'border-2'));
                this.classList.add('border-primary', 'border-2');
            });
        });
    }

    syncQty();
});