<?php
 
class AffinityEngine_AffinityItems_Model_Resource_Syncrotate extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('affinityitems/ae_sync_rotate', 'id');
    }
}