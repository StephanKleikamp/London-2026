<?php
// ============================================================
//  Datenbank-Konfiguration — WD My Cloud EX4100 (lokal)
// ============================================================

define('DB_HOST',     'localhost');
define('DB_NAME',     'webseite');
define('DB_USER',     'webuser');
define('DB_PASS',     'Klebe');

// Upload-Verzeichnis relativ zu dieser Datei
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Erlaubte Bild-MIME-Types
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Max. Dateigröße in Bytes (10 MB)
define('MAX_FILE_SIZE', 10 * 1024 * 1024);

// ============================================================
//  Datenbankverbindung herstellen
// ============================================================
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}
