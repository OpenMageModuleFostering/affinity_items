<?php

class AffinityEngine_AffinityItems_Model_Sync_ProductSync extends AffinityEngine_AffinityItems_Model_Sync_Sync {

    public function syncProducts($sync_count = 300, $new = true) {
        $countProduct = $this->getProductsForSync()->count();
        $countPage = ceil($countProduct / $sync_count);
        for ($cPage = 0; $cPage <= ($countPage - 1); $cPage++) {
            $products = $this->getProductsForSync()->setPageSize($sync_count);

            if ($new) {
                $products->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync_date', 'null' => true),
                    array('attribute' => 'ae_sync_date', 'eq' => 0),
                        ), '', 'left');
            } else {
                $products->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync_date', 'null' => false),
                    array('attribute' => 'ae_sync_date', 'neq' => 0),
                        ), '', 'left');
            }

            $aeproductList = array();
            foreach ($products as $prod) {
                $prod = Mage::getModel('catalog/product')->setStoreId(0)->load($prod->getId());
                $aeproduct = new stdClass();
                $aeproduct->productId = $prod->getId();
                $aeproduct->updateDate = $prod->getUpdatedAt();
                $aeproduct->categoryIds = $prod->getCategoryIds();
                $aeproduct->recommendable = $this->isRecomendable($prod);
                $aeproduct->localizations = $this->getLocalizations($prod);
                $aeproduct->prices = $this->getProductPrices($prod);
                array_push($aeproductList, $aeproduct);
            }

            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_ProductRequest($aeproductList);
            if ($new && count($aeproductList)) {
                $response = $request->post();
            } elseif (!$new && count($aeproductList)) {
                $response = $request->put();
            } else {
                return;
            }

            if ($response) {
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); //fix for SQL error when saving product
                foreach ($aeproductList as $product) {
                    $prod = Mage::getModel('catalog/product')->setStoreId(0)->load($product->productId);
                    try {
                        $prod->setData('ae_sync', 1)->setData('observer', true)->setData('ae_sync_date', date("Y-m-d H:i:s"))->save();
                        $this->logger->log('[INFO]', 'Synchronize product: ' . $prod->getName() . ' (ID:' . $prod->getId() . ') [' . time() . ']');
                    } catch (Exception $e) {
                        $this->logger->log('[ERROR]', $e->getMessage());
                        $this->logger->log('[ERROR]', 'Synchronize product: ' . $prod->getName() . ' (ID: ' . $prod->getId() . ') [' . time() . ']');
                    }
                }
            } else {
                $ids = array();
                foreach ($aeproductList as $product) {
                    array_push($ids, $product->productId);
                }
                $this->logger->log('[ERROR]', 'Synchronize of products failed: (IDs: ' . implode(",", $ids) . ') [' . time() . ']');
            }
        }
    }

    public function syncDeletedProducts($sync_count = 300) {
        $countProduct = $this->getDeletedProductsForSync()->count();
        $countPage = ceil($countProduct / $sync_count);
        for ($cPage = 0; $cPage <= ($countPage - 1); $cPage++) {
            $products = $this->getDeletedProductsForSync()->setPageSize($sync_count);

            $aeproductList = array();
            foreach ($products as $prod) {
                $aeproduct = new stdClass();
                $aeproduct->productId = $prod->getObjId();
                array_push($aeproductList, $aeproduct);
            }

            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_ProductRequest($aeproductList);
            $response = $request->delete();


            if ($response) {
                foreach ($aeproductList as $product) {
                    try {
                        Mage::getModel('affinityitems/catProdRepo')->load($product->productId, 'obj_id')->delete();
                        $this->logger->log('[INFO]', 'Delete product:  (ID:' . $product->productId . ') [' . time() . ']');
                    } catch (Exception $e) {
                        $this->logger->log('[ERROR]', $e->getMessage());
                        $this->logger->log('[ERROR]', 'Delete product:  (ID: ' . $product->productId . ') [' . time() . ']');
                    }
                }
            } else {
                $ids = array();
                foreach ($aeproductList as $product) {
                    array_push($ids, $product->productId);
                }
                $this->logger->log('[ERROR]', 'Delete of products failed: (IDs: ' . implode(",", $ids) . ') [' . time() . ']');
            }
        }
    }

    public function syncProductFromObserver($prod = false) {
        if (!$prod)
            return false;
        $prod = Mage::getModel('catalog/product')->setStoreId(0)->load($prod);
        if ($prod->getVisibility() == 1 && $prod->getTypeId() == 'simple')
            return false;
        $is_new = (bool) $prod->getAeSyncDate();
        $aeproduct = new stdClass();
        $aeproduct->productId = $prod->getId();
        $aeproduct->updateDate = $prod->getUpdatedAt();
        $aeproduct->categoryIds = $prod->getCategoryIds();
        $aeproduct->recommendable = $this->isRecomendable($prod);
        $aeproduct->localizations = $this->getLocalizations($prod);
        $aeproduct->prices = $this->getProductPrices($prod);
        $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_ProductRequest($aeproduct);
        if (!$is_new) {
            $response = $request->post();
        } else {
            $response = $request->put();
        }
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); //fix for SQL error when saving product
        $prod = Mage::getModel('catalog/product')->setStoreId(0)->load($aeproduct->productId);
        if ($response) {
            try {
                $prod->setData('ae_sync', 1)->setData('observer', true)->setData('ae_sync_date', date("Y-m-d H:i:s"))->save();
                $this->logger->log('[INFO]', 'Synchronize product: ' . $prod->getName() . ' (ID:' . $prod->getId() . ') [' . time() . ']');
                return true;
            } catch (Exception $e) {
                $this->logger->log('[ERROR]', $e->getMessage());
                $this->logger->log('[ERROR]', 'Synchronize product: ' . $prod->getName() . ' (ID: ' . $prod->getId() . ') [' . time() . ']');
            }
        } else {
            $this->logger->log('[ERROR]', 'Synchronize product: ' . $prod->getName() . ' (ID: ' . $prod->getId() . ') [' . time() . ']');
            try {
                $prod->setData('ae_sync', 1)->setData('observer', true)->setData('ae_sync', 0)->save();
            } catch (Exception $e) {
                
            }
        }
        return false;
    }

    public function isRecomendable($prod) {
        $reco = false;
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($prod);
        // status must be enabled, visibility anything but NOT visible, product must be in stock, and must be assined to any category
        if ($prod->getStatus() == 1 && $prod->getVisibility() != 1 && $stock->getIsInStock() != 0 && count($prod->getCategoryIds()) != 0)
            $reco = true;
        return $reco;
    }

    public function getLocalizations($p) {

        $tagList = $this->getProductTags($p);
        $attributeList = $this->getProductAttributes($p);
        $featureList = $this->getProductAttributes($p);
        $localizationList = array();
        foreach ($this->getAllStores() as $store) {
            $p = Mage::getModel('catalog/product')->setStoreId($store->getStoreId())->load($p->getId());
            $plocalization = new stdClass();
            $locale = new Zend_Locale(strtolower(Mage::getStoreConfig('general/locale/code', $store->getStoreId())));
            $plocalization->language = $locale->getLanguage();
            $plocalization->name = strip_tags(preg_replace("(\r\n|\n|\r)", '', $p->getName()));
            $plocalization->shortDescription = strip_tags(preg_replace("(\r\n|\n|\r)", '', $p->getShortDescription()));
            $plocalization->description = strip_tags(preg_replace("(\r\n|\n|\r)", '', $p->getDescription()));
            $plocalization->tags = $tagList;
            $plocalization->attributes = $attributeList;
            $plocalization->features = array();

            array_push($localizationList, $plocalization);
        }
        return $localizationList;
    }

    public function getProductTags($p) {
        $listTag = array();
        $model = Mage::getModel('tag/tag');
        $tags = $model->getResourceCollection()
                ->addPopularity()
                ->addStatusFilter($model->getApprovedStatus())
                ->addProductFilter($p->getId())
                ->setFlag('relation', true)
                ->setActiveFilter()
                ->load();

        if (isset($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
                array_push($listTag, $tag->getName());
            }
        }

        return $listTag;
    }

    public static function getProductPrices($p) {
        $listPrice = array();
        $iso_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        if ($p->getPrice()) {
            $price = new stdClass();
            $price->currency = $iso_code;
            $price->amount = $p->getPrice();
            array_push($listPrice, $price);
        }
        if ($p->getFinalPrice() && $p->getPrice() <> $p->getFinalPrice()) {
            $price = new stdClass();
            $price->currency = $iso_code;
            $price->amount = $p->getFinalPrice();
            array_push($listPrice, $price);
        }
        if ($p->getSpecialPrice() && $p->getPrice() <> $p->getSpecialPrice()) {
            $price = new stdClass();
            $price->currency = $iso_code;
            $price->amount = $p->getSpecialPrice();
            array_push($listPrice, $price);
        }
        return $listPrice;
    }

    public static function getProductAttributes($p) {
        $listAttribute = array();
        $attributes = $p->getAttributes();

        // get all attributes of a product
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()) {

                $attributeCode = $attribute->getAttributeCode();
                $label = $attribute->getFrontend()->getLabel($p);
                $value = $attribute->getFrontend()->getValue($p);

                $group = new stdClass();
                $group->name = $label;
                $group->values = array(array("characteristicId" => $attribute->getId(), "name" => $value));

                array_push($listAttribute, $group);
            }
        }
        if ($p->isConfigurable()) {
            $productAttributeOptions = $p->getTypeInstance(true)->getConfigurableAttributesAsArray($p);
            $attributeOptions = array();
            foreach ($productAttributeOptions as $productAttribute) {
                $group = new stdClass();
                $group->name = $productAttribute['label'];
                $tmpAttribute = array();
                foreach ($productAttribute['values'] as $attribute) {
                    array_push($tmpAttribute, array("characteristicId" => $attribute['value_index'], "name" => $attribute['store_label']));
                }
                $group->values = $tmpAttribute;
                array_push($listAttribute, $group);
            }
        }
        return $listAttribute;
    }

    public static function getProductFeatures($p) {

        $listFeature = array();
        return $listFeature;
    }

}
