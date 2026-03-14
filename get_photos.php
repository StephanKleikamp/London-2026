<?php
// get_photos.php — gibt alle Fotos als JSON zurück (neueste zuerst)

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Nur GET erlaubt']);
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->query(
        'SELECT id, filename, uploader, description, quest_id, uploaded_at
         FROM photos
         ORDER BY uploaded_at DESC'
    );
    $photos = $stmt->fetchAll();

    echo json_encode(['success' => true, 'photos' => $photos]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'photos' => [], 'error' => 'Datenbankfehler']);
}
