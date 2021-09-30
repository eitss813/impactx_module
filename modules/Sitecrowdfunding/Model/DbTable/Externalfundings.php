<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Externalfundings   extends Engine_Db_Table
{
   protected $_rowClass = 'Sitecrowdfunding_Model_Externalfunding';

    public function getAllExternalFunding($project_id) {

        $fundingTable = Engine_Api::_()->getDbtable('externalfundings', 'sitecrowdfunding');
        $fundingTableName = $fundingTable->info( 'name' ) ;
        $fundingSelect = $fundingTable->select()
            ->from( $fundingTableName)
            ->where( 'project_id = ?' , $project_id )
            ->order( 'funding_date DESC' );

        $result =  $fundingSelect->query()->fetchAll() ;

        $externalfunding = array();
        foreach ($result as $item){
            $temp= [];
            if($item['resource_type'] == 'organization' && $item['is_external'] == 1 ){
                // if id not given check if name is given
                if(empty($item['resource_id'])){
                    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                    $photoURL = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/modules/Sitepage/externals/images/nophoto_sitepage_thumb_icon.png';
                    if(!empty($item['resource_name'])){
                        $temp['title'] = $item['resource_name'];
                    }else{
                        $temp['title'] = '';
                    }
                    $temp['link'] = '';
                    $temp['logo'] = $photoURL;
                }else{
                    $orgItem = Engine_Api::_()->getItem('sitecrowdfunding_organization', $item['resource_id']);
                    $temp['title'] = $orgItem->getTitle();
                    $temp['link'] = $orgItem->getHref();
                    $temp['logo'] = $orgItem->getLogoUrl();
                }
                $temp['type'] = 'Organization';
            }else if($item['resource_type'] == 'organization' && $item['is_external'] == 0 ){
                $pageItem = Engine_Api::_()->getItem('sitepage_page',$item['resource_id']);
                $temp['title'] = $pageItem->getTitle();
                $temp['type'] = 'Organization';
                $temp['link'] = $pageItem->getHref();
                $temp['logo'] = $pageItem->getPhotoUrl();
            }else{
                $userItem = Engine_Api::_()->getItem('user',$item['resource_id']);
                $temp['title'] = $userItem->getTitle();
                $temp['type'] = 'Member';
                $temp['link'] = $userItem->getHref();
                $temp['logo'] = $userItem->getPhotoUrl();
            }
            $temp['amount'] = $item['funding_amount'];
            $temp['funding_date'] = $item['funding_date'];
            $temp['notes'] = $item['notes'];
            $temp['externalfunding_id'] = $item['externalfunding_id'];
            array_push($externalfunding, $temp);
        }
        return $externalfunding;
    }

    public function getExternalFundingAmountChart($project_id){

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);


        $orgFunding = $this->getOrgFundingAmount($project_id);
        //member funding total
        $memberTotal = $this->getMemberFundingAmountTotal($project_id);

        $externalfunding = array();
        $tempFundingAmount = 0;
        foreach ($orgFunding as $item){
            $tempFundingAmount = $tempFundingAmount + $item['funding_amount'];
        }

        $totalFundingAmtByMemberOrg = $tempFundingAmount + $memberTotal;

        if(!empty($project->invested_amount) && $project->invested_amount != 0){
            array_push($externalfunding, array(
                'title' => 'Family Contribution',
                'funding_amount' => $project->invested_amount
            ));
            //$totalFundingAmtByMemberOrg = $totalFundingAmtByMemberOrg + $project->invested_amount;
        }

        if(!empty($memberTotal)){
            array_push($externalfunding, array(
                'title' => 'Already Funded',
                'funding_amount' => $totalFundingAmtByMemberOrg
            ));
        }

        if(!empty($project->goal_amount - $tempFundingAmount)){
            array_push($externalfunding, array(
                'title' => 'Yet to be Funded',
                'funding_amount' => $project->goal_amount - $totalFundingAmtByMemberOrg - $project->invested_amount
            ));
        }

        return array(
            'totalFundingAmount' => $totalFundingAmtByMemberOrg,
            'fundingData' => array_reverse($externalfunding),
            'memberCount' => 0,
            'orgCount' => 0
        );
    }

    public function getExternalFundingAmount($project_id){

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);



        $result = $this->getMemberFundingAmount($project_id);
        $result2 = $this->getOrgFundingAmount($project_id);

        $common_result = array_merge($result, $result2);
        $externalfunding = array();
        $tempFundingAmount = 0;
        foreach ($common_result as $item){
            $temp= [];
            if($item['resource_type'] == 'organization' && $item['is_external'] == 1 ){
                if(empty($item['resource_id'])){
                    if(!empty($item['resource_name'])){
                        $temp['title'] = $item['resource_name'];
                    }else{
                        $temp['title'] = '';
                    }
                }else{
                    $orgItem = Engine_Api::_()->getItem('sitecrowdfunding_organization', $item['resource_id']);
                    $temp['title'] = $orgItem->getTitle();
                }
            }else if($item['resource_type'] == 'organization' && $item['is_external'] == 0 ){
                $pageItem = Engine_Api::_()->getItem('sitepage_page',$item['resource_id']);
                $temp['title'] = $pageItem->getTitle();
            }else{
                $userItem = Engine_Api::_()->getItem('user',$item['resource_id']);
                $temp['title'] = $userItem->getTitle();
            }
            $temp['funding_amount'] = $item['funding_amount'];
            $tempFundingAmount = $tempFundingAmount + $item['funding_amount'];
            array_push($externalfunding, $temp);
        }

        if(!empty($project->invested_amount)){
            array_push($externalfunding, array(
                'title' => 'Family Contribution',
                'funding_amount' => $project->invested_amount
            ));
            $tempFundingAmount = $tempFundingAmount + $project->invested_amount;
        }


        if(!empty($project->goal_amount - $tempFundingAmount)){
            array_push($externalfunding, array(
                'title' => 'Yet to be Funded',
                'funding_amount' => $project->goal_amount - $tempFundingAmount
            ));
        }
//        if(!empty($memberTotal)){
//            array_push($externalfunding, array(
//                'title' => 'Already Funded',
//                'funding_amount' => $memberTotal
//            ));
//        }

        return array(
            'totalFundingAmount' => $tempFundingAmount,
            'fundingData' => array_reverse($externalfunding),
            'memberCount' => count($result),
            'orgCount' => count($result2)
        );
    }

    public function getMemberFundingAmountTotal($project_id){
        $total=0;
        // fetching internal backers
        $params = array('project_id' => $project_id);
        $result2 =  Engine_Api::_()->getDBTable('backers','sitecrowdfunding')->getInternalBackers($params);
        $result2 = empty($result2) ? [] : $result2->toArray();
        // fetching internal backers

        // fetching external backers
        $select = $this->select();
        $select->from($this->info('name'), array('sum(funding_amount) as funding_amount','resource_type','resource_id','is_external'));
        $select->where('project_id = ?', $project_id);
        $select->where('resource_type = ?', 'member');
        $select->group('resource_id');
        $result = $this->fetchAll($select);
        $result = empty($result) ? [] : $result->toArray();
        // fetching external backers


        // merging the data
        $result3 = array_merge($result2,$result);
        // merging the data

        // total the funding amount if resource id is same
        $result4 = array();
        foreach($result3 as $data) {
            $result4[ $data['resource_id'] ] += $data['funding_amount'];
        }
        // total the funding amount if resource id is same

        // preparing the array to fetching the name
        $memebers = array();
        foreach ($result4 as $key => $item){
            $temp = [];
            $temp['resource_id'] = $key;
            $temp['funding_amount'] = $item;
            $total += $item;
            array_push($memebers, $temp);
        }
        // preparing the array to fetching the name

        return $total;

    }
    public function getMemberFundingAmount($project_id){

        // fetching internal backers
        $params = array('project_id' => $project_id);
        $result2 =  Engine_Api::_()->getDBTable('backers','sitecrowdfunding')->getInternalBackers($params);
        $result2 = empty($result2) ? [] : $result2->toArray();
        // fetching internal backers

        // fetching external backers
        $select = $this->select();
        $select->from($this->info('name'), array('sum(funding_amount) as funding_amount','resource_type','resource_id','is_external'));
        $select->where('project_id = ?', $project_id);
        $select->where('resource_type = ?', 'member');
        $select->group('resource_id');
        $result = $this->fetchAll($select);
        $result = empty($result) ? [] : $result->toArray();
        // fetching external backers


        // merging the data
        $result3 = array_merge($result2,$result);
        // merging the data

        // total the funding amount if resource id is same
        $result4 = array();
        foreach($result3 as $data) {
            $result4[ $data['resource_id'] ] += $data['funding_amount'];
        }
        // total the funding amount if resource id is same

        // preparing the array to fetching the name
        $memebers = array();
        foreach ($result4 as $key => $item){
            $temp = [];
            $temp['resource_id'] = $key;
            $temp['funding_amount'] = $item;
            array_push($memebers, $temp);
        }
        // preparing the array to fetching the name

        return $memebers;

    }

    public function getOrgFundingAmount($project_id){
        $select = $this->select();
        $select->from($this->info('name'),array('sum(funding_amount) as funding_amount','resource_type','resource_id','is_external'));
        $select->where('project_id = ?', $project_id);
        $select->where('resource_type = ?', 'organization');
        $select->group('resource_id');
        $select->group('resource_name');
        $result = $this->fetchAll($select);
        return empty($result) ? [] : $result->toArray();
    }

    public function getFundingAmountByOrgId($page_id){
        $select = $this->select();
        $select->from($this->info('name'),array('sum(funding_amount) as funding_amount'));
        $select->where('resource_id = ?', $page_id);
        $select->where('resource_type = ?', 'organization');
        $select->where('is_external = ?', 0);
        $select->group('resource_id');
        $result = $this->fetchRow($select);
        return empty($result) ? 0 : $result->toArray();
    }

    public function getFundingAmountByUserId($project_id, $user_id){
        $select = $this->select();
        $select->from($this->info('name'), 'sum(funding_amount) as funding_amount');
        $select->where('project_id = ?', $project_id);
        $select->where('resource_type = ?', 'member');
        if($user_id){
            $select->where('resource_id = ?', $user_id);
        }
        $select->group('project_id');

        $result = $this->fetchAll($select);
        if(!empty($result)){
            $result = $result->toArray();
            if(count($result) > 0){
                return $result[0]['funding_amount'];
            }
            return 0;
        }
        return 0;
    }

    public function getFundingAmountORG($project_id){
        $select = $this->select();
        $select->from($this->info('name'), 'sum(funding_amount) as funding_amount');
        $select->where('project_id = ?', $project_id);
        $select->where('resource_type = ?', 'organization');
        $select->group('project_id');

        $result = $this->fetchAll($select);
        if(!empty($result)){
            $result = $result->toArray();
            if(count($result) > 0){
                return $result[0]['funding_amount'];
            }
            return 0;
        }
        return 0;
    }

}
