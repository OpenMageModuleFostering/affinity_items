<?php

class AffinityEngine_AffinityItems_Model_Source_Loglevel
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('affinityitems')->__('Off')),
            array('value'=>1, 'label'=>Mage::helper('affinityitems')->__('[INFO]')),
            array('value'=>2, 'label'=>Mage::helper('affinityitems')->__('[ERROR]')),
            array('value'=>3, 'label'=>Mage::helper('affinityitems')->__('[INFO] & [ERROR]')),
        );
    }

}