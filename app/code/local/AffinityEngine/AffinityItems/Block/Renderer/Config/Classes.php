<?php

class AffinityEngine_AffinityItems_Block_Renderer_Config_Classes extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setStyle('width:110px;')
            ->setName($element->getName() . '[]');

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = array();
        }

        $id = $element->setValue(isset($values[0]) ? $values[0] : null)->getElementHtml();
        $class = $element->setValue(isset($values[1]) ? $values[1] : null)->getElementHtml();
        $html = "id " . $id . "&nbsp;class " . $class;
        return $html;
      
    }
}