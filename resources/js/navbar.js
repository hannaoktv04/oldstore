document.addEventListener('DOMContentLoaded', () => {
    const cartIcon = document.getElementById('cart-icon');
    const cartPopup = document.getElementById('cart-popup');

    const notifIcon = document.getElementById('notif-icon');
    const notifPopup = document.getElementById('notif-popup');

    const userIcon = document.getElementById('user-icon');
    const userPopup = document.getElementById('user-popup');

    function closeAllPopups() {
        cartPopup?.classList.add('d-none');
        notifPopup?.classList.add('d-none');
        userPopup?.classList.add('d-none');
    }

    cartIcon?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = !cartPopup.classList.contains('d-none');
        closeAllPopups();
        if (!isOpen) cartPopup.classList.remove('d-none');
    });

    notifIcon?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = !notifPopup.classList.contains('d-none');
        closeAllPopups();
        if (!isOpen) notifPopup.classList.remove('d-none');
    });

    userIcon?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = !userPopup.classList.contains('d-none');
        closeAllPopups();
        if (!isOpen) userPopup.classList.remove('d-none');
    });

    document.addEventListener('click', () => {
        closeAllPopups();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAllPopups();
    });
});