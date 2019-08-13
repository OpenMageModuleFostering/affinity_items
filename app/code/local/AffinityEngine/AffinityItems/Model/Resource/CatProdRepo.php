<?php
 
class AffinityEngine_AffinityItems_Model_Resource_CatProdRepo extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('affinityitems/ae_cat_prod_repository', 'id');
    }
}