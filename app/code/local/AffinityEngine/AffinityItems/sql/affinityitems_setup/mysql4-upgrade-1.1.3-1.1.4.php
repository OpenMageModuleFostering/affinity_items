<?php

$installer = $this;
$installer->startSetup();

$sql = 'ALTER TABLE  `' . $this->getTable('ae_sync_rotate') . '` ADD  `website_id` INT NOT NULL, ADD `website_reg` INT NOT NULL ;'
        . ' ALTER TABLE  `' . $this->getTable('ae_sync_rotate') . '` CHANGE  `id`  `id` INT( 16 ) NOT NULL AUTO_INCREMENT ;'
        . ' TRUNCATE TABLE  `' . $this->getTable('ae_sync_rotate') . '`;';
$installer->run($sql);

$sql = 'ALTER TABLE  `' . $this->getTable('ae_cart_repository') . '` ADD  `website_id` INT NOT NULL ;';
$installer->run($sql);

$sql = 'ALTER TABLE  `' . $this->getTable('ae_cat_prod_repository') . '` ADD  `website_id` INT NOT NULL ;';
$installer->run($sql);

$sql = 'ALTER TABLE  `' . $this->getTable('ae_guest_action_repository') . '` ADD  `website_id` INT NOT NULL ;';
$installer->run($sql);

$_websites = Mage::getResourceModel('core/website_collection');
foreach($_websites as $website) {
	$sql = "INSERT INTO `" . $this->getTable('ae_sync_rotate') . "` (`last_sync`, `date_add`, `website_id`, `website_reg`) VALUES ('actions', '2014-01-01'," . $website->getWebsiteId() . ", 0);"; 
	$installer->run($sql);
}

$sql = "DELETE FROM `" . $this->getTable('core_config_data') . "` WHERE `path` IN ('affinityitems/security/login', 'affinityitems/security/password', 'affinityitems/security/site_id', 'affinityitems/security/key');";
$installer->run($sql);

$installer->endSetup();
