document.addEventListener('DOMContentLoaded', () => {
    const cartIcon = document.getElementById('cart-icon');
    const cartPopup = document.getElementById('cart-popup');
    const userIcon = document.getElementById('user-icon');
    const userPopup = document.getElementById('user-popup');
    const overlay = document.getElementById('popup-overlay');

    let isOpeningUserPopup = false;

    function closeAllPopups() {
        cartPopup?.classList.add('d-none');
        overlay?.classList.remove('show');
        userPopup?.classList.remove('show');

        setTimeout(() => {
            overlay?.classList.add('d-none');
            userPopup?.classList.add('d-none');
        }, 400);
    }

    cartIcon?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = !cartPopup.classList.contains('d-none');
        closeAllPopups();
        if (!isOpen) cartPopup.classList.remove('d-none');
    });

    userIcon?.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = userPopup.classList.contains('show');
    closeAllPopups();

    if (!isOpen) {
        // Tampilkan elemen SEBELUM memulai transisi
        overlay?.classList.remove('d-none');
        userPopup?.classList.remove('d-none');
        
        // Trigger reflow browser untuk memastikan transisi berjalan
        void overlay.offsetWidth;
        
        // Mulai transisi masuk
        overlay?.classList.add('show');
        userPopup?.classList.add('show');
    }
    });

    document.addEventListener('click', (e) => {
        if (isOpeningUserPopup) return; 

        const target = e.target;
        if (
            userPopup.contains(target) ||
            userIcon.contains(target)
        ) return;

        closeAllPopups();
    });

    userPopup?.addEventListener('click', e => e.stopPropagation());
    overlay?.addEventListener('click', () => closeAllPopups());
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAllPopups();
    });
});
