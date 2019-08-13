<?php
class AffinityEngine_AffinityItems_Model_System_Config_Source_Dropdown_Values{

    public function toOptionArray()
    {
        $allStores = Mage::app()->getStores();
        $optionArray=array();
        $optionArray[]=array(
            'value' => 0,
            'label' => 'All'
        );
        foreach ($allStores as $_eachStoreId => $val)
        {
           // $_storeCode = Mage::app()->getStore($_eachStoreId)->getCode();
            $_storeName = Mage::app()->getStore($_eachStoreId)->getName();
            $_storeId = Mage::app()->getStore($_eachStoreId)->getId();
            $optionArray[]=array(
                'value' => $_storeId,
                'label' => $_storeName
            );
        }
        return $optionArray;
    }
}