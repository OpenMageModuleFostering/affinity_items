<?php

class AffinityEngine_AffinityItems_Model_Cron {

    const PROCESS_ID = 'Affinity_Sync';

    private $indexProcess;

    public function __construct() {
        $this->indexProcess = new Mage_Index_Model_Process();
        $this->indexProcess->setId(self::PROCESS_ID);
        $this->isShell = Mage::registry('run_from_shell');
        $this->helper = Mage::helper('affinityitems');
        $this->logger = Mage::getModel('affinityitems/log');
    }

    public function getWebsiteForSync() {
        //get all websites
        $syncrotate_model = Mage::getModel('affinityitems/syncrotate');
        $_websites = Mage::app()->getWebsites();
        foreach ($_websites as $website) {
            Mage::register('website_id', $website->getId());
            if ($this->helper->isModuleEnabledAndRegistered($website->getId())) {
                $syncrotate_model->updReg(1, $website->getId());
            } else {
                $syncrotate_model->updReg(0, $website->getId());
            }
            Mage::unregister('website_id');
        }
        $sync_website_collection = $syncrotate_model->getCollection();
        foreach ($sync_website_collection as $sync_item) {
            if ($sync_item->getInitalSync() && $sync_item->getWebsiteReg())
                return $sync_item->getWebsiteId();
        }
        foreach ($sync_website_collection as $sync_item) {
            if ($sync_item->getWebsiteReg())
                return $sync_item->getWebsiteId();
        }
    }

    public function Sync() {
        $website_id = $this->getWebsiteForSync();
        $this->logger->log("[INFO]", "AE Cron start for website ". $website_id ." [" . time() . "]");
        Mage::register('website_id', $website_id);
        if (!$this->helper->isModuleEnabledAndRegistered($website_id)) {
            $this->logger->log("[INFO]", "You are not logged in to Affinity, or extension is not enabled ! [" . time() . "]");
            if ($this->isShell)
                echo "You are not logged in to Affinity, or extension is not enabled !" . "\n";
            return false;
        }
        if ($this->indexProcess->isLocked()) {
            $this->logger->log("[INFO]", "Another AE Sync process is running! [" . time() . "]");
            if ($this->isShell)
                echo "Another AE Sync process is running!" . "\n";
            return false;
        }
        if ($this->isShell)
            echo "Setting locks" . "\n";

        $this->logger->log("[INFO]", "Setting locks [" . time() . "]");

        $this->indexProcess->lockAndBlock();

        $sync_model = Mage::getModel('affinityitems/sync_sync');
        $syncrotate_model = Mage::getModel('affinityitems/syncrotate');
        $last_sync = (count($syncrotate_model->getCollection())) ? $syncrotate_model->getCollection()->addFieldToFilter('website_id', $website_id)->getFirstItem()->getData('last_sync') : 'actions';
        $initialsync = (count($syncrotate_model->getCollection())) ? (bool) $syncrotate_model->getCollection()->addFieldToFilter('website_id', $website_id)->getFirstItem()->getData('initial_sync') : true;
        
        $sync_count = $this->helper->getAdvanced('sync_count');

        if ($initialsync) {
            if ($this->isShell)
                echo " - Initial sync in progress..." . "\n";
            $this->logger->log("[INFO]", "Initial sync in progress...! [" . time() . "]");
        }
        $resyncRequest = new AffinityEngine_AffinityItems_Model_Sdk_Request_ResynchronizeRequest(array());
        $elementList = $resyncRequest->get();
        if (isset($elementList['synchro'])) {
            if (is_array($elementList['synchro'])) {
                foreach ($elementList['synchro'] as $element) {
                    if ($this->isShell)
                        echo " - Resync process " . ucfirst(strtolower($element)) . "..." . "\n";
                    switch ($element) {
                        case 'CATEGORY':
                            $this->helper->deleteCategorySync();
                            break;
                        case 'PRODUCT':
                            $this->helper->deleteProductSync();
                            break;
                        case 'MEMBER':
                            $this->helper->deleteMemberSync();
                            break;
                        case 'CART':
                            $this->helper->deleteCartSync();
                            break;
                        case 'ORDER':
                            $this->helper->deleteOrderSync();
                            break;
                        case 'ACTION':
                            $this->helper->deleteActionSync();
                            break;
                    }
                }
            }
        }

        if (isset($elementList['abRatio'])) {
            Mage::getModel('core/config')->saveConfig('affinityitems/general/guest_percentage', ($elementList['abRatio'] * 100));
        }

        if (isset($elementList['trackingJs'])) {
            Mage::getModel('core/config')->saveConfig('affinityitems/advanced/ae_tracking_js', (int) $elementList['trackingJs']);
        }

        if ((count($sync_model->getCategoriesForSync()) || count($sync_model->getDeletedCategoriesForSync())) && ($last_sync == 'actions' || $initialsync)) {
            if ($this->isShell)
                echo " - Syncing categories..." . "\n";
            $this->logger->log("[INFO]", "Syncing categories... [" . time() . "]");
            $syncrotate_model->upd('categories', $website_id);
            Mage::getModel('affinityitems/sync_categorySync')->syncCategories($sync_count, true); //new
            Mage::getModel('affinityitems/sync_categorySync')->syncCategories($sync_count, false); //old
            Mage::getModel('affinityitems/sync_categorySync')->syncDeletedCategories($sync_count);
        } elseif ((count($sync_model->getProductsForSync()) || count($sync_model->getDeletedProductsForSync())) && ($last_sync == 'categories' || $initialsync)) {
            if ($this->isShell)
                echo " - Syncing products..." . "\n";
            $this->logger->log("[INFO]", "Syncing products... [" . time() . "]");
            $syncrotate_model->upd('products', $website_id);
            Mage::getModel('affinityitems/sync_productSync')->syncProducts($sync_count, true); //new
            Mage::getModel('affinityitems/sync_productSync')->syncProducts($sync_count, false); //old
            Mage::getModel('affinityitems/sync_productSync')->syncDeletedProducts($sync_count);
        } elseif ((count($sync_model->getMembersForSync())) && ($last_sync == 'products' || $initialsync)) {
            if ($this->isShell)
                echo " - Syncing members..." . "\n";
            $this->logger->log("[INFO]", "Syncing members... [" . time() . "]");            
            $syncrotate_model->upd('members', $website_id);
            Mage::getModel('affinityitems/sync_memberSync')->syncMember($sync_count);
        } elseif (count($sync_model->getOrdersForSync()) && ($last_sync == 'members' || $initialsync)) {
            if ($this->isShell)
                echo " - Syncing orders..." . "\n";
            $this->logger->log("[INFO]", "Syncing orders... [" . time() . "]");
            $syncrotate_model->upd('orders', $website_id);
            Mage::getModel('affinityitems/sync_orderSync')->syncOrders($sync_count);
        } elseif (count($sync_model->getCartForSync()) && ($last_sync == 'orders' || $initialsync)) {
            if ($this->isShell)
                echo " - Syncing cart actions..." . "\n";
            $this->logger->log("[INFO]", "Syncing cart actions... [" . time() . "]");
            $syncrotate_model->upd('cart', $website_id);
            Mage::getModel('affinityitems/sync_CartActionSync')->syncCartActions($sync_count);
        } elseif (count($sync_model->getActionsForSync()) && ($last_sync == 'cart' || $initialsync)) {
            if ($this->isShell)
                echo " - Syncing actions..." . "\n";
            $this->logger->log("[INFO]", "Syncing actions... [" . time() . "]");            
            $syncrotate_model->upd('actions', $website_id);
            Mage::getModel('affinityitems/sync_ActionSync')->syncActions($sync_count);
        } else {
            if ($initialsync) {
                $syncrotate_model->updInitial(0, $website_id);
                if ($this->isShell)
                    echo " - Initial sync finished..." . "\n";
                $this->logger->log("[INFO]", "Initial sync finished...! [" . time() . "]");
            }
            if ($this->isShell)
                echo " - Tried to sync " . $last_sync . "..." . "\n";
            switch ($last_sync) {
                case 'actions':
                    $syncrotate_model->upd('categories', $website_id);
                    break;
                case 'categories':
                    $syncrotate_model->upd('products', $website_id);
                    break;
                case 'products':
                    $syncrotate_model->upd('members', $website_id);
                    break;
                case 'members':
                    $syncrotate_model->upd('orders', $website_id);
                    break;
                case 'orders':
                    $syncrotate_model->upd('cart', $website_id);
                    break;
                case 'cart':
                    $syncrotate_model->upd('actions', $website_id);
                    break;
            }
        }
        if ($this->isShell)
            echo "Removing locks" . "\n";
        $this->logger->log("[INFO]", "Removing locks... [" . time() . "]");            
        $this->indexProcess->unlock();
        $this->logger->log("[INFO]", "AE Cron stop [" . time() . "]");
    }

}
