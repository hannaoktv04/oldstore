import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import * as bootstrap from "bootstrap";

document.addEventListener("DOMContentLoaded", () => {
    let selectedRequestId = null;

    const calendarModal = document.getElementById("calendarModal");
    const calendarContainer = document.getElementById("calendarContainer");

    document.querySelectorAll(".btn-calendar").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            selectedRequestId = button.dataset.requestId;

            const modal = new bootstrap.Modal(calendarModal);
            modal.show();

            calendarContainer.innerHTML = '<div id="calendarFlatpickr"></div>';
            setTimeout(() => {
                flatpickr("#calendarFlatpickr", {
                    inline: true,
                    minDate: "today",
                    onChange: function (selectedDates, dateStr) {
                        document.getElementById("request_id_input").value = selectedRequestId;
                        document.getElementById("tanggal_input").value = dateStr;
                        document.getElementById("tanggalForm").submit();
                    }
                });
            }, 100);
        });
    });
});
