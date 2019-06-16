SELECT id FROM article WHERE

(`name` LIKE CONCAT('%',{{text}},'%') OR teaser LIKE CONCAT('%',{{text}},'%')) AND delete_date IS NULL AND publish_date IS NOT NULL AND is_public = 1
