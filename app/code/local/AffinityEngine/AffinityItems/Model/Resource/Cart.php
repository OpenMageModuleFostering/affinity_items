<?php
 
class AffinityEngine_AffinityItems_Model_Resource_Cart extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('affinityitems/ae_cart_repository', 'id');
    }
}