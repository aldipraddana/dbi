ALTER TABLE `dana_keluar_items`
CHANGE `nama_barang` `nama_barang` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `pengadaan_stock_id`;

ALTER TABLE `dana_keluar_items` DROP FOREIGN KEY `dana_keluar_items_pengadaan_stock_id_foreign`;
ALTER TABLE `dana_keluar_items` ADD FOREIGN KEY (`pengadaan_stock_id`) REFERENCES `pengadaan_stock_details` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;