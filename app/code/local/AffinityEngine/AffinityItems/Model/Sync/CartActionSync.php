<?php

class AffinityEngine_AffinityItems_Model_Sync_CartActionSync extends AffinityEngine_AffinityItems_Model_Sync_Sync {

    public function syncCartActions($sync_count = 300) {
        $countCartForSync = $this->getCartForSync()->count();
        $countPage = ceil($countCartForSync / $sync_count);
        for ($cPage = 0; $cPage <= ($countPage - 1); $cPage++) {
            $cartaction = $this->getCartForSync()->setPageSize($sync_count);
            $aecartList = array();
            foreach ($cartaction as $action) {
                $aecart = new stdClass();
                $aecart->context = $action->getAction();
                $aecart->id = $action->getIdCart();
                $aecart->addDate = $action->getDateAdd();
                $aecart->guestId = $action->getAeguestid();

                if($action->getAememberid() != '') {
                    $aecart->memberId = $action->getAememberid();
                }

                $aecart->productAttributesId = $action->getIdProduct();
                $aecart->ip = $action->getIp();
                $aecart->language = $action->getLanguage();

                $orderLine = new stdClass();
                $orderLine->productId = $action->getIdProduct();
                $orderLine->attributeIds = unserialize($action->getIdProductAttribute());
                $orderLine->quantity = (int) $action->getQuantity();

                $aecart->orderLine = $orderLine;
                $aecart->group = $action->getAegroup();
                array_push($aecartList, $aecart);
            }
            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_ActionRequest($aecartList);
            if ($request->post()) {
                foreach ($cartaction as $action) {
                    try {
                        Mage::getModel('affinityitems/cart')->setId($action->getId())->delete();
                        $this->logger->log('[INFO]', 'Synchronize action: ' . $action->getAction() . ' (Product ID:' . $action->getIdProduct() . ') [' . time() . ']');
                    } catch (Exception $e) {
                        $this->logger->log('[ERROR]', $e->getMessage());
                        $this->logger->log('[ERROR]', 'Synchronize action: ' . $action->getAction() . ' (Product ID: ' . $action->getIdProduct() . ') [' . time() . ']');
                    }
                }
            } else {
                $ids = array();
                foreach ($aecartList as $action) {
                    array_push($ids, $action->id);
                }
                $this->logger->log('[ERROR]', 'Synchronize of actions failed: (IDs: ' . implode(",", $ids) . ') [' . time() . ']');
            }
        }
    }

    public function syncCartFromObserver($action, $inRepo = false) {

        $aecart = new stdClass();
        $aecart->context = $action->getAction();
        $aecart->id = $action->getIdCart();
        $aecart->addDate = $action->getDateAdd();
        $aecart->guestId = $action->getAeguestid();

        if($action->getAememberid() != '') {
            $aecart->memberId = $action->getAememberid();
        }
        
        $aecart->productAttributesId = $action->getIdProduct();
        $aecart->ip = $action->getIp();
        $aecart->language = $action->getLanguage();

        $orderLine = new stdClass();
        $orderLine->productId = $action->getIdProduct();
        $orderLine->attributeIds = unserialize($action->getIdProductAttribute());
        $orderLine->quantity = (int) $action->getQuantity();

        $aecart->orderLine = $orderLine;
        $aecart->group = $action->getAegroup();

        $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_ActionRequest($aecart);
        if ($request->post()) {
            if ($inRepo) {
                try {
                    Mage::getModel('affinityitems/cart')->setId($action->getId())->delete();
                } catch (Exception $e) {
                    $this->logger->log('[ERROR]', $e->getMessage());
                }
            }
            $this->logger->log('[INFO]', 'Synchronize action: ' . $action->getAction() . ' (Product ID:' . $action->getIdProduct() . ') [' . time() . ']');
            return true;
        } else {
            $this->logger->log('[ERROR]', 'Synchronize action: ' . $action->getAction() . ' (Product ID: ' . $action->getIdProduct() . ') [' . time() . ']');
            return false;
        }
        return false;
    }

}
