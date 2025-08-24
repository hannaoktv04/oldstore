document.addEventListener('DOMContentLoaded', function () {
  const checkAll = document.getElementById('flexCheckDefault');
  const checkboxes = document.querySelectorAll('.item-checkbox');
  const btnHapus = document.getElementById('btnHapusTerpilih');
  const selectedCartIds = document.getElementById('selectedCartIds');
  const jumlahTerpilihEls = document.querySelectorAll('.jumlah-terpilih');

  function toggleDeleteButton() {
    const checked = Array.from(checkboxes).filter(cb => cb.checked);
    if (btnHapus) btnHapus.classList.toggle('d-none', checked.length === 0);
    if (selectedCartIds) selectedCartIds.value = checked.map(cb => cb.value).join(',');
    if (checkAll) checkAll.checked = checked.length === checkboxes.length;
    jumlahTerpilihEls.forEach(el => el.textContent = checked.length);
  }

  if (checkAll) {
    checkAll.addEventListener('change', function () {
      checkboxes.forEach(cb => cb.checked = this.checked);
      toggleDeleteButton();
    });
  }
  checkboxes.forEach(cb => cb.addEventListener('change', toggleDeleteButton));


  const checkoutForm = document.getElementById('checkoutForm');
  const tanggalInput = document.getElementById('tanggal_pengiriman_input');
  const tanggalHidden = document.getElementById('tanggal_pengiriman');

  if (checkoutForm && tanggalInput && tanggalHidden) {
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

      if (!tanggalInput.value) {
        e.preventDefault();
        Swal.fire({
          icon: 'info',
          title: 'Tanggal & waktu belum diisi',
          text: 'Silakan pilih tanggal dan waktu pengiriman.',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        }).then(() => {
          if (tanggalInput._flatpickr) tanggalInput._flatpickr.open();
          tanggalInput.focus();
        });
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
  
  let tickTimeout = null;  
  let tickInterval = null; 

  function stopLiveTick() {
    if (tickTimeout)  { clearTimeout(tickTimeout);  tickTimeout = null; }
    if (tickInterval) { clearInterval(tickInterval); tickInterval = null; }
  }

  function setTimeToNow(instance) {
    if (instance.selectedDates.length) return;

    const now = new Date();

    instance.currentYear  = now.getFullYear();
    instance.currentMonth = now.getMonth();
    instance.redraw();

    if (instance.timeContainer) {
      instance.hourElement.value   = String(now.getHours()).padStart(2, "0");
      instance.minuteElement.value = String(now.getMinutes()).padStart(2, "0");
      if (instance.secondElement) {
        instance.secondElement.value = String(now.getSeconds()).padStart(2, "0");
      }
    }

    instance.input.value = "";
  }

  function startLiveTick(instance) {
    stopLiveTick();
    const now = new Date();
    const msToNextMinute = (60 - now.getSeconds()) * 1000;
    tickTimeout = setTimeout(() => {
      setTimeToNow(instance);
      tickInterval = setInterval(() => setTimeToNow(instance), 60_000);
    }, msToNextMinute);
  }

  flatpickr("#tanggal_pengiriman_input", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    minuteIncrement: 1,      
    position: "auto",
    scrollToInput: true,
    appendTo: document.body,

    onReady(selectedDates, dateStr, instance) {
      if (!instance.selectedDates.length) instance.input.value = ""; 
    },

    onOpen(selectedDates, dateStr, instance) {
      setTimeToNow(instance);   
      startLiveTick(instance);  
    },

    onClose(selectedDates, dateStr, instance) {
      stopLiveTick();           
    }
  });
});
