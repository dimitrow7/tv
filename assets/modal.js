document.getElementById('uploadForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'dashboard.php', true);

    // Показваме модалния прозорец
    const progressModal = new bootstrap.Modal(document.getElementById('uploadProgressModal'));
    progressModal.show();

    // Проследяване на прогреса на качване
    xhr.upload.onprogress = function(event) {
        if (event.lengthComputable) {
            const percentComplete = (event.loaded / event.total) * 100;
            const progressBar = document.getElementById('uploadProgressBar');
            progressBar.style.width = percentComplete + '%';
            progressBar.textContent = Math.round(percentComplete) + '%';
        }
    };

    // Обработване на отговора от сървъра
    xhr.onload = function() {
        console.log('Отговор от сървъра:', xhr.responseText); // Логваме отговора от сървъра

        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    progressModal.hide();
                    window.location.reload();
                } else {
                    alert('Възникна грешка: ' + response.message);
                    progressModal.hide();
                }
            } catch (e) {
                console.error('Грешка при парсирането на отговора:', e);
                alert('Неуспешно качване. Моля, опитайте отново.');
                progressModal.hide();
            }
        } else {
            alert('Грешка при качването на файла: ' + xhr.status);
            progressModal.hide();
        }
    };

    xhr.onerror = function() {
        alert('Грешка при качването на файла.');
        progressModal.hide();
    };

    xhr.send(formData);
});
