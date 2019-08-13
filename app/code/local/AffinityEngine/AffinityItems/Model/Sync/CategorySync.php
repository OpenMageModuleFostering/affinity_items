<?php

class AffinityEngine_AffinityItems_Model_Sync_CategorySync extends AffinityEngine_AffinityItems_Model_Sync_Sync {

    public function syncCategories($sync_count = 300, $new = true) {
        $countCategory = $this->getCategoriesForSync()->count();
        $countPage = ceil($countCategory / $sync_count);
        for ($cPage = 0; $cPage <= ($countPage - 1); $cPage++) {
            $categories = $this->getCategoriesForSync()->setPageSize($sync_count);

            if ($new) {
                $categories->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync_date', 'null' => true),
                    array('attribute' => 'ae_sync_date', 'eq' => 0),
                        ), '', 'left');
            } else {
                $categories->addAttributeToFilter(
                        array(
                    array('attribute' => 'ae_sync_date', 'null' => false),
                    array('attribute' => 'ae_sync_date', 'neq' => 0),
                        ), '', 'left');
            }

            $categoryList = array();
            foreach ($categories as $cat) {
                $category = new stdClass();
                $category->categoryId = (int) $cat->getId();
                $category->parentId = (int) $cat->getParentId();
                $category->updateDate = $cat->getUpdatedAt();
                $category->localizations = $this->getFeatureList($cat);
                array_push($categoryList, $category);
            }
            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_CategoryRequest($categoryList);
            if ($new && count($categoryList)) {
                $response = $request->post();
            } elseif (!$new && count($categoryList)) {
                $response = $request->put();
            } else {
                return;
            }

            if ($response) {
                foreach ($categoryList as $category) {
                    $cat = Mage::getModel('catalog/category')->load($category->categoryId);
                    try {
                        $cat->setData('ae_sync', 1)->setData('observer', true)->setData('ae_sync_date', date("Y-m-d H:i:s"))->save();
                        $this->logger->log('[INFO]', 'Synchronize category: ' . $cat->getName() . ' (ID:' . $cat->getId() . ') [' . time() . ']');
                    } catch (Exception $e) {
                        $this->logger->log('[ERROR]', $e->getMessage());
                        $this->logger->log('[ERROR]', 'Synchronize category: ' . $cat->getName() . ' (ID:' . $cat->getId() . ') [' . time() . ']');
                    }
                }
            } else {
                $ids = array();
                foreach ($categoryList as $category) {
                    array_push($ids, $category->categoryId);
                }
                $this->logger->log('[ERROR]', 'Synchronize of categories failed: (IDs: ' . implode(",", $ids) . ') [' . time() . ']');
            }
        }
    }

    public function syncDeletedCategories($sync_count = 300) {
        $countCategory = $this->getDeletedCategoriesForSync()->count();
        $countPage = ceil($countCategory / $sync_count);
        for ($cPage = 0; $cPage <= ($countPage - 1); $cPage++) {
            $categories = $this->getDeletedCategoriesForSync()->setPageSize($sync_count);
            $categoryList = array();
            foreach ($categories as $cat) {
                $category = new stdClass();
                $category->categoryId = $cat->getObjId();
                array_push($categoryList, $category);
            }
            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_CategoryRequest($categoryList);
            $response = $request->delete();

            if ($response) {
                foreach ($categoryList as $category) {
                    try {
                        Mage::getModel('affinityitems/catProdRepo')->load($category->categoryId, 'obj_id')->delete();
                        $this->logger->log('[INFO]', 'Delete category:  (ID:' . $category->categoryId . ') [' . time() . ']');
                    } catch (Exception $e) {
                        $this->logger->log('[ERROR]', $e->getMessage());
                        $this->logger->log('[ERROR]', 'Delete category:  (ID:' . $category->categoryId . ') [' . time() . ']');
                    }
                }
            } else {
                $ids = array();
                foreach ($categoryList as $category) {
                    array_push($ids, $category->categoryId);
                }
                $this->logger->log('[ERROR]', 'Delete of categories failed: (IDs: ' . implode(",", $ids) . ') [' . time() . ']');
            }
        }
    }

    public function syncCategoryFromObserver($cat_id = false) {
        if (!$cat_id)
            return false;
        $cat = Mage::getModel('catalog/category')->setStoreId($this->getStoreIdByWebsiteId())->load($cat_id);
        $is_new = (bool) $cat->getAeSyncDate();
        $category = new stdClass();
        $category->categoryId = (int) $cat->getId();
        $category->parentId = (int) $cat->getParentId();
        $category->updateDate = $cat->getUpdatedAt();
        $category->localizations = $this->getFeatureList($cat);

        $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_CategoryRequest($category);
        if (!$is_new) {
            $response = $request->post();
        } else {
            $response = $request->put();
        }

        if ($response) {
            //$cat = Mage::getModel('catalog/category')->setStoreId($this->getStoreIdByWebsiteId())->load($category->categoryId);
            try {
                $cat->setData('ae_sync', 1)->setData('observer', true)->setData('ae_sync_date', date("Y-m-d H:i:s"))->save();
                $this->logger->log('[INFO]', 'Synchronize category: ' . $cat->getName() . ' (ID:' . $cat->getId() . ') [' . time() . ']');
                return true;
            } catch (Exception $e) {
                $this->logger->log('[ERROR]', $e->getMessage());
                $this->logger->log('[ERROR]', 'Synchronize category: ' . $cat->getName() . ' (ID:' . $cat->getId() . ') [' . time() . ']');
            }
        } else {
            $this->logger->log('[ERROR]', 'Synchronize category: ' . $cat->getName() . ' (ID:' . $cat->getId() . ') [' . time() . ']');
        }
        return false;
    }

    public function getFeatureList($cat) {
        $featureList = array();
        foreach (parent::getAllStores() as $store) {
            $cat = Mage::getModel('catalog/category')->setStoreId($store->getStoreId())->load($cat->getId());
            $locale = new Zend_Locale(strtolower(Mage::getStoreConfig('general/locale/code', $store->getStoreId())));
            array_push($featureList, array(
                "language" => strtolower($locale->getLanguage()),
                "name" => $cat->getName(),
                "description" => $cat->getDescription()
            ));
        }
        return $featureList;
    }

}
