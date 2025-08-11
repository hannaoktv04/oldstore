document.addEventListener('DOMContentLoaded', function () {
    const notificationList = document.getElementById('notificationList');
    const notifIndicator = document.getElementById('notifIndicator');
    const notifCountBadge = document.getElementById('notifCountBadge');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const markAllBtn = document.getElementById('markAllAsRead');

    async function loadNotifications() {
        try {
            const response = await fetch('/notifikasi', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();

            notificationList.innerHTML = '';

            if (data.jumlah > 0) {
                notifIndicator?.classList.remove('d-none');
                notifCountBadge.textContent = data.jumlah;
                notifCountBadge?.classList.remove('d-none');
            } else {
                notifIndicator?.classList.add('d-none');
                notifCountBadge?.classList.add('d-none');
                notificationList.innerHTML = `
                    <li class="list-group-item text-center py-3 text-muted">
                        Tidak ada notifikasi baru
                    </li>
                `;
                return;
            }

            data.data.forEach(notif => {
                const item = document.createElement('li');
                item.classList.add('list-group-item', 'dropdown-notifications-item', 'position-relative');

                let content = `
                    <div class="flex-grow-1 pe-4">
                        <h6 class="mb-1">${notif.judul}</h6>
                        <p class="mb-0">${notif.pesan}</p>
                        <small class="text-muted">${notif.waktu}</small>
                    </div>
                    ${notif.read ? '' : '<span class="badge badge-dot bg-primary position-absolute top-50 translate-middle-y end-0 me-2"></span>'}
                `;

                if (notif.url) {
                    item.innerHTML = `
                        <a href="${notif.url}" class="d-flex text-reset text-decoration-none w-100">
                            ${content}
                        </a>
                    `;
                } else {
                    item.innerHTML = `<div class="d-flex w-100">${content}</div>`;
                }

                notificationList.appendChild(item);
            });

        } catch (error) {
            console.error('Gagal memuat notifikasi:', error);
            notificationList.innerHTML = `
                <li class="list-group-item text-center py-3 text-danger">
                    Gagal memuat notifikasi
                    <button id="retryLoadNotif" class="btn btn-sm btn-link">Coba lagi</button>
                </li>
            `;
            document.getElementById('retryLoadNotif')?.addEventListener('click', loadNotifications);
        }
    }

    async function markAllAsRead() {
        try {
            const response = await fetch('/notifikasi/baca', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            notifIndicator?.classList.add('d-none');
            notifCountBadge?.classList.add('d-none');

            loadNotifications();

        } catch (error) {
            console.error('Gagal menandai notifikasi:', error);
            alert('Gagal menandai notifikasi sebagai sudah dibaca');
        }
    }

    if (notificationDropdown) {
        notificationDropdown.addEventListener('click', loadNotifications);
    }

    if (markAllBtn) {
        markAllBtn.addEventListener('click', markAllAsRead);
    }

    loadNotifications();

    setInterval(loadNotifications, 60000);
});
