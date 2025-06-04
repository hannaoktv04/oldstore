document.addEventListener("DOMContentLoaded", function () {
  const cartIcon = document.getElementById("cart-icon");
  const cartPopup = document.getElementById("cart-popup");

  cartIcon.addEventListener("click", () => {
    cartPopup.style.display = cartPopup.style.display === "block" ? "none" : "block";
  });

  document.addEventListener("click", (e) => {
    if (!cartIcon.contains(e.target) && !cartPopup.contains(e.target)) {
      cartPopup.style.display = "none";
    }
  });
});
