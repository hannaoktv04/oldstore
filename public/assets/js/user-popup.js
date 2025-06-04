document.addEventListener("DOMContentLoaded", () => {
  const userIcon = document.getElementById("user-icon");
  const userPopup = document.getElementById("user-popup");

  userIcon.addEventListener("click", () => {
    userPopup.classList.toggle("hidden");
  });

  document.addEventListener("click", (e) => {
    if (!userPopup.contains(e.target) && !userIcon.contains(e.target)) {
      userPopup.classList.add("hidden");
    }
  });
});
