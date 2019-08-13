<?php

/**
 * 2014 Affinity-Engine
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade AffinityItems to newer
 * versions in the future. If you wish to customize AffinityItems for your
 * needs please refer to http://www.affinity-engine.fr for more information.
 *
 *  @author    Affinity-Engine SARL <contact@affinity-engine.fr>
 *  @copyright 2014 Affinity-Engine SARL
 *  @license   http://www.gnu.org/licenses/gpl-2.0.txt GNU GPL Version 2 (GPLv2)
 *  International Registered Trademark & Property of Affinity Engine SARL
 */
class AffinityEngine_AffinityItems_Model_Sdk_Recommendation_Recommendation {

    public $aecontext;
    public $pscontext;
    public $stack;
    public $render;
    public $langId;
    public $actionSynchronize;
    public $actionRepository;

    public function __construct($paecontext, $ppscontext) {
        $this->aecontext = $paecontext;
        $this->pscontext = $ppscontext;
        $this->logger = Mage::getModel('affinityitems/log');
        $this->cookie = Mage::getModel('core/cookie');
        $this->helper = Mage::helper('affinityitems');
    }

    public function getRecommendation() {
        $products = array();
        $productPool = array();
        $select = '';
        $tax = '';


        if(!$this->cookie->get('aeguest')) {
            return array();
        } else {
            $this->aecontext->guestId = $this->cookie->get('aeguest');
        }

        if ($group = $this->cookie->get('aegroup')) {
            $this->aecontext->group = $group;
        }

        if($this->helper->getMemberId() != '') {
            $this->aecontext->memberId = $this->helper->getMemberId();
        }

        $this->aecontext->ip = $this->helper->getIp();

        $this->aecontext->language = $this->helper->getLang();

        //sync action of guest id
        
        $this->aecontext->person = $this->aecontext->guestId;
        
        $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_RecommendationRequest($this->aecontext);
        $products = $request->post();
        
        return $products;
    }

}

?>