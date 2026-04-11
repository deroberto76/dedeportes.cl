<?php
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Tournaments with 'Libertadores' or 'Sudamericana' in the name:\n";
    $sql = "SELECT DISTINCT torneo FROM partidos WHERE torneo LIKE '%Libertadores%' OR torneo LIKE '%Sudamericana%'";
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['torneo'] . "\n";
    }

    echo "\nGroups for these tournaments:\n";
    $sql = "SELECT DISTINCT torneo, grupo FROM partidos WHERE (torneo LIKE '%Libertadores%' OR torneo LIKE '%Sudamericana%') ORDER BY torneo, grupo";
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['torneo'] . " | Group: " . $row['grupo'] . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
