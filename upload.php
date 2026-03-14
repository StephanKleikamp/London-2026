<?php
// upload.php — nimmt ein Foto entgegen, speichert es auf dem Server
// und schreibt die Metadaten in die MySQL-Datenbank.

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

// Nur POST erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Nur POST erlaubt']);
    exit;
}

// Eingaben bereinigen
$uploader    = trim(strip_tags($_POST['uploader']    ?? ''));
$description = trim(strip_tags($_POST['description'] ?? ''));

// quest_id: 0–11 oder null
$questIdRaw = $_POST['quest_id'] ?? '';
$questId    = ($questIdRaw !== '' && is_numeric($questIdRaw) && (int)$questIdRaw >= 0 && (int)$questIdRaw <= 11)
              ? (int)$questIdRaw
              : null;

if ($uploader === '') {
    echo json_encode(['success' => false, 'error' => 'Gruppe fehlt']);
    exit;
}
if (mb_strlen($uploader) > 50) {
    echo json_encode(['success' => false, 'error' => 'Name zu lang']);
    exit;
}
if (mb_strlen($description) > 150) {
    echo json_encode(['success' => false, 'error' => 'Beschreibung zu lang']);
    exit;
}

// Datei prüfen
if (empty($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    $err = $_FILES['photo']['error'] ?? -1;
    echo json_encode(['success' => false, 'error' => 'Upload-Fehler (Code ' . $err . ')']);
    exit;
}

$file = $_FILES['photo'];

// Größe prüfen
if ($file['size'] > MAX_FILE_SIZE) {
    echo json_encode(['success' => false, 'error' => 'Datei zu groß (max. 10 MB)']);
    exit;
}

// MIME-Type prüfen (anhand tatsächlicher Dateiinhalte, nicht des Headers)
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, ALLOWED_TYPES, true)) {
    echo json_encode(['success' => false, 'error' => 'Dateityp nicht erlaubt: ' . $mimeType]);
    exit;
}

// Sichere Dateiendung bestimmen
$extensions = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];
$ext = $extensions[$mimeType];

// Eindeutigen Dateinamen generieren
$filename = bin2hex(random_bytes(16)) . '.' . $ext;
$destPath = UPLOAD_DIR . $filename;

// Upload-Verzeichnis sicherstellen
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Datei verschieben
if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(['success' => false, 'error' => 'Datei konnte nicht gespeichert werden']);
    exit;
}

// In Datenbank eintragen
try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO photos (filename, uploader, description, quest_id, uploaded_at)
         VALUES (:filename, :uploader, :description, :quest_id, NOW())'
    );
    $stmt->execute([
        ':filename'    => $filename,
        ':uploader'    => $uploader,
        ':description' => $description !== '' ? $description : null,
        ':quest_id'    => $questId,
    ]);

    echo json_encode(['success' => true, 'filename' => $filename]);
} catch (PDOException $e) {
    // Datei wieder löschen, wenn DB-Eintrag fehlschlägt
    @unlink($destPath);
    // Fehlermeldung für Diagnose (SQLSTATE + Meldung, keine Zugangsdaten)
    $code = $e->getCode();
    $hint = match(true) {
        str_starts_with((string)$code, '2002') || str_starts_with((string)$code, 'HY')
            => 'Verbindung zur Datenbank fehlgeschlagen — DB_HOST, DB_USER oder DB_PASS in config.php prüfen',
        str_contains($e->getMessage(), 'Base table') || str_contains($e->getMessage(), "doesn't exist")
            => 'Tabelle "photos" nicht gefunden — setup.sql noch nicht ausgeführt?',
        default => 'Datenbankfehler (SQLSTATE ' . $code . ')',
    };
    echo json_encode(['success' => false, 'error' => $hint]);
}
