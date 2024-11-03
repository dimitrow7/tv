<?php
// Данни за свързване с базата данни
define('DB_HOST', 'localhost');
define('DB_USER', 'resinwoo_barhey');
define('DB_PASS', 'SrPGbw}&SbT1');
define('DB_NAME', 'resinwoo_media_dashboard');

// Основни настройки
define('UPLOADS_DIR', 'uploads/');
define('THUMBNAILS_DIR', UPLOADS_DIR . 'thumbnails/');

// Настройка на часова зона
date_default_timezone_set('Europe/Sofia'); // Задайте вашата часова зона

// Дефиниция на текущите дата и час
define('CURRENT_DATE', date('Y-m-d'));
define('CURRENT_DAY', date('N')); // Ден от седмицата (1 за понеделник до 7 за неделя)
define('CURRENT_TIME', date('H:i:s')); // Текущ час в 24-часов формат

// Имена на страниците
define('BRAND', 'BAR HEY');
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
define('SYSTEM_VERSION', 'v1.0.0');
define('DEVELOPED_BY', 'Разработка от BETAone');
