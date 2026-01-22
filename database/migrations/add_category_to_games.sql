-- Migration: Add category column to games table
-- Description: Adds category/genre support to games for filtering (RPG, MOBA, PC, Action, etc)
-- Date: 2025

ALTER TABLE games ADD COLUMN category VARCHAR(50) DEFAULT 'Other' AFTER is_active;

-- Update existing games with categories (adjust as needed based on actual games)
UPDATE games SET category = 'RPG' WHERE name IN ('Mobile Legends', 'Ragnarok M', 'Lost Ark');
UPDATE games SET category = 'MOBA' WHERE name IN ('DOTA 2', 'League of Legends', 'Mobile Legends');
UPDATE games SET category = 'PC' WHERE name IN ('DOTA 2', 'League of Legends', 'Valorant');
UPDATE games SET category = 'Action' WHERE name IN ('PUBG', 'Valorant', 'Apex Legends');
UPDATE games SET category = 'Sports' WHERE name IN ('FIFA', 'NBA 2K', 'Pro Evolution Soccer');
UPDATE games SET category = 'Strategy' WHERE name IN ('Clash of Clans', 'Hay Day', 'Supercell');
