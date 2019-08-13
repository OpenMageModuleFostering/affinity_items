<?php

class AffinityEngine_AffinityItems_Model_Cart extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('affinityitems/cart');
    }

    public function cartrepo($id_cart, $id_product, $quantity, $action, $attributes = array(), $aeguestId, $aegroup, $ip, $aememberId = '', $language = '') {

        $test=$this->setData(
                array(
                    'id_cart' => $id_cart,
                    'date_add' => new Zend_Db_Expr('NOW()'),
                    'id_product' => $id_product,
                    'id_product_attribute' => (string) serialize($attributes),
                    'quantity' => $quantity,
                    'action' => $action,
                    'aeguestid' => $aeguestId,
                    'aememberid' => $aememberId,
                    'aegroup' => $aegroup,
                    'language' => $language,
                    'ip' => $ip
                )
        )->save();
    }

}
