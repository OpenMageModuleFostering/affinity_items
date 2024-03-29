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

class AffinityEngine_AffinityItems_Model_Sdk_Request_MemberRequest extends AffinityEngine_AffinityItems_Model_Sdk_Core_AbstractRequest {

	public function __construct($pcontent) { 
		if(is_array($pcontent)) {
			parent::__construct('/members', array("members" => $pcontent));
		}
		else if(is_object($pcontent)) {
			parent::__construct('/member/' . $pcontent->memberId, $pcontent);
		}
	}

}

?>