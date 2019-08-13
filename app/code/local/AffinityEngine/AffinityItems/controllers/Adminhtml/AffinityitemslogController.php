<?php 

class AffinityEngine_AffinityItems_Adminhtml_AffinityitemslogController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		$this->loadLayout();
		$this->_title($this->__("AffinityItems"));
		$this->_setActiveMenu('affinityengine/affinityitems');
		$this->renderLayout();
	}

	public function deleteallAction() {
		$resource = Mage::getSingleton('core/resource')->getConnection('core_write');
		$resource->query('TRUNCATE TABLE '.Mage::getSingleton('core/resource')->getTableName('ae_log'));
		Mage::getSingleton('adminhtml/session')->addSuccess('Log cleared');
		$this->_redirect('*/*/index');
	}
}