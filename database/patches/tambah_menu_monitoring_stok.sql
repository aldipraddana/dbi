INSERT INTO `user_menus` (`name`)
SELECT 'Monitoring Stok'
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `user_menus` WHERE `name` = 'Monitoring Stok'
);
