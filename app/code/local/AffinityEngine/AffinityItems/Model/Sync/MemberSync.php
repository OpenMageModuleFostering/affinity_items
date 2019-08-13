<?php

class AffinityEngine_AffinityItems_Model_Sync_MemberSync extends AffinityEngine_AffinityItems_Model_Sync_Sync {

    public function syncMember($sync_count = 300, $new = true) {
        $countMember = $this->getMembersForSync()->count();
        $countPage = ceil($countMember / $sync_count);
        for ($cPage = 0; $cPage <= ($countPage - 1); $cPage++) {
            $members = $this->getMembersForSync()->setPageSize($sync_count);
            $memberList = array();
            foreach ($members as $member) {
                $aemember = new stdClass();
                $aemember->memberId = $member->getId();
                $aemember->firstname = $member->getFirstname();
                $aemember->lastname = $member->getLastname();
                $aemember->email = $member->getEmail();
                $aemember->birthday = '';
                array_push($memberList, $aemember);
            }
            $request = new AffinityEngine_AffinityItems_Model_Sdk_Request_MemberRequest($memberList);
            if ($request->post()) {
                foreach ($memberList as $member) {
                    $memb = Mage::getModel('customer/customer')->load($member->memberId);
                    $memb->setData('ae_sync', 1)->save();
                }
            } else {
                echo "bad connection";
            }
        }
    }

}

?>