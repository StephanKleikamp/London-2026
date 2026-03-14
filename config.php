<?php
// ============================================================
//  Datenbank-Konfiguration — Strato-Zugangsdaten eintragen
//
//  Wo findest du die Werte?
//  Strato Kunden-Login → Meine Produkte → Hosting-Paket →
//  "MySQL-Datenbanken verwalten"
//
//  DB_HOST: Steht dort als "Server" oder "Host", z.B.:
//           db12345678.db.strato.de  (NICHT localhost!)
//  DB_NAME: Der Datenbankname, z.B.: db12345678
//  DB_USER: Meist identisch mit DB_NAME
//  DB_PASS: Das Passwort das du beim Erstellen vergeben hast
// ============================================================

define('DB_HOST',     'database-5019970591.webspace-host.com');
define('DB_NAME',     'dbs15413347');
define('DB_USER',     'dbu4242856');
define('DB_PASS',     'Klebe-25l0tt02026');

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
