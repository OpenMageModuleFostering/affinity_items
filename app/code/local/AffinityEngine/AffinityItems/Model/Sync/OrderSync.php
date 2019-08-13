<?php

class AffinityEngine_AffinityItems_Model_Sync_OrderSync extends AffinityEngine_AffinityItems_Model_Sync_Sync {

    public function syncOrders($sync_count = 300) {

        $countOrdersForSync = $this->getOrdersForSync()->count();
        $countPage = ceil($countOrdersForSync / $sync_count);
        for ($cPage = 0; $cPage <= ($countPage - 1); $cPage++) {
            $orders = $this->getOrdersForSync()->setPageSize($sync_count);
            $ordersList = array();

            foreach ($orders as $order) {
                $orderLines = $this->getOrderLines($order);
                $aeorder = new stdClass();
                $aeorder->orderId = $order->getEntityId();
                $aeorder->addDate = $order->getCreatedAt();
                $aeorder->currency = $order->getOrderCurrencyCode();                
                $aeorder->statusMessage = $order->getState();
                $aeorder->paymentMode = $order->getPayment()->getMethodInstance()->getTitle();
                $aeorder->updateDate = $order->getUpdatedAt();
                $aeorder->memberId = ($order->getCustomerId()) ? $order->getCustomerId() : 0;
                $aeorder->amount = $order->getBaseSubtotal();
                $aeorder->group = ($order->getAeGroup()) ? $order->getAeGroup() : NULL;
                $aeorder->ip = $order->getRemoteIp();
                $aeorder->orderLines = $orderLines;
                array_push($ordersList, $aeorder);
            }
            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_OrderRequest($ordersList);
            if ($request->post()) {
                foreach ($orders as $order) {
                    try {
                        $aeg = ($order->getAeGroup()) ? $order->getAeGroup() : NULL;
                        $order->setData('ae_sync', 1)->setData('observer', true)->setData('ae_group', $aeg)->save();
                        $this->logger->log('[INFO]', 'Synchronize order: ' . $order->getIncrimentId() . ' (ID:' . $order->getEntityId() . ') [' . time() . ']');
                    } catch (Exception $e) {
                        $this->logger->log('[ERROR]', $e->getMessage());
                        $this->logger->log('[ERROR]', 'Synchronize order: ' . $order->getIncrimentId() . ' (ID: ' . $order->getEntityId() . ') [' . time() . ']');
                    }
                }
            } else {
                $ids = array();
                foreach ($ordersList as $order) {
                    array_push($ids, $order->orderId);
                }
                $this->logger->log('[ERROR]', 'Synchronize of orders failed: (IDs: ' . implode(",", $ids) . ') [' . time() . ']');
            }
        }
    }

    public function syncOrderFromObserver($orderId = false) {
        if (!$orderId)
            return false;
        $order = Mage::getModel('sales/order')->load($orderId);
        $orderLines = $this->getOrderLines($order);
        $aeorder = new stdClass();
        $aeorder->orderId = $order->getEntityId();
        $aeorder->addDate = $order->getCreatedAt();
        $aeorder->currency = $order->getOrderCurrencyCode();                
        $aeorder->statusMessage = $order->getState();
        $aeorder->paymentMode = $order->getPayment()->getMethodInstance()->getTitle();
        $aeorder->updateDate = $order->getUpdatedAt();
        $aeorder->memberId = ($order->getCustomerId()) ? $order->getCustomerId() : 0;
        $aeorder->amount = $order->getBaseSubtotal();
        $aeorder->group = (Mage::getModel('core/cookie')->get('aegroup')) ?  Mage::getModel('core/cookie')->get('aegroup') : NULL;
        $aeorder->ip = $order->getRemoteIp();
        $aeorder->orderLines = $orderLines;
        
        $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_OrderRequest($aeorder);
        if ($request->post()) {
            try {
                $order->setData('ae_sync', 1)->setData('observer', true)->setData('ae_group', $aeorder->group)->save();
                $this->logger->log('[INFO]', 'Synchronize order: ' . $order->getIncrimentId() . ' (ID:' . $order->getEntityId() . ') [' . time() . ']');
                return true;
            } catch (Exception $e) {
                $this->logger->log('[ERROR]', $e->getMessage());
                $this->logger->log('[ERROR]', 'Synchronize order: ' . $order->getIncrimentId() . ' (ID: ' . $order->getEntityId() . ') [' . time() . ']');
            }
        } else {
            $this->logger->log('[ERROR]', 'Synchronize order: ' . $order->getIncrimentId() . ' (ID: ' . $order->getEntityId() . ') [' . time() . ']');
        }
        return false;
    }

    public function getOrderLines($order) {
        $orderLines = array();
        $ordered_items = $order->getAllItems();
        foreach ($ordered_items as $item) {
            if ($item->getParentItemId()) continue;
            $attributes = array();

            $options = $item->getProductOptions();

            if (isset($options['info_buyRequest']['super_attribute']) && $ai = $options['info_buyRequest']['super_attribute']) {
                foreach ($ai as $key => $val) {
                    array_push($attributes, $key);
                }
            }

            $orderLine = new stdClass();
            $orderLine->productId = $item->getProductId();
            $orderLine->attributeIds = $attributes;
            $orderLine->quantity = (int) $item->getQtyOrdered();
            array_push($orderLines, $orderLine);
            
            // sync qty/stock if needed
            $this->syncQtyCheck($item->getProductId());
            
        }

        return $orderLines;
    }
    
    public function syncQtyCheck($pid) {
        if ($pid) {
            $product = Mage::getModel('catalog/product')->load($pid);
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            if ($stock->getIsInStock() == 0 || $stock->getQty() == 0) {
                Mage::getModel('affinityitems/sync_productSync')->syncProductFromObserver($pid);
            }
        }
    }

}
