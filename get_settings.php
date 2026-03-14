<?php
// get_settings.php — gibt App-Einstellungen als JSON zurück (öffentlich lesbar)

require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo  = getDB();
    $stmt = $pdo->query('SELECT key_name, value FROM settings');
    $rows = $stmt->fetchAll();
    $out  = [];
    foreach ($rows as $row) {
        $out[$row['key_name']] = $row['value'];
    }
    $out['group_count'] = (int)($out['group_count'] ?? 4);
    echo json_encode($out);
} catch (PDOException $e) {
    // Tabelle existiert noch nicht oder Verbindungsfehler → Standardwerte
    echo json_encode(['group_count' => 4]);
}
