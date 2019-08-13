<?php

class AffinityEngine_AffinityItems_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action {

    public function registerAction() {
        if (Mage::app()->getRequest()->getParam('email') != '' && Mage::app()->getRequest()->getParam('password') != '' /*&& Mage::app()->getRequest()->getParam('website_id') != ''*/) {
            $website_id = Mage::app()->getRequest()->getParam('website_id');
            
            $customer = new stdClass();
            $customer->siteName = $_SERVER['SERVER_NAME'];
            $customer->domain = $_SERVER['HTTP_HOST'];
            $customer->email = Mage::app()->getRequest()->getParam('email');
            $customer->password = Mage::app()->getRequest()->getParam('password');
            $customer->origin = 'magentoextension';
            $customer->platform = 'Magento';
            $customer->platformVersion = Mage::getVersion();
            $customer->ip = $_SERVER['SERVER_ADDR'];

            $language = explode('_', Mage::app()->getLocale()->getLocaleCode());
            $customer->language = $language[0];
            $customer->refCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();

            if(!is_null(Mage::app()->getRequest()->getParam('discountCode')))
                $customer->code = Mage::app()->getRequest()->getParam('discountCode');
            /*
            $customer->firstname = Mage::app()->getRequest()->getParam('firstname');
            $customer->lastname = Mage::app()->getRequest()->getParam('lastname');
            $customer->activity = Mage::app()->getRequest()->getParam('activity');*/            

           // if (Mage::app()->getRequest()->getParam('password') == Mage::app()->getRequest()->getParam('confirmPassword')) {
            
            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_CustomerRequest($customer);
            $response = $request->registerCustomer();
            
            /*} else {
                $response = false;
            }*/

            if ($response) {
                if ($response['_ok'] == 'true') {
                    Mage::helper('affinityitems/aeadapter')->authentication($response['email'], 'password', $response['siteId'], $response['securityKey'], $website_id);
                    //addFirstHost();
                } else {
                    $ret['_errorMessage'] = $response['_errorMessage'];
                }
                $ret['_ok'] = $response['_ok'];
            } else {
                $ret['_ok'] = "false";
            }
        } else {
            $ret['_ok'] = "false";
        }
        $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($ret));
    }

    /*
    public function loginAction() {
        if (Mage::app()->getRequest()->getParam('email') != '' && Mage::app()->getRequest()->getParam('password') != '') {
            $customer = new stdClass();
            $customer->siteName = Mage::app()->getStore()->getName();
            $customer->email = Mage::app()->getRequest()->getParam('email');
            $customer->password = Mage::app()->getRequest()->getParam('password');
            $customer->domain = $_SERVER['HTTP_HOST'];
            $customer->origin = 'magentoextension';
            $customer->platform = 'Magento';
            $customer->platformVersion = Mage::getVersion();
            $customer->ip = $_SERVER['SERVER_ADDR'];

            //$customer->activity = AEAdapter::getActivity();
            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_CustomerRequest($customer);
            $response = $request->loginCustomer();
            if ($response) {
                if ($response['_ok'] == 'true') {
                    Mage::helper('affinityitems/aeadapter')->authentication($response['email'], $response['password'], $response['siteId'], $response['securityKey']);
                    //addFirstHost();
                } else {
                    $ret['_errorMessage'] = $response['_errorMessage'];
                }
                $ret['_ok'] = $response['_ok'];
            } else {
                $ret['_ok'] = "false";
            }
        } else {
            $ret['_ok'] = "false";
        }
        $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($ret));
    }
    */

}
