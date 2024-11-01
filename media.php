<?php
require_once 'config.php'; // Включваме конфигурационния файл

header('Content-Type: application/json');
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$result = $mysqli->query("SELECT file_path FROM media_files");
$mediaFiles = [];

while ($row = $result->fetch_assoc()) {
    $mediaFiles[] = $row['file_path'];
}

echo json_encode($mediaFiles);
?>
