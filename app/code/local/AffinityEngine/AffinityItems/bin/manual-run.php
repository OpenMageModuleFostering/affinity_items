#!/usr/bin/env php
<?php
ini_set('memory_limit','256M');
$mageFilename = '/../../../../../Mage.php';
require_once dirname(__FILE__) . $mageFilename;
Mage::app();
Mage::register('run_from_shell', true); // for debugging only, disable if this file is used for production
Mage::register('curl_debug', false); // for CURL debugging only, disable if this file is used for production
Mage::getModel('affinityitems/cron')->sync();
Mage::unregister('run_from_shell');
Mage::unregister('curl_debug');
?>