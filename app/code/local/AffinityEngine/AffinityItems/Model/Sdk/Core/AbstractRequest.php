<?php

class AffinityEngine_AffinityItems_Model_Sdk_Core_AbstractRequest extends Mage_Core_Model_Abstract {

    protected $path;
    protected $content;
    protected $curl;

    public function __construct($ppath, $pcontent) {
        $this->path = $ppath;
        $this->content = $pcontent;
        $this->curl = new AffinityEngine_AffinityItems_Model_Sdk_Core_Curl(false);
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($ppath) {
        $this->path = $ppath;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($pcontent) {
        $this->content = $pcontent;
    }

    public function getCurl() {
        return $this->curl;
    }

    public function setCurl($pcurl) {
        $this->curl = $pcurl;
    }

    public function post() {
        if ($response = $this->curl->post($this->path, $this->content)) {
            return $response;
        }
        return false;
    }

    public function put() {
        if ($response = $this->curl->put($this->path, $this->content)) {
            return $response;
        }
        return false;
    }

    public function delete() {
        if ($response = $this->curl->delete($this->path, $this->content)) {
            return $response;
        }
        return false;
    }

    public function get() {
        if ($response = $this->curl->get($this->path)) {
            return $response;
        }
        return false;
    }

    public function enableReturnErrors() {
        $this->setCurl(new AffinityEngine_AffinityItems_Model_Sdk_Core_Curl(true));
    }

}
