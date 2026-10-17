CREATE OR REPLACE VIEW `v_monitoring_stock` AS
SELECT
    `psd`.`id` AS `id`,
    `psd`.`nama_barang` AS `nama_barang`,
    `psd`.`tipe` AS `tipe`,
    `psd`.`pm` AS `pm`,
    (`psd`.`qty` - COALESCE(`keluar`.`total_keluar`, 0)) AS `stok_saat_ini`,
    `ps`.`client_id` AS `client_id`,
    `c`.`nama_client` AS `nama_client`,
    `ps`.`supplier_id` AS `supplier_id`,
    `s`.`nama_supplier` AS `nama_supplier`
FROM `pengadaan_stock_details` `psd`
INNER JOIN `pengadaan_stocks` `ps` ON `ps`.`id` = `psd`.`pengadaan_stock_id`
INNER JOIN `clients` `c` ON `c`.`id` = `ps`.`client_id`
INNER JOIN `suppliers` `s` ON `s`.`id` = `ps`.`supplier_id`
LEFT JOIN (
    SELECT
        `psd_keluar`.`pengadaan_stock_detail_id` AS `pengadaan_stock_detail_id`,
        SUM(`psd_keluar`.`qty`) AS `total_keluar`
    FROM `pengeluaran_stock_details` `psd_keluar`
    GROUP BY `psd_keluar`.`pengadaan_stock_detail_id`
) `keluar` ON `keluar`.`pengadaan_stock_detail_id` = `psd`.`id`;
