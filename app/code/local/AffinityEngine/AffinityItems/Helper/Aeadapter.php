<?php

class AffinityEngine_AffinityItems_Helper_Aeadapter extends Mage_Core_Helper_Abstract {

    public function authentication($email, $password, $site_id, $security_key, $website_id = 0) {
        $ae_config = new Mage_Core_Model_Config();
        $ae_config->saveConfig('affinityitems/security/' . $this->getAuthPrefix() . 'login', $email, 'websites', $website_id);
        $ae_config->saveConfig('affinityitems/security/' . $this->getAuthPrefix() . 'password', 'empty', 'websites', $website_id);
        $ae_config->saveConfig('affinityitems/security/' . $this->getAuthPrefix() . 'site_id', $site_id, 'websites', $website_id);
        $ae_config->saveConfig('affinityitems/security/' . $this->getAuthPrefix() . 'key', $security_key, 'websites', $website_id);
    }

    public function getAuthPrefix() {
        return ($this->getEnv()) ? 'prod_' : 'test_';
    }

    public function getEnv() {
        return $this->getConfigFromDb('affinityitems/general/dev_prod');
    }

    public function getTestHost() {
        return $this->getConfigFromDb('affinityitems/security/conf_test_host');
    }

    public function getProdHost() {
        return $this->getConfigFromDb('affinityitems/security/conf_prod_host');
    }

    public function getPort() {
        return $this->getConfigFromDb('affinityitems/security/conf_port');
    }

    public function getSiteId() {
        return $this->getConfigFromDb('affinityitems/security/' . $this->getAuthPrefix() . 'site_id');
    }

    public function getLogin() {
        return $this->getConfigFromDb('affinityitems/security/' . $this->getAuthPrefix() . 'login');
    }

    public function getPassowrd() {
        return $this->getConfigFromDb('affinityitems/security/' . $this->getAuthPrefix() . 'password');
    }

    public function getSecurityKey() {
        return $this->getConfigFromDb('affinityitems/security/' . $this->getAuthPrefix() . 'key');
    }

    public function getRegisterUrl() {
        return Mage::helper("adminhtml")->getUrl("affinityitems/adminhtml_ajax/register");
    }

    public function getLoginUrl() {
        return Mage::helper("adminhtml")->getUrl("affinityitems/adminhtml_ajax/login");
    }

    public function isRegistered() {
        return ($this->getLogin() && $this->getPassowrd() && $this->getSiteId() && $this->getSecurityKey()) ? true : false;
    }

    public function checkSync($value = false) {
        if (!$value)
            return false;
        switch ($value) {
            case 'categories':
                return (count(Mage::getModel('affinityitems/sync_sync')->getCategoriesForSync()) > 0 || count(Mage::getModel('affinityitems/sync_sync')->getDeletedCategoriesForSync()) > 0) ? false : true;
                break;
            case 'products':
                return (count(Mage::getModel('affinityitems/sync_sync')->getProductsForSync()) > 0 || count(Mage::getModel('affinityitems/sync_sync')->getDeletedProductsForSync()) > 0) ? false : true;
                break;
            case 'members':
                return (count(Mage::getModel('affinityitems/sync_sync')->getMembersForSync()) > 0) ? false : true;
                break;  
            case 'orders':
                return (count(Mage::getModel('affinityitems/sync_sync')->getOrdersForSync()) > 0) ? false : true;
                break;
            case 'cart':
                return (count(Mage::getModel('affinityitems/sync_sync')->getCartForSync()) > 0) ? false : true;
                break;
            case 'actions':
                return (count(Mage::getModel('affinityitems/sync_sync')->getActionsForSync()) > 0) ? false : true;
                break;
            default:
                return false;
                break;
        }
    }

    public function getHost() {
        return ($this->getConfigFromDb('affinityitems/general/dev_prod')) ? $this->getProdHost() : $this->getTestHost();
    }

    public function getWebsiteId() {
        if (Mage::app()->getRequest()->getParam('website_id')) {
            return Mage::app()->getRequest()->getParam('website_id');
        } elseif (Mage::registry('website_id')) {
            return Mage::registry('website_id');
        } elseif (!Mage::app()->getStore()->isAdmin() && Mage::getDesign()->getArea() != 'adminhtml') {
            return Mage::app()->getWebsite()->getId();
        }
        return 0;
    }

    public function getConfigFromDb($path = false) {
        return ($path) ? Mage::getModel('affinityitems/config')->getValue($path, $this->getWebsiteId()) : false;
    }

    public function getWebsiteIdByRootCategoryId($rootId) {
        $update_query = "SELECT `website_id` FROM `" . Mage::getSingleton('core/resource')->getTableName('core_store_group') . "` WHERE `root_category_id` = '" . $rootId . "';";
        $db_read = Mage::getSingleton('core/resource')->getConnection('affinityitems_read');
        $result = $db_read->fetchOne($update_query);
        return $result;
    }

    public function registerWebsiteId($website_id) {
            Mage::unregister('website_id');
            Mage::register('website_id', $website_id);
        return;
    }

}
