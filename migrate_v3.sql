-- ============================================================
--  Migration v3 — settings-Tabelle hinzufügen
--
--  NUR ausführen wenn die Tabelle noch nicht existiert
--  (d.h. Installation vor dem Update auf v3).
--  In phpMyAdmin: deine Datenbank auswählen → SQL-Tab.
-- ============================================================

CREATE TABLE IF NOT EXISTS settings (
    key_name  VARCHAR(50)  NOT NULL,
    value     VARCHAR(255) NOT NULL,
    PRIMARY KEY (key_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Standard: 4 Gruppen (nur eintragen falls noch nicht vorhanden)
INSERT IGNORE INTO settings (key_name, value) VALUES ('group_count', '4');
