SET foreign_key_checks = 0;
DROP TABLE IF EXISTS `ae_cart_ab_testing`, `ae_cat_prod_repository`, `ae_cart_repository`, `ae_guest_action_repository`, `ae_log`, `ae_sync_rotate`;
DELETE FROM `eav_attribute` WHERE `eav_attribute`.`attribute_code` = "ae_sync";
DELETE FROM `eav_attribute` WHERE `eav_attribute`.`attribute_code` = "ae_sync_date";
ALTER TABLE `sales_flat_order` DROP `ae_sync`;
ALTER TABLE `sales_flat_order` DROP `ae_group`;
DELETE FROM `core_resource` WHERE `core_resource`.`code` = 'affinityitems_setup';
DELETE FROM `core_config_data` WHERE `core_config_data`.`path` LIKE "affinityitems%";
SET foreign_key_checks = 1;
