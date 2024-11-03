<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?></title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .media-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .media-container img,
        .media-container video {
            max-width: 100%;
            max-height: 100%;
        }

        video {
            background-color: #000;
        }
    </style>
</head>
<body>
    <div class="media-container">
        <img id="media" src="" style="display: none;">
        <video id="video" autoplay muted style="display: none;"></video>
    </div>

    <script>
        let mediaFiles = [];
        let currentIndex = 0;
        const mediaElement = document.getElementById('media');
        const videoElement = document.getElementById('video');
        const imageSlideInterval = <?php echo IMAGE_SLIDE_INTERVAL; ?>;

        async function fetchMediaFiles() {
            try {
                const response = await fetch('media.php');
                mediaFiles = await response.json();
                showMedia();
            } catch (error) {
                console.error('Error loading media files:', error);
            }
        }

        function showMedia() {
            if (mediaFiles.length === 0) {
                console.log('No media files found.');
                return;
            }

            const file = mediaFiles[currentIndex];

            if (file.endsWith('.mp4') || file.endsWith('.webm')) {
                mediaElement.style.display = 'none';
                videoElement.src = file;
                videoElement.style.display = 'block';
                videoElement.muted = true;
                videoElement.controls = false;
                videoElement.play();

                videoElement.onended = () => {
                    currentIndex = (currentIndex + 1) % mediaFiles.length;
                    showMedia();
                };
            } else {
                videoElement.style.display = 'none';
                mediaElement.src = file;
                mediaElement.style.display = 'block';

                setTimeout(() => {
                    currentIndex = (currentIndex + 1) % mediaFiles.length;
                    showMedia();
                }, imageSlideInterval);
            }
        }

        fetchMediaFiles();
    </script>

    <script>
        let lastReloadCheck = null;

        function checkForReload() {
            fetch('reload_trigger.txt')
                .then(response => response.text())
                .then(timestamp => {
                    if (lastReloadCheck && lastReloadCheck !== timestamp) {
                        location.reload(); // Презарежда страницата, ако има промяна
                        console.log('asd')
                    }
                    lastReloadCheck = timestamp;
                })
                .catch(error => console.error('Error checking for reload:', error));
        }

        setInterval(checkForReload, 5000); // Проверка на всеки 5 секунди
    </script>

</body>
</html>
