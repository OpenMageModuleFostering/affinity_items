<?php

class AffinityEngine_AffinityItems_Model_Source_Recoconfig
{
	public static function getRecoIds()
	{
		$request = new AffinityEngine_AffinityItems_Model_Sdk_Request_SiteRequest(array());
		$data = $request->get();
		return isset($data['recoIds']) ? json_decode($data['recoIds']) : array();
	}

	public function home()
	{
		$recoIds = array();
		$tmp = self::getRecoIds();
		foreach ($tmp as $recoId) { 
			$recoIds[$recoId] = $recoId;
		}
		return $recoIds;
	}

	public function right()
	{
		$recoIds = array();
		$tmp = self::getRecoIds();
		foreach ($tmp as $recoId) { 
			$recoIds[$recoId] = $recoId;
		}
		return $recoIds;
	}

	public function left()
	{
		$recoIds = array();
		$tmp = self::getRecoIds();
		foreach ($tmp as $recoId) { 
			$recoIds[$recoId] = $recoId;
		}
		return $recoIds;
	}

	public function product()
	{
		$recoIds = array();
		$tmp = self::getRecoIds();
		foreach ($tmp as $recoId) { 
			$recoIds[$recoId] = $recoId;
		}
		return $recoIds;
	}

	public function cart()
	{
		$recoIds = array();
		$tmp = self::getRecoIds();
		foreach ($tmp as $recoId) { 
			$recoIds[$recoId] = $recoId;
		}
		return $recoIds;
	}

	public function category()
	{
		$recoIds = array();
		$tmp = self::getRecoIds();
		foreach ($tmp as $recoId) { 
			$recoIds[$recoId] = $recoId;
		}
		return $recoIds;
	}

	public function search()
	{
		$recoIds = array();
		$tmp = self::getRecoIds();
		foreach ($tmp as $recoId) { 
			$recoIds[$recoId] = $recoId;
		}
		return $recoIds;
	}

}