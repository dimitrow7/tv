let lastReloadCheck = null;

    function checkForReload() {
        fetch('./reload_trigger.txt')
            .then(response => response.text())
            .then(timestamp => {
                if (lastReloadCheck && lastReloadCheck !== timestamp) {
                    location.reload(); // Презарежда страницата, ако има промяна
                }
                lastReloadCheck = timestamp;
            })
            .catch(error => console.error('Error checking for reload:', error));
    }

    setInterval(checkForReload, 10000); // Проверка на всеки 10 секунди
