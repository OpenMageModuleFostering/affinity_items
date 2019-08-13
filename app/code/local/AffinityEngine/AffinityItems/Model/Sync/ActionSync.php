<?php

class AffinityEngine_AffinityItems_Model_Sync_ActionSync extends AffinityEngine_AffinityItems_Model_Sync_Sync {

    public function syncActions($sync_count = 300) {
        $countActionsForSync = $this->getActionsForSync()->count();
        $countPage = ceil($countActionsForSync / $sync_count);
        for ($cPage = 0; $cPage <= ($countPage - 1); $cPage++) {
            $actions = $this->getActionsForSync()->setPageSize($sync_count);
            $aeactionsList = array();
            foreach ($actions as $action) {
                $aeaction = new stdClass();

                if($action->getProductId() != 0)
                    $aeaction->productId = $action->getProductId();
                if($action->getCategoryId() != 0)
                    $aeaction->categoryId = $action->getCategoryId();

                $aeaction->ip = $action->getIp();
                $aeaction->context = $action->getAeAction();
                $aeaction->guestId = $action->getAeGuest();

                if($action->getAeMember() != '') {
                    $aeaction->memberId = $action->getAeMember();
                }

                $aeaction->language = $action->getLanguage();

                $aeaction->group = $action->getAeGroup();
                if ($action->getRecType())
                    $aeaction->recoType = strtoupper($action->getRecType());
                array_push($aeactionsList, $aeaction);
            }
            
            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_ActionRequest($aeactionsList);
            if ($request->post()) {
                foreach ($actions as $action) {
                    try {
                        Mage::getModel('affinityitems/action')->setId($action->getId())->delete();
                        //$this->logger->log('[INFO]', 'Synchronize action : ' . $action->getAction() . '[' . time() . ']');
                    } catch (Exception $e) {
                        $this->logger->log('[ERROR]', $e->getMessage());
                        //$this->logger->log('[ERROR]', 'Synchronize action : ' . $action->getAction() . '[' . time() . ']');
                    }
                }
            } else {
               $this->logger->log('[ERROR]', 'Synchronize action failed');
            }
        }
    }

    public function syncActionFromObserver($action, $inRepo = false) {
        $aeaction = new stdClass();

        if(!is_null($action->productId))
            $aeaction->productId = $action->getProductId();
        if(!is_null($action->categoryId))
            $aeaction->categoryId = $action->getCategoryId();

        $aeaction->context = $action->getAeAction();
        $aeaction->guestId = $action->getAeGuest();

        if($action->getAeMember() != '') {
            $aeaction->memberId = $action->getAeMember();
        }

        $aeaction->language = $action->getLanguage();
        $aeaction->ip = $action->getIp();
        $aeaction->group = $action->getAeGroup();
        if ($action->getRecType())
            $aeaction->recoType = strtoupper($action->getRecType());

        $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_ActionRequest($aeaction);
        if ($request->post()) {
            if ($inRepo) {
                try {
                    Mage::getModel('affinityitems/action')->setId($action->getId())->delete();
                } catch (Exception $e) {
                    $this->logger->log('[ERROR]', $e->getMessage());
                }
            }
            //$this->logger->log('[INFO]', 'Synchronize action: ' . $action->getAction() . '[' . time() . ']');
            return true;
        } else {
            //$this->logger->log('[ERROR]', 'Synchronize action: ' . $action->getAction() . '[' . time() . ']');
            return false;
        }
        return false;
    }

}
