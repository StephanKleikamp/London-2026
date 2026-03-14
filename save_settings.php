<?php
// save_settings.php — speichert App-Einstellungen (nur Admin)

session_start();
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Keine Berechtigung']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Nur POST erlaubt']);
    exit;
}

$count = filter_input(INPUT_POST, 'group_count', FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 20]]);

if (!$count) {
    echo json_encode(['success' => false, 'error' => 'Gruppenanzahl muss zwischen 1 und 20 liegen']);
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO settings (key_name, value) VALUES (:k, :v)
         ON DUPLICATE KEY UPDATE value = VALUES(value)'
    );
    $stmt->execute([':k' => 'group_count', ':v' => (string)$count]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $hint = '';
    if (strpos($e->getMessage(), "doesn't exist") !== false || $e->getCode() === '42S02') {
        $hint = ' — Tabelle "settings" fehlt, bitte migrate_v3.sql in phpMyAdmin ausführen';
    }
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler' . $hint]);
}
