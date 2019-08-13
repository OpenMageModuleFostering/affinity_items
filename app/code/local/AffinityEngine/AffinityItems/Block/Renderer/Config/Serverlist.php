<?php

class AffinityEngine_AffinityItems_Block_Renderer_Config_Serverlist extends Mage_Adminhtml_Block_System_Config_Form_Field
{
 
   protected $_addRowButtonHtml = array();
   protected $_removeRowButtonHtml = array();
 
   /**
    * Returns html part of the setting
    *
    * @param Varien_Data_Form_Element_Abstract $element
    * @return string
    */
   protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
   {
       $this->setElement($element);
       $html = '<div id="server_ip_header" style="width:480px;">';
       $html .= '<div style="width:209px;float: left;"><b>'. $this->__("Server IP's") . '</div>';
       $html .= '<div style="clear:both;"></div>';
       $html .= '</div>';
       
       $html .= '<div id="server_ip_template" style="display:none">';
       $html .= $this->_getRowTemplateHtml();
       $html .= '</div>';
 
       $html .= '<ul id="server_ip_container" style="width:480px;">';
       if ($this->_getValue('server_ip')) {
           foreach ($this->_getValue('server_ip') as $i => $f) {
               if ($i) {
                   $html .= $this->_getRowTemplateHtml($i);
               }
           }
       }
       $html .= '</ul>';
       $html .= $this->_getAddRowButtonHtml('server_ip_container',
           'server_ip_template', $this->__('Add New'));
 
       return $html;
   }

   /**
    * Retrieve html template for setting
    *
    * @param int $rowIndex
    * @return string
    */
   protected function _getRowTemplateHtml($rowIndex = 0)
   {
       $html = '<li>';
       $html .= '<div>';
       $html .= '<input name="'
           . $this->getElement()->getName() . '[server_ip][]" value="'
           . $this->_getValue('server_ip/' . $rowIndex) . '" ' . $this->_getDisabled() . '/> ';
       $html .= $this->_getRemoveRowButtonHtml();
       $html .= '</div>';
       $html .= '</li>';
 
       return $html;
   }
 
   protected function _getDisabled()
   {
       return $this->getElement()->getDisabled() ? ' disabled' : '';
   }
 
   protected function _getValue($key)
   {
       return $this->getElement()->getData('value/' . $key);
   }
 
   protected function _getSelected($key, $value)
   {
       return $this->getElement()->getData('value/' . $key) == $value ? 'selected="selected"' : '';
   }
 
   protected function _getAddRowButtonHtml($container, $template, $title='Add')
   {
       if (!isset($this->_addRowButtonHtml[$container])) {
           $this->_addRowButtonHtml[$container] = $this->getLayout()->createBlock('adminhtml/widget_button')
               ->setType('button')
               ->setClass('add ' . $this->_getDisabled())
               ->setLabel($this->__($title))
               ->setOnClick("Element.insert($('" . $container . "'), {bottom: $('" . $template . "').innerHTML})")
               ->setDisabled($this->_getDisabled())
               ->toHtml();
       }
       return $this->_addRowButtonHtml[$container];
   }
 
   protected function _getRemoveRowButtonHtml($selector = 'li', $title = 'Delete')
   {
       if (!$this->_removeRowButtonHtml) {
           $this->_removeRowButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
               ->setType('button')
               ->setClass('delete v-middle ' . $this->_getDisabled())
               ->setLabel($this->__($title))
               ->setOnClick("Element.remove($(this).up('" . $selector . "'))")
               ->setDisabled($this->_getDisabled())
               ->toHtml();
       }
       return $this->_removeRowButtonHtml;
   }

}