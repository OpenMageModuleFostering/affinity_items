<?php

class AffinityEngine_AffinityItems_Model_Log extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('affinityitems/log');
    }

    public function log($severity, $message) {
        $log_level = Mage::getStoreConfig('affinityitems/developer/log_level', 0);
        if ($log_level == '0' || ($log_level == '1' && $severity != "[INFO]") || ($log_level == '2' && $severity != "[ERROR]") || ($log_level != '4' && $severity == "[DEBUG]"))
            return;
        if ($log_level == 4) {
            switch ($severity) {
                case '[INFO]':
                    $ll = 6;
                    break;
                case '[ERROR]':
                    $ll = 3;
                    break;
                default:
                    $ll = 7;
            }
            Mage::log($message, $ll, 'AffinityDebug.log', true);
        } else {
            $this->setData(array('date_add' => new Zend_Db_Expr('NOW()'), 'severity' => $severity, 'message' => $message))->save();
        }
    }

}
