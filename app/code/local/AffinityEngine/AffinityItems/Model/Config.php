<?php

class AffinityEngine_AffinityItems_Model_Config extends Mage_Core_Model_Abstract {

    public function getValue($path, $storeId = 0) {
        $query = "
            SELECT `value` 
            FROM `" . Mage::getSingleton('core/resource')->getTableName('core_config_data') . "` 
            WHERE `scope_id`='" . $storeId . "' 
            AND  `path` = '" . $path . "' ;";
        $read = Mage::getSingleton('core/resource')->getConnection('affinityitems_read');
        $result = $read->fetchRow($query);
        if (!$result) return false;
        return $result['value'];
    }

}
