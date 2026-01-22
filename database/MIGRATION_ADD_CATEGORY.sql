-- =========================================================
-- PLAYSHOP.ID DATABASE MIGRATION
-- Update: Add Game Category Column & Category Display
-- =========================================================

-- Step 1: Add category column to games table
-- RUN THIS FIRST if games.category column doesn't exist yet
ALTER TABLE games ADD COLUMN category VARCHAR(50) DEFAULT 'Other' AFTER is_active;

-- Step 2 (OPTIONAL): Update existing games with categories
-- Uncomment and modify these lines based on your actual games
-- UPDATE games SET category = 'RPG' WHERE name LIKE '%Mobile Legends%' OR name LIKE '%Ragnarok%' OR name LIKE '%Lost Ark%';
-- UPDATE games SET category = 'MOBA' WHERE name LIKE '%DOTA%' OR name LIKE '%League%';
-- UPDATE games SET category = 'PC' WHERE name LIKE '%DOTA%' OR name LIKE '%League%' OR name LIKE '%Valorant%';
-- UPDATE games SET category = 'Action' WHERE name LIKE '%PUBG%' OR name LIKE '%Valorant%' OR name LIKE '%Apex%';
-- UPDATE games SET category = 'Sports' WHERE name LIKE '%FIFA%' OR name LIKE '%NBA%' OR name LIKE '%PES%';
-- UPDATE games SET category = 'Strategy' WHERE name LIKE '%Clash%' OR name LIKE '%Hay%' OR name LIKE '%Supercell%';

-- Verify the column was added:
-- SELECT * FROM games LIMIT 1;
