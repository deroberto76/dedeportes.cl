<?php
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, torneo, local, visitante, fecha, estado FROM partidos ORDER BY id DESC LIMIT 10");
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($matches);

    $today_start = strtotime('today');
    $today_end = strtotime('tomorrow') - 1;
    echo "\nToday Start: " . $today_start . " (" . date('Y-m-d H:i:s', $today_start) . ")\n";
    echo "Today End: " . $today_end . " (" . date('Y-m-d H:i:s', $today_end) . ")\n\n";

    foreach ($matches as $match) {
        $fecha_db = str_replace('/', '-', $match['fecha']);
        $match_timestamp = strtotime($fecha_db);
        echo "ID {$match['id']} - Fecha DB Original: {$match['fecha']} -> Timestamp procesado: {$match_timestamp} (" . date('Y-m-d H:i:s', $match_timestamp) . ") | Estado: {$match['estado']}\n";
    }

} catch (PDOException $e) {
    echo "Error DB: " . $e->getMessage();
}
