-- ============================================================
--  Migration v2 — quest_id Spalte hinzufügen
--
--  NUR ausführen wenn die Tabelle bereits aus setup.sql
--  erstellt wurde (vor dem Update auf v2).
--  In phpMyAdmin: Datenbank auswählen → SQL → diese Datei importieren.
-- ============================================================

ALTER TABLE photos
    ADD COLUMN quest_id TINYINT UNSIGNED DEFAULT NULL
        COMMENT 'Rallye-Aufgabe 0–11, NULL = allgemeines Foto'
        AFTER description,
    ADD INDEX idx_quest_id (quest_id);
