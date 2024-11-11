document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const xhr = new XMLHttpRequest();

    xhr.open('POST', 'dashboard.php', true);

    xhr.upload.onprogress = function(event) {
        if (event.lengthComputable) {
            const percentComplete = Math.round((event.loaded / event.total) * 100);
            document.getElementById('progressBar').value = percentComplete;
            document.getElementById('progressText').textContent = percentComplete + '%';
        }
    };

    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('Файлът е качен успешно');
            location.reload();
        } else {
            alert('Възникна грешка при качването');
        }
    };

    xhr.onerror = function() {
        alert('Възникна грешка при качването');
    };

    document.getElementById('progressContainer').style.display = 'block';
    xhr.send(formData);
});