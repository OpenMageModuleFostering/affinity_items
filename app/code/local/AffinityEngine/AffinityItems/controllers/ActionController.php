<?php

class AffinityEngine_AffinityItems_ActionController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {

        $this->_redirect('/');
    }

    public function ajaxAction() {
        if (Mage::helper('affinityitems')->isModuleEnabledAndRegistered()) {
            $rec_type = $this->getRequest()->getPost('recoType');
            $rec_type = isset($rec_type) ? $rec_type : '';
            
            $action = new Varien_Object();
            
            if(!is_null($this->getRequest()->getPost('productId'))) {
                $action->setProductId($this->getRequest()->getPost('productId'));
            }

            if(!is_null($this->getRequest()->getPost('categoryId'))) {
                $action->setCategoryId($this->getRequest()->getPost('categoryId'));
            }

            $action->setIp(Mage::helper('affinityitems')->getIp());
            $action->setAeAction($this->getRequest()->getPost('action'));

            $action->setLanguage(Mage::helper('affinityitems')->getLang());

            $action->setAeGuest(($this->getRequest()->getPost('guestId') != "") ? $this->getRequest()->getPost('guestId') : Mage::getModel('core/cookie')->get('aeguest'));
            $action->setAeMember(Mage::helper('affinityitems')->getMemberId());
            $action->setAeGroup(($this->getRequest()->getPost('group') != "") ? $this->getRequest()->getPost('group') : Mage::getModel('core/cookie')->get('aegroup'));
            $action->setRecType($this->getRequest()->getPost('recoType'));
            
            if (!Mage::getModel('affinityitems/sync_actionSync')->syncActionFromObserver($action)) {

                if(!is_null($this->getRequest()->getPost('productId'))) {
                    Mage::getModel("affinityitems/action")->setData(
                            array(
                                'product_id' => $action->getProductId(),
                                'category_id' => 0,
                                'ae_action' => $action->getAeAction(),
                                'ae_guest' => $action->getAeGuest(),
                                'ae_member' => $action->getAeMember(),
                                'language' => $action->getLanguage(),
                                'ae_group' => $action->getAeGroup(),
                                'rec_type' => $action->getRecType(),
                                'ip' => $action->getIp()
                            )
                    )->save();
                }

                if(!is_null($this->getRequest()->getPost('categoryId'))) {
                    Mage::getModel("affinityitems/action")->setData(
                            array(
                                'product_id' => 0,
                                'category_id' => $action->getCategoryId(),
                                'ae_action' => $action->getAeAction(),
                                'ae_guest' => $action->getAeGuest(),
                                'language' => $action->getLanguage(),
                                'ae_member' => $action->getAeMember(),                                
                                'ae_group' => $action->getAeGroup(),
                                'rec_type' => $action->getRecType(),
                                'ip' => $action->getIp()
                            )
                    )->save();
                }

            }
        }
    }

}
