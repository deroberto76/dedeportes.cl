<?php
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Columns in 'partidos' table:\n";
    $stmt = $pdo->query("DESCRIBE partidos");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }

    echo "\nSample rows for Copa Libertadores:\n";
    $sql = "SELECT torneo, grupo, equipo, rival, fecha FROM partidos WHERE torneo = 'Copa Libertadores' LIMIT 5";
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- Torneo: " . $row['torneo'] . " | Grupo: " . $row['grupo'] . " | " . $row['equipo'] . " vs " . $row['rival'] . "\n";
    }

    echo "\nSample rows for Copa Sudamericana:\n";
    $sql = "SELECT torneo, grupo, equipo, rival, fecha FROM partidos WHERE torneo = 'Copa Sudamericana' LIMIT 5";
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- Torneo: " . $row['torneo'] . " | Grupo: " . $row['grupo'] . " | " . $row['equipo'] . " vs " . $row['rival'] . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
