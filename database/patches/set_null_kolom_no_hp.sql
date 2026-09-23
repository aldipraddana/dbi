ALTER TABLE `transactions`
CHANGE `no_handphone` `no_handphone` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'No Handphone pelanggan' AFTER `customer`;