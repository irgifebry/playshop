-- Add description column to banners table if it doesn't exist
ALTER TABLE banners ADD COLUMN description TEXT NULL AFTER title;
