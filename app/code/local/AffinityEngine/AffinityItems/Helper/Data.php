<?php

class AffinityEngine_AffinityItems_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH = 'affinityitems/';
    const ENABLE = '/enable';

    public function isEnabled() {
        return (bool) ($this->isAeEnabled() && Mage::getStoreConfig(self::XML_PATH . $this->getXmlPath() . self::ENABLE));
    }

    public function isLeftEnabled() {
        return (bool) ($this->isAeEnabled() && Mage::getStoreConfig(self::XML_PATH . 'items_left' . self::ENABLE));
    }

    public function isRightEnabled() {
        return (bool) ($this->isAeEnabled() && Mage::getStoreConfig(self::XML_PATH . 'items_right' . self::ENABLE));
    }

    public function isSideEnabled() {
        return ($this->isRightEnabled() || $this->isLeftEnabled());
    }

    public function isAeEnabled() {
        return (bool) Mage::getStoreConfig(self::XML_PATH . 'general/enable');
    }

    public function getXmlPath() {
        return 'items_' . $this->getCurrentPage();
    }

    public function getCurrentPage() {
        $path = Mage::app()->getRequest()->getRouteName() . Mage::app()->getRequest()->getControllerName();
        switch ($path) {
            case 'cmsindex':
                return "home";
                break;
            case 'catalogcategory':
                return "category";
                break;
            case 'catalogproduct':
                return "product";
                break;
            case 'checkoutcart':
                return "cart";
                break;
            case 'catalogsearchresult':
                return "search";
                break;
        }
        return 'category';
    }

    public function getExtensionVersion() {
        return (string) Mage::getConfig()->getNode()->modules->AffinityEngine_AffinityItems->version;
    }

    public function getConfig($endPath = false) {
        if (!$endPath)
            return;
        $value = Mage::getStoreConfig(self::XML_PATH . $this->getXmlPath() . '/' . $endPath);
        return (strpos($value, ',') !== false) ? explode(",", $value) : $value;
    }

    public function getGeneral($endPath = false, $websiteId = 0) {
        if (!$endPath)
            return;
        $value = Mage::getStoreConfig(self::XML_PATH . 'general/' . $endPath, $websiteId);
        return $value;
    }

    public function getAdvanced($endPath = false) {
        if (!$endPath)
            return;
        $value = Mage::getStoreConfig(self::XML_PATH . 'advanced/' . $endPath);
        return $value;
    }

    public function getLeftNOP($endPath = false) {
        if (!$endPath)
            return;
        $value = Mage::getStoreConfig(self::XML_PATH . 'items_left/' . $endPath);
        return (strpos($value, ',') !== false) ? explode(",", $value) : $value;
    }

    public function getRightNOP($endPath = false) {
        if (!$endPath)
            return;
        $value = Mage::getStoreConfig(self::XML_PATH . 'items_right/' . $endPath);
        return (strpos($value, ',') !== false) ? explode(",", $value) : $value;
    }

    public function getId($endPath = false) {
        if (!$endPath)
            return;
        $value = $this->getConfig($endPath);
        if (is_array($value) && array_key_exists(0, $value))
            return $value[0];
        return $value;
    }

    public function getClass($endPath = false) {
        if (!$endPath)
            return;
        $value = $this->getConfig($endPath);
        if (is_array($value) && array_key_exists(1, $value))
            return $value[1];
        return $value;
    }

    public function getImageSize() {
        $i_type = $this->getImageType();
        $value = Mage::getStoreConfig(self::XML_PATH . 'image_size/' . $i_type[$this->getConfig('image_size')]);
        return (strpos($value, ',') !== false) ? explode(",", $value) : $value;
    }

    public function getLImageSize() {
        $i_type = $this->getImageType();
        $conf = Mage::getStoreConfig(self::XML_PATH . 'items_left/image_size');
        $value = Mage::getStoreConfig(self::XML_PATH . 'image_size/' . $i_type[$conf]);
        return (strpos($value, ',') !== false) ? explode(",", $value) : $value;
    }

    public function getRImageSize() {
        $i_type = $this->getImageType();
        $conf = Mage::getStoreConfig(self::XML_PATH . 'items_right/image_size');
        $value = Mage::getStoreConfig(self::XML_PATH . 'image_size/' . $i_type[$conf]);
        return (strpos($value, ',') !== false) ? explode(",", $value) : $value;
    }

    public function getImageType() {
        return Array(0 => 'default', 1 => 'large', 2 => 'medium', 3 => 'small');
    }

    public function getSideTitle($side = false) {
        if (!$side)
            return;
        return ($side == 'ae_vertical_left') ? Mage::getStoreConfig(self::XML_PATH . 'items_left/recommendation_label') : Mage::getStoreConfig(self::XML_PATH . 'items_right/recommendation_label');
    }

    public function getFullProductUrl($product) {
        if (is_object($product) && $product->getSku()) {
            // first try SQL approach
            try {
                $cats = $product->getCategoryIds();
                $query = "
                SELECT `request_path` 
                FROM `" . Mage::getSingleton('core/resource')->getTableName('core_url_rewrite') . "` 
                WHERE `product_id`='" . $product->getEntityId() . "' 
                AND `category_id`='" . end($cats) . "' 
                AND `store_id`='" . Mage::app()->getStore()->getId() . "';
                ";
                $read = Mage::getSingleton('core/resource')->getConnection('affinityitems_read');
                $result = $read->fetchRow($query);
                if (!$result)
                    throw new Exception('no record in db');
                return Mage::getUrl('') . $result['request_path'];
            }
            // if it fails, than use failsafe way with category object loading
            catch (Exception $e) {
                $allCategoryIds = $product->getCategoryIds();
                $lastCategoryId = end($allCategoryIds);
                $lastCategory = Mage::getModel('catalog/category')->load($lastCategoryId);
                $lastCategoryUrl = $lastCategory->getUrl();
                $fullProductUrl = str_replace(Mage::getStoreConfig('catalog/seo/category_url_suffix'), '/', $lastCategoryUrl) . basename($product->getUrlKey()) . Mage::getStoreConfig('catalog/seo/product_url_suffix');
                if (strpos($fullProductUrl, 'catalog/category/view')) {
                    $fullProductUrl = basename($product->getUrlKey()) . Mage::getStoreConfig('catalog/seo/product_url_suffix');
                }
                return $fullProductUrl;
            }
        }
        return;
    }

    public function getFormatedPrice($product) {
        $blockname = (Mage::registry('current_product')) ? 'view' : 'list';
        return $this->getLayout()->createBlock('catalog/product_' . $blockname)->setProd($product)->setTemplate('affinityengine/pricehtml.phtml')->toHtml();
    }

    public function log($severity, $message) {
        Mage::getModel('affinityitems/log')->log($severity, $message);
        return;
    }

    public function isAdmin() {
        if (Mage::app()->getStore()->isAdmin()) {
            return true;
        }

        if (Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }
        return false;
    }

    public function generateGuest() {
        $aeguest = str_replace('.', '', uniqid('ae', true));
        Mage::getModel('core/cookie')->set('aeguest', $aeguest, 630720000);
        return;
    }

    public function isModuleEnabledAndRegistered($websiteId = 0) {
        return (bool) Mage::helper('affinityitems/aeadapter')->isRegistered() && $this->getWebsiteConfig('general/enable', $websiteId);
    }

    public function canDisplayProduct($product) {
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        return (bool) (!$product->getStatus() == 2 || $product->getVisibility() == 1 || $stock->getIsInStock() == 0 || count($product->getCategoryIds()) == 0 );
    }

    public function getWebsiteConfig($path, $website_id) {
        return Mage::app()->getWebsite($website_id)->getConfig(self::XML_PATH . $path);
    }

    public function getIp() {
        return long2ip(Mage::helper('core/http')->getRemoteAddr(true));
    }

    public function getMemberId() {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            return $customerData->getId();
        } else {
            return '';
        }
    }

    public function getLang() {
        return substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
    }
    
    public function deleteCategorySync() {
        $store_id = Mage::getModel('affinityitems/sync_sync')->getStoreIdByWebsiteId();
        $rootId = Mage::app()->getStore()->getRootCategoryId();
        $categories = Mage::getModel('catalog/category')->getCollection();
        $categories->addIsActiveFilter()
                ->addFieldToFilter('path', array('like' => "1/" . $rootId . "/%"))
                ->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync', 'null' => false),
                    array('attribute' => 'ae_sync', 'eq' => 1),
                        ), '', 'left');
        $categories->setDataToAll('ae_sync', 0)->setDataToAll('observer', true)->setDataToAll('ae_sync_date', null)->save();
    }

    public function deleteProductSync() {
        $products = Mage::getModel('catalog/product')->getCollection();
        $products->addStoreFilter(Mage::getModel('affinityitems/sync_sync')->getStoreIdByWebsiteId())
                ->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync', 'null' => false),
                    array('attribute' => 'ae_sync', 'eq' => 1),
                        ), '', 'left');
        $products->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        $products->setDataToAll('ae_sync', 0)->setDataToAll('observer', true)->setDataToAll('ae_sync_date', null)->save();
    }

    public function deleteOrderSync() {
        $store_filter = (Mage::registry('website_id')) ?  " WHERE `store_name` LIKE '%".Mage::app()->getWebsite(Mage::registry('website_id'))->getName()."%';" : ";";
        $update_query = "UPDATE `" . Mage::getSingleton('core/resource')->getTableName('sales_flat_order') . "` SET `ae_sync`='0' ".$store_filter;
        $db_write = Mage::getSingleton('core/resource')->getConnection('affinityitems_write');
        $result = $db_write->query($update_query);
    }

    public function deleteMemberSync() {
        $members = Mage::getModel('customer/customer')->getCollection();
        $website_id = (Mage::registry('website_id')) ? Mage::registry('website_id') : 0;
        $members->addAttributeToFilter(
                array(
            array('attribute' => 'ae_sync', 'null' => false),
            array('attribute' => 'ae_sync', 'eq' => 1),
                ), '', 'left')
                ->addFieldToFilter('website_id', $website_id);
        $members->setDataToAll('ae_sync', 0)->save();
    }

    public function deleteCartSync() {
        $store_filter = (Mage::registry('website_id')) ?  " WHERE `website_id` = '".Mage::registry('website_id')."';" : ";";
        $update_query = "DELETE FROM `" . Mage::getSingleton('core/resource')->getTableName('ae_cart_repository') . "` ".$store_filter;
        $db_write = Mage::getSingleton('core/resource')->getConnection('affinityitems_write');
        $result = $db_write->query($update_query);
    }

    public function deleteActionSync() {
        $store_filter = (Mage::registry('website_id')) ?  " WHERE `website_id` = '".Mage::registry('website_id')."';" : ";";
        $update_query = "DELETE FROM `" . Mage::getSingleton('core/resource')->getTableName('ae_guest_action_repository') . "` ".$store_filter;
        $db_write = Mage::getSingleton('core/resource')->getConnection('affinityitems_write');
        $result = $db_write->query($update_query);
    }

    public function deleteAllSync() {
        $this->deleteCategorySync();
        $this->deleteProductSync();
        $this->deleteMemberSync();
        $this->deleteCartSync();
        $this->deleteActionSync();
    }

}
