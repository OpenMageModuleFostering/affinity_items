<?php

class AffinityEngine_AffinityItems_Model_Source_Imagesize
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('affinityitems')->__('Default')),
            array('value'=>1, 'label'=>Mage::helper('affinityitems')->__('Large')),
            array('value'=>2, 'label'=>Mage::helper('affinityitems')->__('Medium')),            
            array('value'=>3, 'label'=>Mage::helper('affinityitems')->__('Small')),                       
        );
    }

}