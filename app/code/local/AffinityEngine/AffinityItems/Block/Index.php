<?php

class AffinityEngine_AffinityItems_Block_Index extends Mage_Core_Block_Template {

    public function getProductCollection($block = false) {
        // $context = "recoAll";

        $recoId = $this->helper('affinityitems')->getConfig('recommendation_config');

        $no_prod = $this->helper('affinityitems')->getConfig('number_products');
        if ($block && $block == "ae_vertical_right") {
            $area = "RIGHT";
            $no_prod = $this->helper('affinityitems')->getRightNOP('number_products');
        } elseif ($block && $block == "ae_vertical_left") {
            $area = "LEFT";
            $no_prod = $this->helper('affinityitems')->getLeftNOP('number_products');
        } elseif (!$block) {
            $path = $this->helper('affinityitems')->getXmlPath();
            if (strpos($path, 'home')) {
                $area = "HOME";
            } elseif (strpos($path, 'cart')) {
                $refs = $this->getCartOrderLines();
                $refType = "product";
                // $orderLines = $this->getCartOrderLines(); 
                // $context = "recoCart";
            } elseif (strpos($path, 'category')) {
                $refs = (Mage::registry('current_category')) ? Mage::registry('current_category')->getId() : false;
                $refType = "category";
                //$category_id = (Mage::registry('current_category')) ? Mage::registry('current_category')->getId() : false;
                // $context = "recoCategory";
            } elseif (strpos($path, 'search')) {
                $refType = "keywords";
                $expr = Mage::app()->getRequest()->getParam('q');
                // $context = "recoSearch";
            } elseif (strpos($path, 'product')) {
                $refs = (Mage::registry('current_product')) ? Mage::registry('current_product')->getId() : false;
                $refType = "product";
                //$product = (Mage::registry('current_product')) ? Mage::registry('current_product')->getId() : false;
                //$context = "recoSimilar";
            }
        } else {
            return;
        }

        $aecontext = new stdClass();
        //$aecontext->context = $context;

        $aecontext->recoId = $recoId;

        if (isset($area))
            $aecontext->area = $area;
        $aecontext->size = (int) $no_prod;
        /* if (isset($product))
          $aecontext->productId = (string) $product;
          if (isset($category_id))
          $aecontext->categoryId = (string) $category_id;
          if (isset($orderLines))
          $aecontext->orderLines = $orderLines; */
        if (isset($refs)) {
            if (!is_array($refs))
                $refs = array($refs);
            $aecontext->refs = $refs;
        }
        if (isset($refType))
            $aecontext->refType = $refType;
        if (isset($expr))
            $aecontext->keywords = (string) $expr;
        if (isset($refType) && $refType == 'category')
            $aecontext = $this->getFacetAttributes($aecontext);

        $this->helper('affinityitems')->log('[DEBUG]', $aecontext);
        $recomemendation = new AffinityEngine_AffinityItems_Model_Sdk_Recommendation_Recommendation($aecontext, false);
        $products = $recomemendation->getRecommendation();

        if ($products && isset($products['recommend'])) {
            return $this->loadProductsById($products['recommend']);
        }
        return;
    }

    public function getFacetAttributes($ae_object) {
        $filters = array();
        $appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
        foreach ($appliedFilters as $filter) {
            // price filter does not have attribute ID, it generated from price range of products
            if ($filter->getFilter()->getRequestVar() == 'price') continue;
            $filters[] = array($filter->getValue());
        }
        if (!empty($filters))
            $ae_object->facetAttributes = $filters;
        return $ae_object;
    }

    public function loadProductsById($ids) {
        //$ids = array(877,875);
        $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('entity_id', array('in' => $ids));
        $products->getSelect()->order("find_in_set(entity_id,'" . implode(',', $ids) . "')");
        return $products;
    }

    public function getCartOrderLines() {
        $order_lines = array();
        $quote = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        if ($quote) {
            foreach ($quote as $item) {
                $order_line = new stdClass();
                $order_line->productId = $item->getProductId();
                $order_line->attributeIds = $this->getCartsProductAttributes($item);
                $order_line->quantity = $item->getQty();
                array_push($order_lines, $order_line);
            }
        }
        return $order_lines;
    }

    public function getCartsProductAttributes($item) {
        $attribute_ids = array();
        $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

        if (isset($options['info_buyRequest']['super_attribute']) && $ai = $options['info_buyRequest']['super_attribute']) {
            foreach ($ai as $key => $val) {
                array_push($attribute_ids, $key);
            }
        }
        return $attribute_ids;
    }

}
