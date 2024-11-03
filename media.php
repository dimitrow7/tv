<?php
require_once 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die(json_encode(['error' => "Database connection failed: " . $mysqli->connect_error]));
}

// Получаване на текущия ден от седмицата и час
$current_day = date('N'); // Ден от седмицата: 1 (Понеделник) до 7 (Неделя)
$current_time = date('H:i'); // Час в 24-часов формат

// Извличане на файловете от `media_schedule` за текущия ден и час
$query = "
    SELECT mf.file_path 
    FROM media_files mf
    JOIN media_schedule ms ON mf.id = ms.media_file_id
    WHERE ms.day_of_week = ?
      AND ms.start_time <= ? 
      AND ms.end_time >= ?
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("iss", $current_day, $current_time, $current_time);
$stmt->execute();
$result = $stmt->get_result();

// Съхраняване на файловете в масив за JSON връщане
$mediaFiles = [];
while ($row = $result->fetch_assoc()) {
    $mediaFiles[] = $row['file_path'];
}

$stmt->close();
$mysqli->close();

// Връщане на JSON данни с валидните файлове
echo json_encode($mediaFiles);
?>
