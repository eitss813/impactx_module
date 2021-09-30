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
class Sitecrowdfunding_Model_DbTable_Organizations extends Engine_Db_Table
{
   protected $_rowClass = 'Sitecrowdfunding_Model_Organization';


    public function getOrganisationTable(){
        return $this;
    }

    public function fetchOrganizationNamesByProjectId($projectId){

        //MAKE QUERY
        $select = $this->select()
            ->where('project_id = ?', $projectId);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();

        $organizations = array();
        foreach ($result as $value){
            $item = Engine_Api::_()->getItem('sitecrowdfunding_organization', $value['organization_id']);
            $organizations['external'.$value['organization_id']]=  $item->getTitle();
        }
        return $organizations;
    }

    public function fetchOrganizationByProjectId($projectId){

       //MAKE QUERY
       $select = $this->select()
           ->where('project_id = ?', $projectId);
       //RETURN RESULTS
       $result =  $select->query()->fetchAll();

       $organizations = array();
       foreach ($result as $value){
           $item = Engine_Api::_()->getItem('sitecrowdfunding_organization', $value['organization_id']);
           $temp= [];
           $temp['organization_id'] = $value['organization_id'];
           $temp['title'] = $item->getTitle();
           $temp['organization_type'] = $value['organization_type'];
           $temp['description'] = $item->getDescription();
           $temp['link'] = $value['link'];
           $temp['logo'] = $item->getLogoUrl('thumb.profile');
           array_push($organizations,$temp);
       }
       return $organizations;
   }

    public function getParentOrganization($projectId){
       //MAKE QUERY
       $select = $this->select()
           ->where('organization_type = ?', 'parent')
           ->where('project_id = ?', $projectId);
       //RETURN RESULTS
       $result =  $select->query()->fetchAll();

       if(!empty($result) && count($result) > 0){
           $orgId = $result[0]['organization_id'];
           $orgItem = Engine_Api::_()->getItem('sitecrowdfunding_organization', $orgId);
           if(!empty($orgItem)){
               return array( 'title' => $orgItem['title'], 'logo' => $orgItem->getLogoUrl(), 'link' => $orgItem['link']);
           }
           return null;
       }
       return null;
   }

    public function getOrganisationTotalCountByProjectId($project_id){

        $select = new Zend_Db_Select($this->getOrganisationTable()->getAdapter());
        $select
            ->from($this->getOrganisationTable()->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

        $select->where('organization_type = ?', 'parent');
        $select->where('project_id = ?', $project_id);

        $data = $select->query()->fetchAll();
        return (int) $data[0]['count'];
    }


}