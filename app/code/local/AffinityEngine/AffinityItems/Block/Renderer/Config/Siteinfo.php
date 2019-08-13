<?php

class AffinityEngine_AffinityItems_Block_Renderer_Config_Siteinfo extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_SiteRequest(array());
        $data = $request->get();
        $html = '<div style="width:90%;">';
        if (strpos($element->getName(), 'monthly_recommendations') && $data['_ok']) {
            $html .= $data['recommendation'] . ' ' . $this->__('recos');
        } elseif (strpos($element->getName(), 'sales_impact') && $data['_ok']) {
            $html .= $this->__('Impact statistics under construction');
//            if (isset($data['statistics'])) {
//                $statistics = Mage::helper('core')->jsonDecode($data['statistics']);
//                if ($statistics['salesImpactByPercentage'] > 0) {
//                    $html .= $statistics['salesImpactByPercentage'] . '%';
//                }
//            }
        }
        $html .= '</div>';
        return $html;
    }

}
