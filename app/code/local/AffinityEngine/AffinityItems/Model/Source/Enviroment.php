<?php

class AffinityEngine_AffinityItems_Model_Source_Enviroment
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('affinityitems')->__('Test')),
            array('value'=>1, 'label'=>Mage::helper('affinityitems')->__('Production'))                
        );
    }

}