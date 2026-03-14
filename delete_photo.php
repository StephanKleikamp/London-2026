<?php
// delete_photo.php — löscht ein Foto anhand seiner ID
// (Datei vom Server + Eintrag aus der Datenbank)
// Nur für Lehrkräfte (Rolle "admin") erlaubt.

session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

// Rollenpüfung — serverseitig, nicht umgehbar
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

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Ungültige ID']);
    exit;
}

try {
    $pdo = getDB();

    // Dateinamen ermitteln
    $stmt = $pdo->prepare('SELECT filename FROM photos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'Foto nicht gefunden']);
        exit;
    }

    // Datei vom Server löschen
    $filePath = UPLOAD_DIR . $row['filename'];
    if (file_exists($filePath)) {
        @unlink($filePath);
    }

    // Datenbankeintrag löschen
    $del = $pdo->prepare('DELETE FROM photos WHERE id = :id');
    $del->execute([':id' => $id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler']);
}
