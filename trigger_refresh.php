<?php
// trigger_refresh.php
file_put_contents('reload_trigger.txt', time()); // Записва текущото време като индикатор за промяна
echo json_encode(['success' => true]);
?>
