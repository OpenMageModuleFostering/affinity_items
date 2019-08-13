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
class AffinityEngine_AffinityItems_Model_Sdk_Abtesting_Abtesting {

    public static $types = array('A', 'B', 'Z');

    public function __construct() {
        $this->logger = Mage::getModel('affinityitems/log');
        $this->cookie = Mage::getModel('core/cookie');
        $this->helper = Mage::helper('affinityitems');
    }

    public function init() {
        if (!$this->getGuestGroup()) {
            $this->setGuestGroup();
        }
    }

    public function getGuestGroup() {
        try {
            self::filter();
            return $this->cookie->get('aegroup');
        } catch (Exception $e) {
            $this->logger->log("[ERROR]", $e->getMessage());
        }
    }

    public function setGuestGroup() {
        try {
            $rnd = (0 + lcg_value() * (abs(1)));
            $group = ($rnd < ($this->helper->getGeneral('guest_percentage') / 100)) ? "A" : "B";
            $this->cookie->set('aegroup', $group, 630720000);
        } catch (Exception $e) {
            $this->logger->log("[ERROR]", $e->getMessage());
        }
    }

    public function filter() {
        /*try {
            $unsr = unserialize($this->helper->getAdvanced('ab_blacklist_ip'));
            if (in_array(Mage::helper('core/http')->getRemoteAddr(false), $unsr['ab_blacklist_ip'])) {
                $this->cookie->set('aegroup', 'Z', 630720000);
            }
        } catch (Exception $e) {
            $this->logger->log("[ERROR]", $e->getMessage());
        }*/
    }

    public function forceGroup($group) {
        $groups = array('A', 'B', 'Z');
        if (in_array($group, $groups)) {
            $this->logger->log("[INFO]", "Forcing group : " . $group);
            $this->cookie->set('aegroup', $group, 630720000);
        }
    }

}

?>