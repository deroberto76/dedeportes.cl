<?php
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "--- TORNEOS EN TABLA PARTIDOS ---\n";
    $stmt = $pdo->query("SELECT DISTINCT torneo FROM partidos");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['torneo'] . "\n";
    }

    echo "\n--- GRUPOS EN COPA LIBERTADORES ---\n";
    $stmt = $pdo->query("SELECT DISTINCT grupo FROM partidos WHERE torneo LIKE '%Libertadores%'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- '" . $row['grupo'] . "'\n";
    }

    echo "\n--- GRUPOS EN COPA SUDAMERICANA ---\n";
    $stmt = $pdo->query("SELECT DISTINCT grupo FROM partidos WHERE torneo LIKE '%Sudamericana%'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- '" . $row['grupo'] . "'\n";
    }

    echo "\n--- ESTADOS EN PARTIDOS ---\n";
    $stmt = $pdo->query("SELECT DISTINCT estado FROM partidos");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- '" . $row['estado'] . "'\n";
    }

    echo "\n--- EJEMPLO DE FILA PARA COPA LIBERTADORES ---\n";
    $stmt = $pdo->query("SELECT * FROM partidos WHERE torneo LIKE '%Libertadores%' LIMIT 1");
    print_r($stmt->fetch(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
