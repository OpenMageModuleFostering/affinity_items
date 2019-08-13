<?php
 
class AffinityEngine_AffinityItems_Model_Resource_Action extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('affinityitems/ae_guest_action_repository', 'id');
    }
}