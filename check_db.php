<?php
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $stmt = $pdo->query("SHOW COLUMNS FROM partidos");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo $col['Field'] . "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
