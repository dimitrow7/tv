<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file_id = $_POST['file_id'];
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }

    // Премахване на съществуващия график за файла
    $stmt = $mysqli->prepare("DELETE FROM media_schedule WHERE media_file_id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $stmt->close(); // Затваряме заявката след приключване на изтриването

    // Запис на новия график
    if (isset($_POST['schedule'])) {
        $stmt = $mysqli->prepare("INSERT INTO media_schedule (media_file_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");

        foreach ($_POST['schedule'] as $dayNum => $times) {
            if (isset($times['enabled'])) { // Ако чекбоксът е маркиран
                $start_time = $times['start_time'] ?? '00:00';
                $end_time = $times['end_time'] ?? '23:59';

                $stmt->bind_param("iiss", $file_id, $dayNum, $start_time, $end_time);
                $stmt->execute();
            }
        }

        $stmt->close(); // Затваряме заявката след приключване на добавянето на графика
    }

    $mysqli->close();

    header("Location: dashboard.php");
    exit();
}
?>
