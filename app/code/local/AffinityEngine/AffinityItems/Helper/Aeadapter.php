<?php

class AffinityEngine_AffinityItems_Helper_Aeadapter extends Mage_Core_Helper_Abstract {

    public function authentication($email, $password, $site_id, $security_key) {
        $ae_config = new Mage_Core_Model_Config();
        $ae_config->saveConfig('affinityitems/security/login', $email, 'default', 0);
        $ae_config->saveConfig('affinityitems/security/password', 'empty', 'default', 0);
        $ae_config->saveConfig('affinityitems/security/site_id', $site_id, 'default', 0);
        $ae_config->saveConfig('affinityitems/security/key', $security_key, 'default', 0);
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
        return $this->getConfigFromDb('affinityitems/security/site_id');
    }

    public function getLogin() {
        return $this->getConfigFromDb('affinityitems/security/login');
    }

    public function getPassowrd() {
        return $this->getConfigFromDb('affinityitems/security/password');
    }

    public function getSecurityKey() {
        return $this->getConfigFromDb('affinityitems/security/key');
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
        if (!$value) return false;
            switch ($value) {
                case 'categories':
                    return (count(Mage::getModel('affinityitems/sync_sync')->getCategoriesForSync()) > 0 || count(Mage::getModel('affinityitems/sync_sync')->getDeletedCategoriesForSync()) > 0) ? false : true;
                    break;
                case 'products':
                    return (count(Mage::getModel('affinityitems/sync_sync')->getProductsForSync()) > 0 || count(Mage::getModel('affinityitems/sync_sync')->getDeletedProductsForSync()) > 0) ? false : true;
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
    
    public function getConfigFromDb($path = false) {
        return ($path) ? Mage::getModel('affinityitems/config')->getValue($path) : false;
    }

}
