<?php

class AffinityEngine_AffinityItems_Block_Adminhtml_Affinityitems_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("affinityitemsGrid");
        $this->setDefaultSort("id_log");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel("affinityitems/log")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("id_log", array(
            "header" => Mage::helper("affinityitems")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "id_log",
            ));

        $this->addColumn('date_add', array(
            'header' => Mage::helper('affinityitems')->__('Date Added'),
            'index' => 'date_add',
            'type' => 'datetime',
            ));
        $this->addColumn("severity", array(
            "header" => Mage::helper("affinityitems")->__("Severity"),
            "index" => "severity",
            ));
        $this->addColumn("message", array(
            "header" => Mage::helper("affinityitems")->__("Message"),
            "index" => "message",
            ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        //return '#';
    }

}
