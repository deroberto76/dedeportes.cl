<?php
// Script de diagnóstico para ver los nombres de equipos en la DB
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    echo "<h1>Diagnóstico de Equipos</h1>";

    // Listar todos los equipos únicos
    echo "<h2>Equipos Únicos (Columna 'equipo'):</h2><ul>";
    $stmt = $pdo->query("SELECT DISTINCT equipo FROM partidos ORDER BY equipo");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>[" . bin2hex($row['equipo']) . "] | " . htmlspecialchars($row['equipo']) . "</li>";
    }
    echo "</ul>";

    echo "<h2>Equipos Únicos (Columna 'rival'):</h2><ul>";
    $stmt = $pdo->query("SELECT DISTINCT rival FROM partidos ORDER BY rival");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>[" . bin2hex($row['rival']) . "] | " . htmlspecialchars($row['rival']) . "</li>";
    }
    echo "</ul>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
