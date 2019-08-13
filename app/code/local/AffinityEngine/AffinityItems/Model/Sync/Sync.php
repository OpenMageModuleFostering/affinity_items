<?php

class AffinityEngine_AffinityItems_Model_Sync_Sync extends Mage_Core_Model_Abstract {

    public function __construct() {
        $this->logger = Mage::getModel('affinityitems/log');
    }

    public function getProductsForSync() {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('entity_id')
                ->addStoreFilter(0)
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
        return $collection;
    }
    
    public function getCategoriesForSync() {
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToSelect('entity_id')
                ->addIsActiveFilter()
                ->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync', 'null' => true),
                    array('attribute' => 'ae_sync', 'eq' => 0),
                        ), '', 'left');
        return $collection;
    }
    
    public function getDeletedCategoriesForSync() {
        $collection = Mage::getModel('affinityitems/catProdRepo')->getCollection()->addFieldToFilter('obj_type', 'category');
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

      return $collection;
    }

    public function getOrdersForSync() {
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addFieldToFilter('state', array('in' => array('new', 'pending_payment', 'processing', 'complete', 'payment_review')))
                ->addFieldToFilter('ae_sync', array('in' => array(null, 0)));
        return $collection;
    }

    public function getActionsForSync() {
        $collection = Mage::getModel("affinityitems/action")->getCollection();
        return $collection;
    }

    public function getCartForSync() {
        $collection = Mage::getModel("affinityitems/cart")->getCollection();
        return $collection;
    }

    public function getAllStores() {
        return Mage::app()->getStores();
    }

}
