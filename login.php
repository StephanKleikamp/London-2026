<?php
// login.php — prüft das Passwort und setzt eine PHP-Session

session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

// Passwörter → Rollen
// Zum Ändern einfach diese beiden Werte anpassen.
$passwords = [
    'london'       => 'student',  // Schüler: sehen & hochladen
    'london-admin' => 'admin',    // Lehrkraft: zusätzlich löschen
];

$input = $_POST['password'] ?? '';

if (isset($passwords[$input])) {
    $_SESSION['role'] = $passwords[$input];
    echo json_encode(['success' => true, 'role' => $passwords[$input]]);
} else {
    echo json_encode(['success' => false, 'error' => 'Falsches Passwort']);
}
