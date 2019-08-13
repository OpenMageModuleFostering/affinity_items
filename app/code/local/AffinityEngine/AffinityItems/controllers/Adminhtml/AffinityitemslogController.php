<?php

class AffinityEngine_AffinityItems_Adminhtml_AffinityitemslogController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->_title($this->__("AffinityItems"));
        $this->_setActiveMenu('affinityengine/affinityitems_log');
        $this->renderLayout();
    }

    public function deleteallAction() {
        $resource = Mage::getSingleton('core/resource')->getConnection('core_write');
        $resource->query('TRUNCATE TABLE ' . Mage::getSingleton('core/resource')->getTableName('ae_log'));
        Mage::getSingleton('adminhtml/session')->addSuccess('Log cleared');
        $this->_redirect('*/*/index');
    }

    public function clearLogAction() {
        $logfile = Mage::getBaseDir('log') . '/AffinityDebug.log';
        if (file_exists($logfile)) {
            unlink($logfile);
            Mage::getSingleton('adminhtml/session')->addSuccess('Log file cleared');
        } else {
            Mage::getSingleton('adminhtml/session')->addSuccess('Log file was already empty');
        }

        $this->_redirect('*/*/index');
    }

    public function downloadLogAction() {
        $logfile = Mage::getBaseDir('log') . '/AffinityDebug.log';

        if (!is_file($logfile) || !is_readable($logfile)) {
            Mage::getSingleton('adminhtml/session')->addSuccess('Log file doesnt exist');
            $this->_redirect('*/*/index');
        } else {
            $this->getResponse()
                    ->setHttpResponseCode(200)
                    ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                    ->setHeader('Pragma', 'public', true)
                    ->setHeader('Content-type', 'application/force-download')
                    ->setHeader('Content-Length', filesize($logfile))
                    ->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($logfile));
            $this->getResponse()->clearBody();
            $this->getResponse()->sendHeaders();
            readfile($logfile);
        }
    }

}
