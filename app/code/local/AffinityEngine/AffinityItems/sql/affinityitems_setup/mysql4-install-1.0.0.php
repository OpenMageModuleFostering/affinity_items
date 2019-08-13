<?php

$installer = $this;
$installer->startSetup();

$sql = 'CREATE TABLE IF NOT EXISTS `' . $this->getTable('ae_log') . '` (
	`id_log` int(16) NOT NULL AUTO_INCREMENT,
	`date_add` DATETIME NOT NULL,
	`severity` VARCHAR(50) NOT NULL,
	`message` LONGTEXT NOT NULL,
	PRIMARY KEY  (`id_log`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

$installer->run($sql);

$sql = 'CREATE TABLE IF NOT EXISTS `' . $this->getTable('ae_cart_repository') . '` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cart` int(16) DEFAULT NULL,
  `id_product` int(16) DEFAULT NULL,
  `id_product_attribute` varchar(50) DEFAULT NULL,
  `quantity` int(16) DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `aeguestid` varchar(100) DEFAULT NULL,
  `aememberid` varchar(200) DEFAULT "",
  `language` varchar(10) DEFAULT "",
  `aegroup` varchar(10) DEFAULT NULL,
  `ip` varchar(100) DEFAULT "",
  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

$installer->run($sql);

$sql = 'CREATE TABLE IF NOT EXISTS `' . $this->getTable('ae_guest_action_repository') . '` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `ae_guest` varchar(200) NOT NULL,
  `ae_member` varchar(200) DEFAULT "",
  `ae_action` varchar(100) NOT NULL,
  `ae_group` varchar(10) NOT NULL,
  `rec_type` varchar(100) NOT NULL,
  `language` varchar(10) DEFAULT "",
  `ip` varchar(100) DEFAULT "",
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

$installer->run($sql);

$sql = 'CREATE TABLE IF NOT EXISTS `' . $this->getTable('ae_sync_rotate') . '` (
	`id` int(16) NOT NULL,
	`last_sync` VARCHAR(10) NOT NULL,
	`date_add` DATE NULL,
        `initial_sync` INT NULL DEFAULT "1",
	PRIMARY KEY(id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

$installer->run($sql);

$sql = 'CREATE TABLE IF NOT EXISTS `' . $this->getTable('ae_cat_prod_repository') . '` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_id` text NOT NULL,
  `obj_type` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';

$installer->run($sql);

$sql = "INSERT INTO `" . $this->getTable('ae_sync_rotate') . "` (`id`, `last_sync`, `date_add`) VALUES (0, 'actions', '2014-01-01');";
$installer->run($sql);

// define defaults in system config
$sql = "INSERT INTO `" . $this->getTable('core_config_data') . "` (`config_id`, `scope`, `scope_id`, `path`, `value`)
        VALUES
        (NULL, 'default', '0', 'affinityitems/general/storeid', '0');";
$installer->run($sql);

$xml_path = array('items_home', 'items_left', 'items_right', 'items_cart', 'items_product', 'items_search', 'items_category');
foreach ($xml_path as $path) {
    $sql = "INSERT INTO `" . $this->getTable('core_config_data') . "` (`config_id`, `scope`, `scope_id`, `path`, `value`) 
        VALUES 
        (NULL, 'default', '0', 'affinityitems/" . $path . "/number_products', '4'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/number_products_per_line', '4'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/recommendation_config', 'personalization'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/image_size', '0'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/parent_div', ',ae_products_block_center clearfix'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/title_class', 'ae_block_title_center'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/content_div', ',ae_content_block_center'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/ul', ',ae_list_products_block_center'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/li', ',ae_product_block_center'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/product_image_class', 'ae_img_center'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/product_name_class', 'ae_products_name_center'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/product_description_class', 'ae_product_description'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/price_container_class', 'ae_price_container_center'),
        (NULL, 'default', '0', 'affinityitems/" . $path . "/price_class', 'ae_price_center');";
    $installer->run($sql);
}

$sql = "INSERT INTO `" . $this->getTable('core_config_data') . "` (`config_id`, `scope`, `scope_id`, `path`, `value`) 
        VALUES 
        (NULL, 'default', '0', 'affinityitems/security/login', ''),
        (NULL, 'default', '0', 'affinityitems/security/password', ''),
        (NULL, 'default', '0', 'affinityitems/security/site_id', ''),
        (NULL, 'default', '0', 'affinityitems/security/key', ''),
        (NULL, 'default', '0', 'affinityitems/security/conf_test_host', 'json.approval.v1.0.affinityitems.com'),
        (NULL, 'default', '0', 'affinityitems/security/conf_prod_host', 'json.production.affinityitems.com'),
        (NULL, 'default', '0', 'affinityitems/security/conf_port', '80'),
        (NULL, 'default', '0', 'affinityitems/general/dev_prod','0'),
        (NULL, 'default', '0', 'affinityitems/general/dev_prod_rel','0'),
        (NULL, 'default', '0', 'affinityitems/image_size/default', '125,125'),
        (NULL, 'default', '0', 'affinityitems/image_size/large', '458,458'),
        (NULL, 'default', '0', 'affinityitems/image_size/medium', '250,250'),
        (NULL, 'default', '0', 'affinityitems/image_size/small', '98,98'),
        (NULL, 'default', '0', 'affinityitems/advanced/sync_count', '300'),
        (NULL, 'default', '0', 'affinityitems/advanced/ae_css', ''),
        (NULL, 'default', '0', 'affinityitems/advanced/ae_tracking_js', 0),
        (NULL, 'default', '0', 'affinityitems/developer/log_level','3'),
        (NULL, 'default', '0', 'affinityitems/general/guest_percentage', '100');";

$installer->run($sql);

// add attribute to product
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ae_sync', array(
    'group' => 'General',
    'label' => 'AE synced',
    'type' => 'int',
    'input' => 'boolean',
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => 0,
    'required' => 0,
    'user_defined' => 0,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'unique' => 0,
    'default' => 0
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ae_sync_date', array(
    'group' => 'General',
    'label' => 'AE sync date',
    'input'         => 'text',
    'type'          => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'is_visible' => false,
    'required' => 0,
    'user_defined' => 0,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'unique' => 0
));

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY,'ae_sync_date','is_visible',false);

$installer->endSetup();


// add attribute to category
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId = $installer->getEntityTypeId('catalog_category');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'ae_sync', array(
    'group' => 'General Information',
    'type' => 'int',
    'label' => 'AE synced',
    'input' => 'select',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'default' => 0,
    'source' => 'eav/entity_attribute_source_boolean'
));

$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'ae_sync_date', array(
    'group' => 'General Information',
    'type' => 'text',
    'label' => 'AE sync date',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => false,
    'is_visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->updateAttribute(Mage_Catalog_Model_Category::ENTITY,'ae_sync_date','is_visible',false);

$installer->endSetup();

// add attribute to order table
$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$installer->addAttribute('order', 'ae_sync', array(
    'type' => 'int',
    'backend_type' => 'int',
    'is_user_defined' => true,
    'label' => 'AE synced',
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'default' => 0
));
$installer->addAttribute('order', 'ae_group', array(
    'type' => 'varchar',
    'backend_type' => '',
    'is_user_defined' => true,
    'label' => 'AE group',
    'visible' => false,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false
));

// add attribute to customer table
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('customer', 'ae_sync', array(
    'type' => 'int',
    'backend_type' => 'int',
    'input' => 'boolean',
    'source' => 'eav/entity_attribute_source_boolean',
    'is_user_defined' => true,
    'label' => 'AE synced',
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'default' => 0
));


$installer->endSetup();
