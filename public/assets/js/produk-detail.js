function ubahQty(change) {
    const qtyInput = document.getElementById('qty');
    if (!qtyInput) return;

    const max = parseInt(qtyInput.max);
    let current = parseInt(qtyInput.value);

    current += change;
    if (current < 1) current = 1;
    if (current > max) {
        current = max;
        showQtyAlert("Jumlah tidak boleh melebihi stok.");
    }

    qtyInput.value = current;
    syncQty();
}

function syncQty() {
    const qtyInput = document.getElementById('qty');
    const formTambahQty = document.getElementById('formTambahQty');
    const formPesanQty = document.getElementById('formPesanQty');

    if (qtyInput && formTambahQty && formPesanQty) {
        const qty = qtyInput.value;
        formTambahQty.value = qty;
        formPesanQty.value = qty;
    }
}

function showQtyAlert(message) {
    const container = document.getElementById('qtyAlertContainer');
    if (!container) return;

    container.innerHTML = `
        <div class="alert alert-warning alert-dismissible fade show mt-2 py-1 px-2" role="alert">
            <small>${message}</small>
        </div>
    `;
    setTimeout(() => {
        container.innerHTML = '';
    }, 2000);
}

function showLoadingOnButton(button) {
    if (!button) return;
    button.disabled = true;
    button.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Memproses...
    `;
}

document.addEventListener("DOMContentLoaded", function () {
    syncQty();

    const qtyInput = document.getElementById('qty');
    if (qtyInput) {
        qtyInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            let val = parseInt(this.value);
            const max = parseInt(this.max);
            if (val > max) {
                this.value = max;
                showQtyAlert("Jumlah tidak boleh melebihi stok.");
            } else if (val < 1 || isNaN(val)) {
                this.value = 1;
            }
            syncQty();
        });
    }

    const formPesan = document.getElementById('formPesan');
    if (formPesan) {
        formPesan.addEventListener('submit', function () {
            const button = this.querySelector('button');
            showLoadingOnButton(button);
        });
    }

    const formTambah = document.getElementById('formTambah');
    if (formTambah) {
        formTambah.addEventListener('submit', function () {
            const button = this.querySelector('button');
            showLoadingOnButton(button);
        });
    }
});
