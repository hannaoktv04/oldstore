document.addEventListener('DOMContentLoaded', function () {
    const qtyInput = document.getElementById('qty');
    const formTambahQty = document.getElementById('formTambahQty');
    const formPesanQty = document.getElementById('formPesanQty');
    const formPesanQtyFinal = document.getElementById('formPesanQtyFinal');
    const tanggalInput = document.getElementById('tanggalPickerLangsung');
    const tanggalHidden = document.getElementById('tanggalPengambilanLangsung');
    const thumbnails = document.querySelectorAll('.thumbnail-click');
    const mainImage = document.getElementById('mainImage');
    const formPesan = document.getElementById('formPesan');
    const formTambah = document.getElementById('formTambah');
    const formPesanLangsung = document.getElementById('formPesanLangsung');
    const btnKirim = document.getElementById('btnKirimPermintaan');
    const spinnerKirim = document.getElementById('spinnerKirim');
    const textKirim = document.getElementById('textKirim');

    function syncQty() {
        const qty = qtyInput.value;
        formTambahQty.value = qty;
        if (formPesanQty) formPesanQty.value = qty;
        if (formPesanQtyFinal) formPesanQtyFinal.value = qty;
    }

    function showQtyAlert(message) {
        const container = document.getElementById('qtyAlertContainer');
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
        button.disabled = true;
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Memproses...
        `;
    }

    window.ubahQty = function (change) {
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
    };

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

    if (formPesan) {
        formPesan.addEventListener('submit', function () {
            showLoadingOnButton(this.querySelector('button'));
        });
    }

    if (formTambah) {
        formTambah.addEventListener('submit', function () {
            showLoadingOnButton(this.querySelector('button'));
        });
    }

    if (formPesanLangsung) {
        formPesanLangsung.addEventListener('submit', function (e) {
            if (!tanggalInput.value) {
                e.preventDefault();
                alert("Silakan pilih Tanggal Pengiriman terlebih dahulu.");
                return;
            }
            tanggalHidden.value = tanggalInput.value;

            spinnerKirim.classList.remove('d-none');
            textKirim.textContent = 'Mengirim...';
            btnKirim.disabled = true;
        });
    }

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function () {
            const fullSrc = this.getAttribute('data-full');
            mainImage.src = fullSrc;
        });
    });

    syncQty(); 

    flatpickr("#tanggalPickerLangsung", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        minuteIncrement: 5,
        defaultHour: 9,
        defaultMinute: 0,
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            document.getElementById("tanggalPengambilanLangsung").value = dateStr;
        }
    });
});
