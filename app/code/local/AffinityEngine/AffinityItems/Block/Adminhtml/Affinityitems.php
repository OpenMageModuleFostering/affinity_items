<?php


class AffinityEngine_AffinityItems_Block_Adminhtml_Affinityitems extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

		$this->_controller = "adminhtml_affinityitems";
		$this->_blockGroup = "affinityitems";
		$this->_headerText = Mage::helper("affinityitems")->__("Affinityitems Log Manager");
		$this->_addButton('save_and_continue', array(
			'label' => Mage::helper('adminhtml')->__('Clear Log'),
			'onclick' => "setLocation('" . $this->getUrl('*/*/deleteall') . "')",
			'class' => 'save',
			), -100);
		parent::__construct();
		$this->_removeButton('add');
	}

}