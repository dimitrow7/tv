<?php
// Лог файл за отстраняване на грешки
$log_file = __DIR__ . '/zencoder_webhook_log.txt';
function logMessage($message) {
    global $log_file;
    file_put_contents($log_file, "[" . date("Y-m-d H:i:s") . "] $message\n", FILE_APPEND);
}

// Получаване на данните от POST заявката
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Записване на входящите данни в лог файл
logMessage("Получени данни: " . print_r($data, true));

// Проверка на състоянието на задачата
if (isset($data['job']['state']) && $data['job']['state'] === 'finished') {
    logMessage("Задачата е завършена успешно.");
    
    // Извличане на информация за изходния файл
    $output = $data['outputs'][0];
    $output_url = $output['url'];
    $original_file_name = pathinfo(parse_url($output_url, PHP_URL_PATH), PATHINFO_BASENAME);
    $local_path = __DIR__ . '/uploads/compressed_' . $original_file_name;

    // Сваляне на обработения файл
    logMessage("Сваляне на обработения файл от URL: $output_url");
    $ch = curl_init($output_url);
    $fp = fopen($local_path, 'w+');

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 минути timeout за сваляне
    curl_exec($ch);

    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    fclose($fp);

    if ($http_code === 200 && !$curl_error) {
        logMessage("Файлът е успешно свален и записан на: $local_path");

        // Актуализиране на базата данни
        $file_id = $data['job']['id'];
        $new_path = '/uploads/' . 'compressed_' . basename($local_path); 

        logMessage("Актуализиране на базата данни за файл ID: $file_id");

        require_once 'config.php';
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($mysqli->connect_error) {
            logMessage("Грешка при връзка с базата данни: " . $mysqli->connect_error);
            die('Connection failed: ' . $mysqli->connect_error);
        }

        $stmt = $mysqli->prepare("UPDATE media_files SET file_path = ?, thumbnail = ? WHERE id = ?");
        $stmt->bind_param('ssi', $new_path, $thumbnail_path, $file_id);

        if ($stmt->execute()) {
            logMessage("Базата данни е успешно актуализирана с правилния път и миниатюра.");
        } else {
            logMessage("Грешка при актуализиране на базата данни: " . $stmt->error);
        }
        $stmt->close();
        $mysqli->close();
    } else {
        logMessage("Грешка при свалянето на файла. HTTP код: $http_code, cURL грешка: $curl_error");
        unlink($local_path); // Изтриваме частичния файл, ако съществува
    }
} else {
    logMessage("Няма завършена задача за обработка.");
}

http_response_code(200); // Връщаме успех на Zencoder
?>
