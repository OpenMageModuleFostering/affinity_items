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
class AffinityEngine_AffinityItems_Model_Sdk_Core_Curl {

    protected $returnErrors;

    public function __construct($preturnErrors) {
        $this->returnErrors = $preturnErrors;
    }

    public function head($url, $vars = array()) {
        return $this->request('HEAD', $url, $vars);
    }

    public function post($url, $vars = array()) {
        return $this->request('POST', $url, $vars);
    }

    public function put($url, $vars = array()) {
        return $this->request('PUT', $url, $vars);
    }

    public function delete($url, $vars = array()) {
        return $this->request('DELETE', $url, $vars);
    }

    public function get($url, $vars = array()) {
        if (!empty($vars)) {
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= (is_string($vars)) ? $vars : http_build_query($vars, '', '&');
        }
        return $this->request('GET', $url);
    }

    public function getHeaders() {
        $headers = array(
            "Content-type: application/json; charset=utf-8");
        $security = "securityKey: " . Mage::helper('affinityitems/aeadapter')->getSecurityKey();
        array_push($headers, $security);
        return $headers;
    }

    public function request($method, $url, $vars = array()) {
        $helper = Mage::helper('affinityitems/aeadapter');
        if (!($url == 'login' || $url == 'register') && !$helper->getSiteId())
            return false;

        $curl = curl_init();
        $site_id = ($url == 'login' || $url == 'register') ? '' : $helper->getSiteId();
        curl_setopt($curl, CURLOPT_URL, $helper->getHost() . ':' . $helper->getPort() . '/site/' . $site_id . $url);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 1000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if (Mage::registry('curl_debug')) {
            curl_setopt($curl, CURLOPT_VERBOSE, true); // for shell debugging only
        }

        if (!empty($vars)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, Mage::helper('core')->jsonEncode($vars));
        }

        $return = curl_exec($curl);
        if (Mage::registry('curl_debug')) {
            var_dump($vars);
            var_dump($return); // for shell debugging only
        }

        curl_close($curl);

        try {
            if ($ret = Mage::helper('core')->jsonDecode($return)) {
                if ($ret['_ok'] == "true") {
                    return $ret;
                } else {
                    Mage::getModel('affinityitems/log')->log("[ERROR]", $ret->_errorCode . " : " . $ret->_errorMessage);
                    if ($this->returnErrors) {
                        return $ret;
                    }
                }
            }
        } catch (Exception $e) {
            Mage::getModel('affinityitems/log')->log("[ERROR]", $e->getMessage());
        }
        return false;
    }

}
