ALTER TABLE `users`
ADD `role` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT 'member' AFTER `password`;