document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(toggle => {
      const targetId = toggle.getAttribute('href');
      const collapse = document.querySelector(targetId);
      const icon     = toggle.querySelector('.rotate-icon');

      if (!collapse || !icon) return;

      collapse.addEventListener('show.bs.collapse', () => icon.classList.add('rotate'));
      collapse.addEventListener('hide.bs.collapse', () => icon.classList.remove('rotate'));
  });
});
