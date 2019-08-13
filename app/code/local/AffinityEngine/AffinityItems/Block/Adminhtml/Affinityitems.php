<?php

class AffinityEngine_AffinityItems_Block_Adminhtml_Affinityitems extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $logfile = Mage::getBaseDir('log') . '/AffinityDebug.log';
        $this->_controller = "adminhtml_affinityitems";
        $this->_blockGroup = "affinityitems";
        $this->_headerText = Mage::helper("affinityitems")->__("Affinityitems Log Manager");
        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('adminhtml')->__('Clear Log'),
            'onclick' => "setLocation('" . $this->getUrl('*/*/deleteall') . "')",
            'class' => 'save',
                ), -100);
        if (file_exists($logfile)) {
            $this->_addButton('download_log', array(
                'label' => Mage::helper('adminhtml')->__('Download AffinityDebug.log') . ' (' . $this->human_filesize() . ')',
                'onclick' => "setLocation('" . $this->getUrl('*/*/downloadLog') . "')",
                'class' => 'save',
                    ), -200);
            $this->_addButton('clear_log', array(
                'label' => Mage::helper('adminhtml')->__('Clear AffinityDebug.log') . ' (' . $this->human_filesize() . ')',
                'onclick' => "setLocation('" . $this->getUrl('*/*/clearLog') . "')",
                'class' => 'save',
                    ), -300);
        }
        parent::__construct();
        $this->_removeButton('add');
    }

    private function human_filesize($decimals = 2) {
        $logfile = Mage::getBaseDir('log') . '/AffinityDebug.log';
        if (file_exists($logfile)) {
            $bytes = filesize($logfile);
            $sz = 'BKMGTP';
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
        }
        return 'empty file';
    }

}
