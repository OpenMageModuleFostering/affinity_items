<?php

class AffinityEngine_AffinityItems_Model_Syncrotate extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('affinityitems/syncrotate');
    }

    public function upd($sync, $website_id) {
        $query = "UPDATE `" . Mage::getSingleton('core/resource')->getTableName('ae_sync_rotate') . "` SET  `last_sync` =  '" . $sync . "' WHERE  `ae_sync_rotate`.`website_id` =" . $website_id . ";";
        $write = Mage::getSingleton('core/resource')->getConnection('affinityitems_write');
        $result = $write->query($query);
    }

    public function updInitial($value, $website_id) {
        $query = "UPDATE `" . Mage::getSingleton('core/resource')->getTableName('ae_sync_rotate') . "` SET  `initial_sync` =  '" . $value . "' WHERE  `ae_sync_rotate`.`website_id` =" . $website_id . ";";
        $write = Mage::getSingleton('core/resource')->getConnection('affinityitems_write');
        $result = $write->query($query);
    }

    public function updReg($value, $website_id) {
        $query = "UPDATE `" . Mage::getSingleton('core/resource')->getTableName('ae_sync_rotate') . "` SET  `website_reg` =  '" . $value . "' WHERE  `ae_sync_rotate`.`website_id` =" . $website_id . ";";
        $write = Mage::getSingleton('core/resource')->getConnection('affinityitems_write');
        $result = $write->query($query);
    }

}
