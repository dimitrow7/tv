document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll(".datepicker").forEach(function(element) {
        flatpickr(element, {
            dateFormat: "Y-m-d",    // Форматът, подаден към сървъра (за базата данни)
            altInput: true,
            altFormat: "d.m.Y",     // Форматът, визуализиран в таблото (DD.MM.YYYY)
            time_24hr: true
        });
    });
    
    
        // Инициализация на Flatpickr за времевите полета в картите
        document.querySelectorAll(".timepicker").forEach(function(element) {
            flatpickr(element, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i", // 24-часов формат
                time_24hr: true,
                minuteIncrement: 1
            });
        });

        // Инициализация на Flatpickr за времевите полета във формуляра за качване
        flatpickr("#upload_start_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 1
        });

        flatpickr("#upload_end_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 1
        });
    });
    