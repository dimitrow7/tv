<?php
// Данни за свързване с базата данни
define('DB_HOST', 'localhost');
define('DB_USER', 'dimitrow_slidetv_demo');
define('DB_PASS', '[3J|DLU[nmOkRQp]');
define('DB_NAME', 'dimitrow_slidetv_demo');

// Основни настройки
define('BASE_DIR', __DIR__);

// Динамично определяне на директорията за качване
define('UPLOADS_DIR', BASE_DIR . '/uploads');
define('THUMBNAILS_DIR', UPLOADS_DIR . 'thumbnails/');

// Динамично определяне на URL директорията за качване
$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
define('UPLOAD_URL', $base_url . '/uploads');


// Настройка на часова зона
date_default_timezone_set('Europe/Sofia'); // Задайте вашата часова зона

// Zencoder API key and webhook
define('ZENCODER_API', 'd737048726321ed0fe658be41f7a85ec');
define('ZENCODER_WEBHOOK', 'https://demo.slidetv.eu/zencoder-webhook.php');
define('ZENCODER_INPUT_URL', 'https://demo.slidetv.eu/uploads/');


// Конфигурация за FFMPEG
define('FFMPEG_PATH', '/usr/bin/ffmpeg'); // Укажете правилния път до ffmpeg

// Конфигурация за централизирано логване
ini_set('log_errors', 'On'); // Включва логването на грешки
ini_set('error_log', __DIR__ . '/error_log'); // Указва пътя до файла за грешки

// Дефиниция на текущите дата и час
define('CURRENT_DATE', date('Y-m-d'));
define('CURRENT_DAY', date('N')); // Ден от седмицата (1 за понеделник до 7 за неделя)
define('CURRENT_TIME', date('H:i:s')); // Текущ час в 24-часов формат

// Имена на страниците
define('BRAND', 'SLIDETV DEMO');
define('SITE_TITLE', BRAND . ' - TV MEDIA');
define('DASHBOARD_TITLE', BRAND . ' - TV MEDIA DASHBOARD');

// Надписи в банера
define('BANNER_HEADING', 'TV MEDIA DASHBOARD');
define('BANNER_SUBTEXT', '');
define('BANNER_SUBTEXT_2', '');

// Време за смяна на изображенията (в милисекунди)
define('IMAGE_SLIDE_INTERVAL', 10000); // милисекунди (10000 - 10сек)

// Информация за системата
define('SYSTEM_NAME', 'TV Media Dashboard');
define('SYSTEM_VERSION', 'v1.0.2');
define('DEVELOPED_BY', '');
