document.getElementById('videoFile').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('video/')) {
        const video = document.createElement('video');
        video.src = URL.createObjectURL(file);
        
        // Избиране на кадър от по-късна секунда, например 2 секунди
        video.currentTime = 4;

        video.addEventListener('seeked', function() {
            // Изчакваме видеото да достигне желаната времева позиция и тогава извличаме кадър
            const canvas = document.getElementById('thumbnailCanvas');
            const context = canvas.getContext('2d');

            // Рисуваме кадър от видеото върху canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Преобразуваме изображението от canvas в base64
            const thumbnailDataUrl = canvas.toDataURL('image/jpeg');
            document.getElementById('thumbnailInput').value = thumbnailDataUrl;

            // Освобождаваме ресурса на видеото
            URL.revokeObjectURL(video.src);
        });

        video.addEventListener('error', function() {
            console.error('Грешка при зареждането на видеото');
        });
    }
});