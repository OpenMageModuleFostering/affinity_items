<?php

class AffinityEngine_AffinityItems_Model_Observer {

    public function __construct() {
        $this->logger = Mage::getModel('affinityitems/log');
        $this->cookie = Mage::getModel('core/cookie');
        $this->helper = Mage::helper('affinityitems');
        $this->aehelper = Mage::helper('affinityitems/aeadapter');
        $this->aegroups = array("A", "B");
    }

    public function addToCart($observer) {
        $website_id = Mage::app()->getWebsite()->getId();
        $this->aehelper->registerWebsiteId($website_id);
        if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
            if ($this->cookie->get('aeguest') && in_array($this->cookie->get('aegroup'), $this->aegroups)) {
                $cart_repo_model = Mage::getModel("affinityitems/cart");
                $options = $observer->getProduct()->getTypeInstance(true)->getOrderOptions($observer->getProduct());
                $attribute_ids = array();

                if (isset($options['info_buyRequest']['super_attribute']) && $ai = $options['info_buyRequest']['super_attribute']) {
                    foreach ($ai as $key => $val) {
                        array_push($attribute_ids, $key);
                    }
                }

                $action = new Varien_Object();
                $action->setIdCart(Mage::helper('checkout')->getQuote()->getId());
                $action->setAction('addToCart');
                $action->setQuantity($observer->getData('product')->getData('cart_qty'));
                $action->setAegroup($this->cookie->get('aegroup'));
                $action->setAeguestid($this->cookie->get('aeguest'));
                $action->setAememberid($this->helper->getMemberId());
                $action->setDateAdd(date("Y-m-d H:i:s"));
                $action->setIdProduct($observer->getData('product')->getData('entity_id'));
                $action->setIdProductAttribute(serialize($attribute_ids));
                $action->setIp($this->helper->getIp());
                $action->setLanguage($this->helper->getLang());

                if (!Mage::getModel('affinityitems/sync_cartActionSync')->syncCartFromObserver($action)) {
                    $cart_repo_model->cartrepo(
                            $action->getIdCart(), $action->getIdProduct(), $action->getQuantity(), $action->getAction(), $attribute_ids, $action->getAeguestid(), $action->getAegroup(), $action->getIp(), $action->getAememberid(), $action->getLanguage(), $this->aehelper->getWebsiteId());
                }


                // save to session current cart status
                $quote = Mage::helper('checkout')->getQuote();
                $i = 1;
                foreach ($quote->getItemsCollection() as $item) {
                    $cart_before_update[$i]['product_id'] = $item->getProductId();
                    $cart_before_update[$i]['qty'] = $item->getQty();
                    $i++;
                }
                Mage::getSingleton('customer/session')->setData('cart_before_update', $cart_before_update);
            }
        }
    }

    public function updateCart($observer) {
        $website_id = Mage::app()->getWebsite()->getId();
        $this->aehelper->registerWebsiteId($website_id);
        if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
            if ($aeguestId = $this->cookie->get('aeguest') && in_array($this->cookie->get('aegroup'), $this->aegroups)) {
                $cart = Mage::getModel('checkout/cart')->getQuote();
                $cart_repo_model = Mage::getModel("affinityitems/cart");

                foreach (Mage::getSingleton('customer/session')->getData('cart_before_update') as $prod) {
                    foreach ($cart->getAllItems() as $item) {
                        if ($item->getProductId() == $prod['product_id']) {
                            if ($item->getQty() != $prod['qty']) {

                                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                                $attribute_ids = array();

                                if (isset($options['info_buyRequest']['super_attribute']) && $ai = $options['info_buyRequest']['super_attribute']) {
                                    foreach ($ai as $key => $val) {
                                        array_push($attribute_ids, $key);
                                    }
                                }

                                $action = new Varien_Object();
                                $action->setIdCart(Mage::helper('checkout')->getQuote()->getId());
                                $action->setAction('updateCart');
                                $action->setQuantity($item->getQty());
                                $action->setAegroup($this->cookie->get('aegroup'));
                                $action->setAeguestid($this->cookie->get('aeguest'));
                                $action->setAememberid($this->helper->getMemberId());
                                $action->setDateAdd(date("Y-m-d H:i:s"));
                                $action->setIdProduct($prod['product_id']);
                                $action->setIdProductAttribute(serialize($attribute_ids));
                                $action->setIp($this->helper->getIp());
                                $action->setLanguage($this->helper->getLang());

                                if (!Mage::getModel('affinityitems/sync_cartActionSync')->syncCartFromObserver($action)) {
                                    $cart_repo_model->cartrepo(
                                            $action->getIdCart(), $action->getIdProduct(), $action->getQuantity(), $action->getAction(), $attribute_ids, $action->getAeguestid(), $action->getAegroup(), $action->getIp(), $action->getAememberid(), $action->getLanguage(), $this->aehelper->getWebsiteId());
                                }
                            }
                        }
                    }
                }

                // save new session current cart status
                $quote = Mage::helper('checkout')->getQuote();
                $i = 1;
                foreach ($quote->getItemsCollection() as $item) {
                    $cart_before_update[$i]['product_id'] = $item->getProductId();
                    $cart_before_update[$i]['qty'] = $item->getQty();
                    $i++;
                }
                Mage::getSingleton('customer/session')->setData('cart_before_update', $cart_before_update);
            }
        }
    }

    public function updateProductCart($observer) {
        $website_id = Mage::app()->getWebsite()->getId();
        $this->aehelper->registerWebsiteId($website_id);
        if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
            if ($aeguestId = $this->cookie->get('aeguest') && in_array($this->cookie->get('aegroup'), $this->aegroups)) {
                $cart_repo_model = Mage::getModel("affinityitems/cart");

                foreach (Mage::getSingleton('customer/session')->getData('cart_before_update') as $prod) {
                    if ($observer->getData('quote_item')->getData('product_id') == $prod['product_id']) {
                        if ($observer->getData('quote_item')->getData('qty') != $prod['qty']) {

                            $options = $observer->getQuoteItem()->getProduct()->getTypeInstance(true)->getOrderOptions($observer->getQuoteItem()->getProduct());
                            $attribute_ids = array();

                            if (isset($options['info_buyRequest']['super_attribute']) && $ai = $options['info_buyRequest']['super_attribute']) {
                                foreach ($ai as $key => $val) {
                                    array_push($attribute_ids, $key);
                                }
                            }

                            $action = new Varien_Object();
                            $action->setIdCart(Mage::helper('checkout')->getQuote()->getId());
                            $action->setAction('updateCart');
                            $action->setQuantity($observer->getData('quote_item')->getData('qty'));
                            $action->setAegroup($this->cookie->get('aegroup'));
                            $action->setAeguestid($this->cookie->get('aeguest'));
                            $action->setAememberid($this->helper->getMemberId());
                            $action->setDateAdd(date("Y-m-d H:i:s"));
                            $action->setIdProduct($prod['product_id']);
                            $action->setIdProductAttribute(serialize($attribute_ids));
                            $action->setIp($this->helper->getIp());
                            $action->setLanguage($this->helper->getLang());

                            if (!Mage::getModel('affinityitems/sync_cartActionSync')->syncCartFromObserver($action)) {
                                $cart_repo_model->cartrepo(
                                        $action->getIdCart(), $action->getIdProduct(), $action->getQuantity(), $action->getAction(), $attribute_ids, $action->getAeguestid(), $action->getAegroup(), $action->getIp(), $action->getAememberid(), $action->getLanguage(), $this->aehelper->getWebsiteId());
                            }
                            break;
                        }
                    }
                }

                // save new session current cart status
                $quote = Mage::helper('checkout')->getQuote();
                $i = 1;
                foreach ($quote->getItemsCollection() as $item) {
                    $cart_before_update[$i]['product_id'] = $item->getProductId();
                    $cart_before_update[$i]['qty'] = $item->getQty();
                    $i++;
                }
                Mage::getSingleton('customer/session')->setData('cart_before_update', $cart_before_update);
            }
        }
    }

    public function removeFromCart($observer) {
        $website_id = Mage::app()->getWebsite()->getId();
        $this->aehelper->registerWebsiteId($website_id);
        if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
            if ($aeguestId = $this->cookie->get('aeguest') && in_array($this->cookie->get('aegroup'), $this->aegroups)) {
                $options = $observer->getQuoteItem()->getProduct()->getTypeInstance(true)->getOrderOptions($observer->getQuoteItem()->getProduct());
                $attribute_ids = array();

                if (isset($options['info_buyRequest']['super_attribute']) && $ai = $options['info_buyRequest']['super_attribute']) {
                    foreach ($ai as $key => $val) {
                        array_push($attribute_ids, $key);
                    }
                }
                foreach ($session_cart = Mage::getSingleton('customer/session')->getData('cart_before_update') as $prod) {
                    if ($observer->getData('quote_item')->getData('product_id') == $prod['product_id']) {
                        $qty = $prod['qty'];
                        break;
                    }
                }

                $qty = (isset($qty)) ? $qty : 1;
                $cart_repo_model = Mage::getModel("affinityitems/cart");

                $action = new Varien_Object();
                $action->setIdCart(Mage::helper('checkout')->getQuote()->getId());
                $action->setAction('removeFromCart');
                $action->setQuantity($qty);
                $action->setAegroup($this->cookie->get('aegroup'));
                $action->setAeguestid($this->cookie->get('aeguest'));
                $action->setAememberid($this->helper->getMemberId());
                $action->setDateAdd(date("Y-m-d H:i:s"));
                $action->setIdProduct($observer->getData('quote_item')->getData('product_id'));
                $action->setIdProductAttribute(serialize($attribute_ids));
                $action->setIp($this->helper->getIp());
                $action->setLanguage($this->helper->getLang());

                if (!Mage::getModel('affinityitems/sync_cartActionSync')->syncCartFromObserver($action)) {
                    $cart_repo_model->cartrepo(
                            $action->getIdCart(), $action->getIdProduct(), $action->getQuantity(), $action->getAction(), $attribute_ids, $action->getAeguestid(), $action->getAegroup(), $action->getIp(), $action->getAememberid(), $action->getLanguage(), $this->aehelper->getWebsiteId());
                }
            }
        }
    }

    public function saveProduct($observer) {
        foreach ($observer->getData('data_object')->getData('website_ids') as $website_id) {
            $this->aehelper->registerWebsiteId($website_id);

            if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
                if (!$observer->getData('data_object')->getData('observer')) {
                    if (!Mage::getModel('affinityitems/sync_productSync')->syncProductFromObserver($observer->getData('data_object')->getData('entity_id')))
                        $observer->getData('data_object')->setData('ae_sync', 0);
                }
            }
        }
    }

    public function saveCategory($observer) {
        // trying to find website id by Root Id of category
        $rootId = explode('/', $observer->getData('data_object')->getData('path'));
        if (isset($rootId[1])) {
            $website_id = $this->aehelper->getWebsiteIdByRootCategoryId($rootId[1]);
        } else {
            return;
        }
        $this->aehelper->registerWebsiteId($website_id);
        if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
            if (!$observer->getData('data_object')->getData('observer')) {
                if (!Mage::getModel('affinityitems/sync_categorySync')->syncCategoryFromObserver($observer->getData('data_object')->getData('entity_id')))
                    $observer->getData('data_object')->setData('ae_sync', 0);
            }
        }
    }
    
    public function saveOrder($observer) {
        $website_id = Mage::app()->getWebsite()->getId();
        if ($website_id == 0) return;
        $this->aehelper->registerWebsiteId($website_id);
        if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
            if (!$observer->getData('data_object')->getData('observer')) {
                if (!Mage::getModel('affinityitems/sync_orderSync')->syncOrderFromObserver($observer->getData('data_object')->getData('entity_id'))) {
                    $observer->getData('data_object')->setData('ae_sync', 0);
                    if (!$observer->getData('data_object')->getData('ae_group')) {
                        $aeg = (Mage::getModel('core/cookie')->get('aegroup')) ? Mage::getModel('core/cookie')->get('aegroup') : NULL;
                        $observer->getData('data_object')->setData('ae_group', $aeg);
                    }
                }
            }
        }
    }

    public function checkCookie($observer) {
        $website_id = Mage::app()->getWebsite()->getId();
        $this->aehelper->registerWebsiteId($website_id);
        if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
            if (!$this->helper->isAdmin()) {
                if (!$this->cookie->get('aeguest') || $force = Mage::app()->getRequest()->getParam('aeabtesting')) {
                    $this->helper->generateGuest();
                    $ab = new AffinityEngine_AffinityItems_Model_Sdk_Abtesting_Abtesting();
                    if (isset($force)) {
                        $ab->forceGroup($force);
                    }
                    $ab->init();
                }
            }
        }
    }

    public function login($params) {
        $website_id = Mage::app()->getWebsite()->getId();
        $this->aehelper->registerWebsiteId($website_id);
        if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
            if (!$this->helper->isAdmin()) {
                if ($this->cookie->get('aeguest') && $customer = Mage::getSingleton('customer/session')->isLoggedIn()) {
                    try {
                        $mid = Mage::getSingleton('customer/session')->getCustomer()->getId();
                        $gid = $this->cookie->get('aeguest');
                        $data = new stdClass();
                        $data->guestId = (String) $gid;
                        $data->memberId = (String) $mid;
                        $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_LinkGuestToMemberRequest($data);
                        if ($request->post()) {
                            $this->logger->log("[INFO]", "Link guest to member : " . $gid . " - " . $mid);
                        }
                    } catch (Exception $e) {
                        $this->logger->log('[ERROR]', $e->getMessage());
                    }
                }
            }
        }
    }

    public function updateHosts(Varien_Event_Observer $observer) {
        $website_id = Mage::app()->getWebsite()->getId();
        $this->aehelper->registerWebsiteId($website_id);
        if ($this->helper->isModuleEnabledAndRegistered($website_id)) {
            if ($this->detectEnviromentChange())
                return false;
            $ips = unserialize(Mage::getStoreConfig('affinityitems/advanced/server_ip'));
            if (isset($ips['server_ip'])) {
                $data = $ips['server_ip'];
                if (count($data) > 1) {
                    $data[0] = "127.0.0.1";
                    $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_HostRequest($data);
                    if ($request->post()) {
                        $this->logger->log("[INFO]", "Updated hosts: " . implode(', ', $data));
                    }
                }
            }
        }
    }

    public function detectEnviromentChange() {
        if (Mage::getStoreConfig('affinityitems/general/dev_prod') <> Mage::getStoreConfig('affinityitems/general/dev_prod_rel')) {
            $ae_config = new Mage_Core_Model_Config();
            $ae_config->saveConfig('affinityitems/general/dev_prod_rel', Mage::getStoreConfig('affinityitems/general/dev_prod'), 'default', 0);
            // No need to remove user / pass, from v1.1.5 it is saved per website
            // Mage::helper('affinityitems/aeadapter')->authentication('', '', '', '');
            $this->helper->deleteAllSync();
            $this->logger->log('[INFO]', 'Enviroment changed, login info removed, need to login again to AffinityEngine server');
            Mage::getSingleton('adminhtml/session')->addSuccess('Enviroment changed, login info removed, need to login again to AffinityEngine server');
            return true;
        }
        return false;
    }

    public function deleteCategory($observer) {
        $categoryId = $observer->getData('category')->getData('entity_id');
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $categoryIds = $category->getPathIds();

        $stores = Mage::getModel('core/store')->getCollection()->loadByCategoryIds($categoryIds);

        $websiteIds = array_unique($stores->getColumnValues('website_id'));

        foreach ($websiteIds as $websiteId) {
            $this->aehelper->registerWebsiteId($websiteId);

            if ($this->helper->isModuleEnabledAndRegistered($websiteId)) {
                $cid = $observer->getData('category')->getData('entity_id');
                $category = new stdClass();
                $category->categoryId = $cid;

                $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_CategoryRequest($category);
                $response = $request->delete();
                if ($response) {
                    $this->logger->log('[INFO]', 'Delete category:  (ID:' . $cid . ') [' . time() . ']');
                } else {
                    $this->logger->log('[ERROR]', 'Delete category:  (ID:' . $cid . ') [' . time() . ']');
                    Mage::getModel("affinityitems/catProdRepo")->addToCatProdRepo($cid, 'category', $this->aehelper->getWebsiteId());
                }
            }
        }
    }

    public function deleteProduct($observer) {
        foreach ($observer->getData('data_object')->getWebsiteIds() as $websiteId) {
            $this->aehelper->registerWebsiteId($websiteId);

            if ($this->helper->isModuleEnabledAndRegistered($websiteId)) {
                $pid = $observer->getData('data_object')->getData('entity_id');
                if ($observer->getData('data_object')->getData('type_id') == 'simple' && $observer->getData('data_object')->getData('visibility') == 1)
                    return;
                $aeproduct = new stdClass();
                $aeproduct->productId = $pid;
                $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_ProductRequest($aeproduct);
                $response = $request->delete();

                if ($response) {
                    $this->logger->log('[INFO]', 'Delete product:  (ID:' . $pid . ') [' . time() . ']');
                } else {
                    $this->logger->log('[ERROR]', 'Delete product:  (ID:' . $pid . ') [' . time() . ']');
                    Mage::getModel("affinityitems/catProdRepo")->addToCatProdRepo($pid, 'product', $this->aehelper->getWebsiteId());
                }
            }
        }
    }

}
