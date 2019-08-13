<?php

class AffinityEngine_AffinityItems_Model_Sync_Sync extends Mage_Core_Model_Abstract {

    public function __construct() {
        $this->logger = Mage::getModel('affinityitems/log');
        $this->helper = Mage::helper('affinityitems');
    }

    public function getStoreIdByWebsiteId() {
        return (Mage::registry('website_id')) ? Mage::app()->getWebsite(Mage::registry('website_id'))->getDefaultGroup()->getDefaultStoreId() : 0;
    }

    public function getProductsForSync() {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('entity_id')
                ->addStoreFilter($this->getStoreIdByWebsiteId())
                ->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync', 'null' => true),
                    array('attribute' => 'ae_sync', 'eq' => 0),
                        ), '', 'left');
        $collection->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        return $collection;
    }

    public function getDeletedProductsForSync() {
        $collection = Mage::getModel('affinityitems/catProdRepo')->getCollection()->addFieldToFilter('obj_type', 'product');
        if (Mage::registry('website_id')) {
            $collection->addFieldToFilter('website_id', Mage::registry('website_id'));
        }

        return $collection;
    }

    public function getCategoriesForSync() {
        $rootId = Mage::app()->getStore($this->getStoreIdByWebsiteId())->getRootCategoryId();
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToSelect('entity_id')
                ->addIsActiveFilter()
                ->addFieldToFilter('path', array('like' => "1/" . $rootId . "/%"))
                ->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync', 'null' => true),
                    array('attribute' => 'ae_sync', 'eq' => 0),
                        ), '', 'left');
        return $collection;
    }

    public function getDeletedCategoriesForSync() {
        $collection = Mage::getModel('affinityitems/catProdRepo')->getCollection()->addFieldToFilter('obj_type', 'category');
        if (Mage::registry('website_id')) {
            $collection->addFieldToFilter('website_id', Mage::registry('website_id'));
        }

        return $collection;
    }

    public function getMembersForSync() {
        $collection = Mage::getModel('customer/customer')->getCollection();
        $collection->addAttributeToSelect('entity_id')
                ->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync', 'null' => true),
                    array('attribute' => 'ae_sync', 'eq' => 0),
                        ), '', 'left');
        if (Mage::registry('website_id')) {
            $collection->addFieldToFilter('website_id', Mage::registry('website_id'));
        }
        return $collection;
    }

    public function getOrdersForSync() {
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addFieldToFilter('state', array('in' => array('new', 'pending_payment', 'processing', 'complete', 'payment_review')))
                ->addFieldToFilter('created_at', array(
                    'from' => strtotime('-6 month', time()),
                    'to' => time(),
                    'datetime' => true
                ))
                ->addFieldToFilter('ae_sync', array('in' => array(null, 0)));
        if (Mage::registry('website_id')) {
            $collection->addAttributeToFilter('store_name', array('like' => '%' . Mage::app()->getWebsite(Mage::registry('website_id'))->getName() . '%'));
        }
        return $collection;
    }

    public function getActionsForSync() {
        $collection = Mage::getModel("affinityitems/action")->getCollection();
        if (Mage::registry('website_id')) {
            $collection->addFieldToFilter('website_id', Mage::registry('website_id'));
        }
        return $collection;
    }

    public function getCartForSync() {
        $collection = Mage::getModel("affinityitems/cart")->getCollection();
        if (Mage::registry('website_id')) {
            $collection->addFieldToFilter('website_id', Mage::registry('website_id'));
        }
        return $collection;
    }

    public function getAllStores() {
        return Mage::app()->getStores();
    }

}
