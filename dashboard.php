<?php
session_start();

// Временно показване на грешките на екрана
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Проверка за грешки при връзката с базата данни
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Проверка за вход в системата
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $mysqli->query("SELECT * FROM users WHERE username='$username'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Грешно потребителско име или парола.";
    }
}

if (isset($_POST['upload']) && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileName = basename($file['name']);
    $uploadDir = __DIR__ . '/uploads/';
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        error_log("Файлът е качен успешно: $filePath");

        // URL за входящ файл
        $inputUrl = ZENCODER_INPUT_URL . "$fileName";

        // Подготовка на заявката към Zencoder
        $apiKey = ZENCODER_API;
        $webhookUrl = ZENCODER_WEBHOOK;
        $zencoderEndpoint = 'https://app.zencoder.com/api/v2/jobs';

        $data = [
            'api_key' => $apiKey,
            'input' => $inputUrl,
            'notifications' => [$webhookUrl],
            'outputs' => [
                [
                    'label' => 'Compressed Video',
                    'video_codec' => 'h264',
                    'audio_codec' => 'aac',
                    'width' => 1920,
                    'height' => 1080,
                    'quality' => 3
                ]
            ]
        ];

        // Изпращане на заявката към Zencoder
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data)
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($zencoderEndpoint, false, $context);

        if ($response === FALSE) {
            error_log("Грешка при заявка към Zencoder.");
            echo "Грешка при обработката.";
        } else {
            $responseData = json_decode($response, true);
            $jobId = $responseData['id'];

            // Запис в базата данни
            $stmt = $mysqli->prepare("
                INSERT INTO media_files (file_path, zencoder_job_id, uploaded_by) 
                VALUES (?, ?, ?)
            ");
            $uploadedBy = $_SESSION['user'];
            $stmt->bind_param("sis", $filePath, $jobId, $uploadedBy);
            $stmt->execute();
            $stmt->close();

            echo "Файлът е изпратен за обработка.";
        }
    } else {
        echo "Грешка при качването на файла.";
    }
}




// Проверка за изтриване на файлове
if (isset($_GET['delete']) && isset($_SESSION['user'])) {
    $id = $_GET['delete'];
    $result = $mysqli->query("SELECT * FROM media_files WHERE id=$id");
    $file = $result->fetch_assoc();

    if ($file) {
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }

        if (!empty($file['thumbnail']) && file_exists($file['thumbnail'])) {
            unlink($file['thumbnail']);
        }

        $mysqli->query("DELETE FROM media_files WHERE id=$id");
        $mysqli->query("DELETE FROM media_schedule WHERE media_file_id=$id");
    }

    header("Location: dashboard.php");
    exit();
}

$mediaFiles = $mysqli->query("SELECT * FROM media_files");
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo DASHBOARD_TITLE; ?></title>
    <link href="./assets/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- <script src="./assets/thumb.js"></script> -->
    <script src="./assets/time.js"></script> 
    <script src="./assets/progressbar.js"></script> 
</head>

<body class="bg-light">
    <!-- Банер секция -->
    <div class="banner">
        <div class="banner-content">
            <img src="./assets/logo.png" alt="Лого">
            <h2><?php echo BANNER_HEADING; ?></h2>
            <p><?php echo BANNER_SUBTEXT; ?></p>
            <p><?php echo BANNER_SUBTEXT_2; ?></p>
        </div>
    </div>

    <div class="container py-4">
        <?php if (!isset($_SESSION['user'])): ?>
            <h2 class="mb-4">Вход</h2>
            <form method="POST" class="card p-4 shadow">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Потребителско име" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Парола" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary">Вход</button>
            </form>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Здравей, <?php echo htmlspecialchars($_SESSION['user']); ?></h3>
                <div>
                    <button id="refreshScreens" class="btn btn-warning me-2">
                        <i class="bi bi-arrow-clockwise"></i> Презареди екраните
                    </button>
                    <a href="index.php" class="btn btn-primary me-2" target="_blank">
                        <i class="bi bi-eye"></i> Преглед
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="bi bi-person-walking"></i> Изход
                    </a>
                </div>
            </div>

            <script>
                document.getElementById('refreshScreens').addEventListener('click', function() {
                    fetch('trigger_refresh.php')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Екраните ще бъдат презаредени.');
                            } else {
                                alert('Неуспешно презареждане.');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            </script>

            <!-- Форма за качване на файлове -->
            <form method="POST" enctype="multipart/form-data" class="mb-4 card p-4 shadow">
    <div class="mb-3">
        <input type="file" name="file" accept="image/*,video/*" class="form-control" required>
    </div>

    <!-- Прогрес бар -->
    <div id="progressContainer" style="display: none;">
        <progress id="progressBar" value="0" max="100"></progress>
        <span id="progressText">0%</span>
    </div>

    <!-- Бутон за показване на графика -->
    <button type="button" onclick="toggleSchedule()" class="btn btn-info mb-3">График за показване</button>

    <!-- Контейнер за график -->
    <div id="schedule-container" style="display: none; border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; border-radius: 5px;">
        <h5 class="mb-3">Настройки за график</h5>
        <?php
        $days = [
            '1' => 'Понеделник',
            '2' => 'Вторник',
            '3' => 'Сряда',
            '4' => 'Четвъртък',
            '5' => 'Петък',
            '6' => 'Събота',
            '7' => 'Неделя'
        ];
        foreach ($days as $dayNum => $dayName) {
            echo "
            <div class='row mb-2'>
                <div class='col-md-1'>
                    <input type='checkbox' name='schedule[$dayNum][enabled]' value='1' checked>
                </div>
                <div class='col-md-3'>$dayName</div>
                <div class='col-md-4'>
                    <label>Начален час:</label>
                    <input type='text' name='schedule[$dayNum][start_time]' class='form-control timepicker' value='00:00'>
                </div>
                <div class='col-md-4'>
                    <label>Краен час:</label>
                    <input type='text' name='schedule[$dayNum][end_time]' class='form-control timepicker' value='23:59'>
                </div>
            </div>";
        }
        ?>
    </div>

    <button type="submit" name="upload" class="btn btn-success mt-3">Качване</button>
</form>

<!-- JavaScript за показване/скриване на графика -->
<script>
    // Показване/скриване на графика
    function toggleSchedule() {
        const scheduleContainer = document.getElementById('schedule-container');
        scheduleContainer.style.display = scheduleContainer.style.display === 'none' ? 'block' : 'none';
    }

    // Инициализация на Flatpickr
    document.querySelectorAll(".timepicker").forEach(element => {
        flatpickr(element, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
        });
    });
</script>

            <h2 class="mb-4">Медийни файлове:</h2>

            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php while ($file = $mediaFiles->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow">
                            <?php if ($file['file_type'] === 'image'): ?>
                                <img src="<?php echo htmlspecialchars($file['file_path']); ?>" class="card-img-top" alt="Image thumbnail">
                            <?php elseif ($file['file_type'] === 'video' && !empty($file['thumbnail'])): ?>
                                <div class="position-relative">
                                    <img src="<?php echo htmlspecialchars($file['thumbnail']); ?>" class="card-img-top" alt="Video thumbnail">
                                    <span class="play-icon position-absolute top-50 start-50 translate-middle">
                                        <i class="bi bi-play-circle-fill" style="font-size: 3rem; color: white;"></i>
                                    </span>
                                </div>
                            <?php else: ?>
                                <video src="<?php echo htmlspecialchars($file['file_path']); ?>" class="card-img-top" muted autoplay loop></video>
                            <?php endif; ?>

                            <div class="card-body">
                                <p class="card-text"><i class="bi bi-folder2-open"></i> 
                                    <strong>
                                        <?php 
                                            $fileName = htmlspecialchars(basename($file['file_path']));
                                            if (strlen($fileName) > 30) {
                                                echo substr($fileName, 0, 7) . '...' . substr($fileName, -13);
                                            } else {
                                                echo $fileName;
                                            }
                                        ?>
                                    </strong>
                                </p>
                                <p class="card-text"><i class="bi bi-person-square"></i> <?php echo htmlspecialchars($file['uploaded_by']); ?></p>
                                <p class="card-text"><i class="bi bi-calendar4-week"></i> <?php echo htmlspecialchars($file['uploaded_at']); ?></p>

                                <div class="card-control mt-3">
                                    <button type="button" onclick="toggleSchedule2('<?php echo $file['id']; ?>')" class="btn btn-info me-2">График за показване</button>
                                    <a href="<?php echo htmlspecialchars($file['file_path']); ?>" class="btn btn-primary me-2" target="_blank">
                                        <i class="bi bi-eye"></i> Преглед
                                    </a>
                                    <a href="?delete=<?php echo $file['id']; ?>" class="btn btn-danger" onclick="return confirm('Сигурни ли сте, че искате да изтриете този файл?');">
                                        <i class="bi bi-trash3-fill"></i>
                                    </a>
                                </div>

                                <div class="schedule-container" id="schedule-container-<?php echo $file['id']; ?>" style="display: none; border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; border-radius: 5px;">
                                    <h5 class="mb-3">Настройки за график</h5>
                                    <form action="update_media.php" method="POST" class="edit-schedule-form">
                                        <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">

                                        <?php
                                        $days = [
                                            '1' => 'Пон.',
                                            '2' => 'Вт.',
                                            '3' => 'Ср.',
                                            '4' => 'Чет.',
                                            '5' => 'Пет.',
                                            '6' => 'Съб.',
                                            '7' => 'Нед.'
                                        ];

                                        $scheduleQuery = $mysqli->prepare("SELECT day_of_week, start_time, end_time FROM media_schedule WHERE media_file_id = ?");
                                        $scheduleQuery->bind_param("i", $file['id']);
                                        $scheduleQuery->execute();
                                        $scheduleResult = $scheduleQuery->get_result();
                                        $schedules = [];
                                        
                                        while ($schedule = $scheduleResult->fetch_assoc()) {
                                            $schedules[$schedule['day_of_week']] = $schedule;
                                        }
                                        $scheduleQuery->close();

                                        foreach ($days as $dayNum => $dayName) {
                                            $is_checked = isset($schedules[$dayNum]) ? 'checked' : '';
                                            $start_time = $schedules[$dayNum]['start_time'] ?? '00:00';
                                            $end_time = $schedules[$dayNum]['end_time'] ?? '23:59';
                                            echo "
                                            <div class='row mb-2'>
                                                <div class='col-md-1'>
                                                    <input type='checkbox' name='schedule[$dayNum][enabled]' value='1' $is_checked>
                                                </div>
                                                <div class='col-md-3'>$dayName</div>
                                                <div class='col-md-4'>
                                                    <label>Начален час:</label>
                                                    <input type='text' name='schedule[$dayNum][start_time]' class='form-control timepicker' value='$start_time'>
                                                </div>
                                                <div class='col-md-4'>
                                                    <label>Краен час:</label>
                                                    <input type='text' name='schedule[$dayNum][end_time]' class='form-control timepicker' value='$end_time'>
                                                </div>
                                            </div>";
                                        }
                                        ?>
                                        <button type="submit" class="btn btn-success mt-2">
                                            <i class="bi bi-floppy2-fill"></i> Запази промените
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- JavaScript за показване/скриване на графика -->
            <script>
                function toggleSchedule2(fileId) {
                    var scheduleContainer = document.getElementById('schedule-container-' + fileId);
                    if (scheduleContainer.style.display === 'none') {
                        scheduleContainer.style.display = 'block';
                    } else {
                        scheduleContainer.style.display = 'none';
                    }
                }
            </script>


            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p><?php echo SYSTEM_NAME; ?> - <?php echo SYSTEM_VERSION; ?></p>
        <p><?php echo DEVELOPED_BY; ?></p>
    </footer>

</body>
</html>
