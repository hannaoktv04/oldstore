document.addEventListener("DOMContentLoaded", function () {
    const btnMinus = document.getElementById('btn-minus');
    const btnPlus = document.getElementById('btn-plus');
    const inputQty = document.getElementById('qty');

    btnMinus.addEventListener('click', function () {
        let current = Number(inputQty.value) || 1;
        const min = Number(inputQty.min) || 1;

        if (current > min) {
            inputQty.value = current - 1;
        }
    });

    btnPlus.addEventListener('click', function () {
        let current = Number(inputQty.value) || 1;
        const max = Number(inputQty.max) || 9999;

        if (current < max) {
            inputQty.value = current + 1;
        }
    });

    inputQty.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

});
