-- ============================================================
--  London Klassenfahrt — Datenbank-Setup
--  Einmalig ausführen, z.B. über phpMyAdmin bei Strato
--  oder per: mysql -u BENUTZER -p DATENBANKNAME < setup.sql
--
--  WICHTIG: Ersetze 'deine_datenbank' durch deinen echten
--  Datenbanknamen (steht im Strato-Kundenbereich).
-- ============================================================

USE `deine_datenbank`;

CREATE TABLE IF NOT EXISTS photos (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    filename    VARCHAR(50)      NOT NULL COMMENT 'Gespeicherter Dateiname auf dem Server',
    uploader    VARCHAR(50)      NOT NULL COMMENT 'Gruppe (z.B. Gruppe 1)',
    description VARCHAR(150)              COMMENT 'Optionale Beschreibung',
    quest_id    TINYINT UNSIGNED          COMMENT 'Rallye-Aufgabe 0–11, NULL = allgemeines Foto',
    uploaded_at DATETIME         NOT NULL COMMENT 'Zeitpunkt des Uploads',

    PRIMARY KEY (id),
    UNIQUE KEY uq_filename (filename),
    INDEX idx_uploaded_at (uploaded_at),
    INDEX idx_quest_id (quest_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    key_name  VARCHAR(50)  NOT NULL,
    value     VARCHAR(255) NOT NULL,
    PRIMARY KEY (key_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Standard: 4 Gruppen (nur eintragen falls noch nicht vorhanden)
INSERT IGNORE INTO settings (key_name, value) VALUES ('group_count', '4');
