<?php
session_start();
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

// Проверка за качване на файлове
if (isset($_POST['upload']) && isset($_SESSION['user'])) {
    $file = $_FILES['file'];
    $filePath = 'uploads/' . basename($file['name']);
    $thumbnailPath = null;
    $fileType = strpos($file['type'], 'video') !== false ? 'video' : 'image';
    $uploadedBy = $_SESSION['user'];

    // Преместване на качения файл
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Ако има base64 миниатюра, я съхраняваме като файл
        if (!empty($_POST['thumbnail'])) {
            $thumbnailData = $_POST['thumbnail'];
            $thumbnailPath = 'uploads/thumbnails/' . pathinfo($file['name'], PATHINFO_FILENAME) . '.jpg';

            // Създаване на папката за миниатюри, ако не съществува
            if (!is_dir('uploads/thumbnails')) {
                mkdir('uploads/thumbnails', 0777, true);
            }

            // Преобразуване на base64 в изображение
            $thumbnailData = str_replace('data:image/jpeg;base64,', '', $thumbnailData);
            $thumbnailData = base64_decode($thumbnailData);
            file_put_contents($thumbnailPath, $thumbnailData);
        }

        // Запис в базата данни
        $stmt = $mysqli->prepare("INSERT INTO media_files (file_path, file_type, uploaded_by, thumbnail) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $filePath, $fileType, $uploadedBy, $thumbnailPath);
        $stmt->execute();
        $stmt->close();
        echo "Файлът е качен успешно.";
    } else {
        echo "Възникна грешка при качването.";
    }
}

// Проверка за изтриване на файлове
if (isset($_GET['delete']) && isset($_SESSION['user'])) {
    $id = $_GET['delete'];
    $result = $mysqli->query("SELECT * FROM media_files WHERE id=$id");
    $file = $result->fetch_assoc();

    if ($file) {
        // Изтриване на файла
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }

        // Изтриване на миниатюрата, ако съществува
        if (!empty($file['thumbnail']) && file_exists($file['thumbnail'])) {
            unlink($file['thumbnail']);
        }

        // Изтриване на записа в базата данни
        $mysqli->query("DELETE FROM media_files WHERE id=$id");
    }

    // Пренасочване след изтриване, за да се избегне повторно изпращане на заявка при презареждане
    header("Location: dashboard.php");
    exit();
}

// Зареждане на всички медийни файлове за преглед в дашборда
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
</head>
<body class="bg-light">
    <!-- Банер секция -->
    <div class="banner">
        <div class="banner-content">
            <img src="./assets/barhey-logo-sm.png" alt="Лого">
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
                    <a href="index.php" class="btn btn-primary me-2" target="_blank">
                        <i class="bi bi-eye"></i> Преглед
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="bi bi-person-walking"></i> Изход
                    </a>
                </div>
            </div>


            <!-- Форма за качване на файлове -->
            <form method="POST" enctype="multipart/form-data" id="uploadForm" class="mb-4 card p-4 shadow">
                <div class="mb-3">
                    <input type="file" name="file" id="videoFile" accept="image/*,video/*" class="form-control" required>
                </div>
                <div class="mb-3" style="display: none;">
                    <canvas id="thumbnailCanvas" width="640" height="360"></canvas>
                </div>
                <input type="hidden" name="thumbnail" id="thumbnailInput">
                <button type="submit" name="upload" class="btn btn-success"><i class="bi bi-upload"></i> Качване</button>
            </form>

            <!-- Списък с медийни файлове -->
            <h2 class="mb-4">Медийни файлове:</h2>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php while ($file = $mediaFiles->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card shadow position-relative">
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
                                <p class="card-text">
                                    <i class="bi bi-folder2-open"></i> 
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
                                <div class="card-control">
                                    <a href="<?php echo htmlspecialchars($file['file_path']); ?>" class="btn btn-primary me-2" target="_blank">
                                        <i class="bi bi-eye"></i> 
                                    </a>
                                    <a href="?delete=<?php echo $file['id']; ?>" class="btn btn-danger" onclick="return confirm('Сигурни ли сте, че искате да изтриете този файл?');">
                                    <i class="bi bi-trash3-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p><?php echo SYSTEM_NAME; ?> - <?php echo SYSTEM_VERSION; ?></p>
        <p><?php echo DEVELOPED_BY; ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/thumb.js"></script>
</body>
</html>
