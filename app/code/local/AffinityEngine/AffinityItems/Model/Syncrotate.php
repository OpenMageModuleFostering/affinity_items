<?php

class AffinityEngine_AffinityItems_Model_Syncrotate extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('affinityitems/syncrotate');
    }

    public function upd($sync) {
        $query = "UPDATE `" . Mage::getSingleton('core/resource')->getTableName('ae_sync_rotate') . "` SET  `last_sync` =  '" . $sync . "' WHERE  `ae_sync_rotate`.`id` =0;";
        $write = Mage::getSingleton('core/resource')->getConnection('affinityitems_write');
        $result = $write->query($query);
    }

    public function updInitial($value) {
        $query = "UPDATE `" . Mage::getSingleton('core/resource')->getTableName('ae_sync_rotate') . "` SET  `initial_sync` =  '" . $value . "' WHERE  `ae_sync_rotate`.`id` =0;";
        $write = Mage::getSingleton('core/resource')->getConnection('affinityitems_write');
        $result = $write->query($query);
    }

}
