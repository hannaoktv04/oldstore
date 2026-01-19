document.addEventListener('DOMContentLoaded', function () {
  const checkAll = document.getElementById('flexCheckDefault');
  const checkboxes = document.querySelectorAll('.item-checkbox');
  const btnHapus = document.getElementById('btnHapusTerpilih');
  const selectedCartIds = document.getElementById('selectedCartIds');
  const jumlahTerpilihEls = document.querySelectorAll('.jumlah-terpilih');
  const totalHargaEl = document.getElementById('totalHarga');
  const checkoutForm = document.getElementById('checkoutForm');

  function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID').format(number);
  }

  function toggleDeleteButton() {
    const checked = Array.from(checkboxes).filter(cb => cb.checked);
    if (btnHapus) btnHapus.classList.toggle('d-none', checked.length === 0);
    if (selectedCartIds) selectedCartIds.value = checked.map(cb => cb.value).join(',');
    if (checkAll) checkAll.checked = checked.length === checkboxes.length;
    jumlahTerpilihEls.forEach(el => el.textContent = checked.length);

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
      toggleDeleteButton();
    });
  }

  checkboxes.forEach(cb => cb.addEventListener('change', toggleDeleteButton));

  if (checkoutForm) {
    checkoutForm.addEventListener('submit', function (e) {
      document.querySelectorAll('input[name="cart_ids[]"]').forEach(el => el.remove());

      const selected = Array.from(checkboxes).filter(cb => cb.checked);
      if (selected.length === 0) {
        e.preventDefault();
        Swal.fire({
          icon: 'warning',
          title: 'Belum ada item',
          text: 'Silakan pilih minimal satu item di keranjang.',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
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
});
