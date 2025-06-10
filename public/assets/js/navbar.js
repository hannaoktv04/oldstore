document.addEventListener('DOMContentLoaded', () => {
    const cartIcon = document.getElementById('cart-icon');
    const cartPopup = document.getElementById('cart-popup');

    function closeCartPopup() {
        cartPopup.classList.add('d-none');
    }

    cartIcon.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = !cartPopup.classList.contains('d-none');
        closeCartPopup();
        if (!isOpen) cartPopup.classList.remove('d-none');
    });

    // Tutup popup kalau klik di luar
    document.addEventListener('click', () => {
        closeCartPopup();
    });

    // Optional: tutup popup dengan ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeCartPopup();
    });
});
