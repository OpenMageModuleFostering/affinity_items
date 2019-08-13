<?php

class AffinityEngine_AffinityItems_Adminhtml_AffinityitemsbackendController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->_title($this->__("AffinityItems"));
        $this->_setActiveMenu('affinityengine/affinityitems');
        if (Mage::helper('affinityitems/aeadapter')->isRegistered()) {
             $this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('affinityitems/affinityitems_sync.phtml'));
        } else {
             $this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('affinityitems/affinityitems_login.phtml'));
        }
        $this->renderLayout();
    }

}
