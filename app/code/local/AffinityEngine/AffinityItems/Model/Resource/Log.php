<?php
 
class AffinityEngine_AffinityItems_Model_Resource_Log extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('affinityitems/ae_log', 'id_log');
    }
}