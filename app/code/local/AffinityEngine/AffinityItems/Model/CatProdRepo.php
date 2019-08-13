<?php

class AffinityEngine_AffinityItems_Model_CatProdRepo extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('affinityitems/catProdRepo');
    }

    public function addToCatProdRepo($obj_id, $obj_type, $websiteId) {

        $test=$this->setData(
                array(
                    'obj_id' => $obj_id,
                    'obj_type' => $obj_type,
                    'website_id' => $websiteId
                )
        )->save();
    }

}
