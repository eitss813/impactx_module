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
class Sitecrowdfunding_Model_DbTable_Pages extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Page';

    // get pages ids linked to the projects
    // return data -> array
    public function getProjectPages($project_id) {
        //MAKE QUERY
        $select = $this->select()
            ->where('project_id = ?', $project_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $viewablePageIds = $pagesTable->getOnlyViewablePagesId();

        $linked_pages_id = array();
        if(!empty($result)){
            foreach ($result as $value){
                if(isset($value['page_id']) && !empty($value['page_id']) && in_array($value['page_id'],$viewablePageIds)){
                    array_push($linked_pages_id, $value['page_id']);
                }
            }
        }
        return $linked_pages_id;
    }

    public function getPagesbyProjectId($projectId){
        //MAKE QUERY

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pagesTableName = $pagesTable->info('name');

        $select = $this->select()
            ->where('project_id = ?', $projectId);
        $result =  $select->query()->fetchAll();

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $viewablePageIds = $pagesTable->getOnlyViewablePagesId();

        $organizations = array();
        if(!empty($result)){
            foreach ($result as $value){
                if(isset($value['page_id']) && !empty($value['page_id']) && in_array($value['page_id'],$viewablePageIds)){
                    $pageItem = Engine_Api::_()->getItem('sitepage_page',$value['page_id']);
                    $temp= [];
                    $temp['project_page_id'] = $value['project_page_id'];
                    $temp['page_id'] = $value['page_id'];
                    $temp['title'] = $pageItem->getTitle();
                    $temp['organization_type'] = $value['page_type'];
                    $temp['description'] = $pageItem->getDescription();
                    $temp['link'] = $pageItem->getHref();
                    $temp['logo'] = $pageItem->getPhotoUrl('thumb.profile');
                    array_push($organizations,$temp);
                }
            }
        }
        return $organizations;
    }


    public function getPagesTotalCountbyProjectId($project_id){

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pagesTableName = $pagesTable->info('name');

        $select = $this->select()
            ->where('project_id = ?', $project_id);
        $result =  $select->query()->fetchAll();

        return count($result);
    }


    public function getPageProjects($page_id){
        //MAKE QUERY
        $select = $this->select()
            ->where('page_id = ?', $page_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        $linked_projects_id = array();
        if(!empty($result)){
            foreach ($result as $value){
                $project =  Engine_Api::_()->getItem('sitecrowdfunding_project', $value['project_id']);
                if(!empty($project)){
                    //TODO:hari check whether need to display active projects and add extra condition
                    if($project->isActive() && isset($value['project_id']) && !empty($value['project_id'])){
                        array_push($linked_projects_id, $value['project_id']);
                    }
                }
            }
        }
        return $linked_projects_id;
    }

    public function getViewableAdminPages($user_id){

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $viewablePageIds = $pagesTable->getOnlyViewablePagesId();

        $adminpages = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminPages($user_id);
        $manageadmin_ids = array();
        foreach( $adminpages as $adminpage ) {
            if($adminpage->page_id && in_array($adminpage->page_id, $viewablePageIds))
                $manageadmin_ids[] = $adminpage->page_id;
        }
        $organization = array();
        if(!empty($manageadmin_ids)){
            $pageNames = Engine_Api::_()->getDbtable('pages', 'sitepage')->getPageNamesById($manageadmin_ids);
            foreach ($pageNames as $page){
                $organization[$page->page_id] = $page->title;
            }
        }
        return $organization;
    }

    public function getViewableOrganizationNames($project_id){

        $alreadyLinkedPageIds = $this->getProjectPages($project_id);

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $viewablePageIds = $pagesTable->getOnlyViewablePagesId();

        $pageIds = array();
        foreach( $viewablePageIds as $singlePageId ) {
            if(!in_array($singlePageId, $alreadyLinkedPageIds)){
                $pageIds[] = $singlePageId;
            }
        }

        $organizations = array();
        $pageNames = Engine_Api::_()->getDbtable('pages', 'sitepage')->getPageNamesById($pageIds);
        foreach ($pageNames as $page){
            $organizations[$page->page_id] = $page->title;
        }
        return $organizations;
    }

    public function deleteLinkedRecord($project_id){
        $this->delete(array(
            'project_id = ?' => $project_id
        ));
    }

    public function getPageProjectCount($page_id){
        $count = $this->select()
            ->from($this->info('name'), array('count(*) as count'))
            ->where('page_id = ?', $page_id)
            ->query()
            ->fetchColumn();
        return $count;
    }

    public function getParentPages($projectId){
        //MAKE QUERY
        $select = $this->select()
            ->where('page_type = ?', 'parent')
            ->where('project_id = ?', $projectId);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        if(!empty($result) && count($result) > 0){
            $pageId = $result[0]['page_id'];
            $pageItem = Engine_Api::_()->getItem('sitepage_page', $pageId);
            if(!empty($pageItem)){
                return array( 'page_id'=>$pageId ,'title' => $pageItem->getTitle(), 'logo' => $pageItem->getPhotoUrl(), 'link' => $pageItem->getHref());
            }
            return null;
        }
        return null;
    }

    public function getPagesIdAndName($projectId){

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pagesTableName = $pagesTable->info('name');

        $select = $this->select()
            ->where('project_id = ?', $projectId);
        $result =  $select->query()->fetchAll();

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $viewablePageIds = $pagesTable->getOnlyViewablePagesId();

        $organizations = array();

        if(!empty($result)){
            foreach ($result as $value){
                if(isset($value['page_id']) && !empty($value['page_id']) && in_array($value['page_id'],$viewablePageIds)){
                    $pageItem = Engine_Api::_()->getItem('sitepage_page',$value['page_id']);
                    $organizations['internal'.$value['page_id']] = $pageItem->getTitle();
                }
            }
        }
        return $organizations;

    }

    public function getPageRow($project_id, $page_id){
        $select = $this->select();
        $select->where('project_id = ?', $project_id);
        $select->where('page_id = ?', $page_id);
        return $this->fetchRow($select);
    }

    public function getProjectsIdsByPageId($page_id){
        //MAKE QUERY
        $select = $this->select()
            ->where('page_type = ?','parent')
            ->where('page_id = ?', $page_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        $linked_projects_id = array();
        if(!empty($result)){
            foreach ($result as $value){
                $project =  Engine_Api::_()->getItem('sitecrowdfunding_project', $value['project_id']);
                if(!empty($project)){
                    if(isset($value['project_id']) && !empty($value['project_id'])){
                        array_push($linked_projects_id, $value['project_id']);
                    }
                }
            }
        }
        return $linked_projects_id;
    }
    public function getProjectsIdsByPageIdOrganization($page_id){
        //MAKE QUERY
        $select = $this->select()
            ->where('page_type = ?','parent')
            ->where('page_id = ?', $page_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        $linked_projects_id = array();
        if(!empty($result)){
            foreach ($result as $value){
                $project =  Engine_Api::_()->getItem('sitecrowdfunding_project', $value['project_id']);
                if(!empty($project) && $project['state'] =='published'){
                    if(isset($value['project_id']) && !empty($value['project_id'])){
                        array_push($linked_projects_id, $value['project_id']);
                    }
                }
            }
        }
        return $linked_projects_id;
    }
    public function getProjectsIdsByPageIds($page_ids){
        //MAKE QUERY
        $select = $this->select()
            ->where('page_type = ?','parent')
            ->where('page_id in (?)', $page_ids);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        $linked_projects_id = array();
        if(!empty($result)){
            foreach ($result as $value){
                $project =  Engine_Api::_()->getItem('sitecrowdfunding_project', $value['project_id']);
                if(!empty($project)){
                    if(isset($value['project_id']) && !empty($value['project_id'])){
                        array_push($linked_projects_id, $value['project_id']);
                    }
                }
            }
        }
        return $linked_projects_id;
    }

    /******* INITIATIVES *******/

    // used in initiative landing page - project_galleries tab - show projects in each project-gallery sections
    public function getProjectsByPageIdAndTag($page_id,$tag,$limitCount) {

        // projects
        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        // pages
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        // Tags
        $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tagMapTableName = $tagMapTable->info('name');

        $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
        $tagTableName = $tagTable->info('name');

        $currentDate = date('Y-m-d H:i:s');

        $select = $projectTable->select()
            ->setIntegrityCheck(false)
            ->from($projectTablesName)
            ->joinInner($tagMapTableName, "$tagMapTableName.resource_id = $projectTablesName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
            ->joinInner($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id and " . $tagTableName.".text = '$tag' ",array())
            ->where("$projectTablesName.project_id IN (?)", $projectsIds)
            // naaziya: 15th july 2020: show only published projects only in pages
            // $select->where("$projectTableName.state <> ?", 'draft')
            ->where("$projectTablesName.state = ?", 'published')
            ->where("$projectTablesName.approved = ?", 1)
            ->where("$projectTablesName.start_date <= '$currentDate'")
            ->order(new Zend_Db_Expr("-project_order DESC"))
            ->limit($limitCount);

        $result =  $select->query()->fetchAll();

        return $result;

    }

    // used in initiative landing page - project_galleries tab - total count
    public function getProjectsCountByPageIdAndTag($page_id,$tag) {

        // projects
        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        // pages
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        // Tags
        $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tagMapTableName = $tagMapTable->info('name');

        $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
        $tagTableName = $tagTable->info('name');

        $currentDate = date('Y-m-d H:i:s');

        $count = $projectTable->select()
            ->setIntegrityCheck(false)
            ->from($projectTablesName,new Zend_Db_Expr('COUNT(project_id)'))
            ->joinInner($tagMapTableName, "$tagMapTableName.resource_id = $projectTablesName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
            ->joinInner($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id and " . $tagTableName.".text = '$tag' ",array())
            ->where("$projectTablesName.project_id IN (?)", $projectsIds)
            // naaziya: 15th july 2020: show only published projects only in pages
            // $select->where("$projectTableName.state <> ?", 'draft')
            ->where("$projectTablesName.state = ?", 'published')
            ->where("$projectTablesName.approved = ?", 1)
            ->where("$projectTablesName.start_date <= '$currentDate'")
            ->query()->fetchColumn();

        return $count;

    }


    // used in organisation initiative list - page-profile - count of projects for each initiatives
    public function getProjectsCountByPageIdAndInitiativesIds($page_id,$initiativesId) {

        $currentDate = date('Y-m-d H:i:s');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        $count = 0;

        $sections = array();

        // Initiatives
        $initiative= Engine_Api::_()->getItem('sitepage_initiative', $initiativesId);
        $initiative_id = (Int)$initiative['initiative_id'];
        $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
        $initiativeSections = array_filter(array_map("trim", $initiativeSections));

        if(count($initiativeSections) > 0 ){
            $sections = $initiativeSections;
        }

        // get page projects
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        if(count($sections) > 0 ){
            // Tags
            $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $tagMapTableName = $tagMapTable->info('name');

            $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
            $tagTableName = $tagTable->info('name');

            $count =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'),new Zend_Db_Expr('COUNT(DISTINCT  project_id)'))
                ->joinleft($tagMapTableName,"$tagMapTableName.resource_id = $projectTablesName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'",array())
                ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id",array())
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                ->where("$tagTableName.text IN (?) OR $projectTablesName.initiative_id=$initiativesId",$sections)
                ->query()->fetchColumn();

        }
        // if initiative has no project-galleries, then check if initiative_id is attached in project modal
        else{

            if(count($projectsIds) && $initiative_id) {
                $count =  $projectTable->select()
                    ->from($projectTable->info('name'),new Zend_Db_Expr('COUNT(DISTINCT project_id)'))
                    // naaziya: 15th july 2020: show only published projects only in pages
                    // $select->where("$projectTableName.state <> ?", 'draft')
                    ->where("$projectTablesName.state = ?", 'published')
                    ->where("$projectTablesName.approved = ?", 1)
                    ->where("$projectTablesName.start_date <= '$currentDate'")
                    ->where("$projectTablesName.initiative_id = ?", $initiative_id)
                    ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                    ->query()->fetchColumn();
            } else {
                $count = 0;
            }

        }

        return $count;

    }



    // used in organisation initiative list - page-profile - count of projects for each initiatives
    public function getProjectsCountByPageIdAndInitiativesId($page_id,$initiativesId) {

        $currentDate = date('Y-m-d H:i:s');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        $count = 0;

        $sections = array();

        // Initiatives
        $initiative= Engine_Api::_()->getItem('sitepage_initiative', $initiativesId);
        $initiative_id = (Int)$initiative['initiative_id'];
        $initiativeSections = preg_split('/[,]+/', $initiative['sections']);
        $initiativeSections = array_filter(array_map("trim", $initiativeSections));

        if(count($initiativeSections) > 0 ){
            $sections = $initiativeSections;
        }

        // get page projects
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        if(count($sections) > 0 ){
            // Tags
            $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $tagMapTableName = $tagMapTable->info('name');

            $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
            $tagTableName = $tagTable->info('name');

            $count =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'),new Zend_Db_Expr('COUNT(DISTINCT  project_id)'))
                ->joinleft($tagMapTableName,"$tagMapTableName.resource_id = $projectTablesName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'",array())
                ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id",array())
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                ->where("$tagTableName.text IN (?) OR $projectTablesName.initiative_id=$initiativesId",$sections)
                ->query()->fetchColumn();

        }
        // if initiative has no project-galleries, then check if initiative_id is attached in project modal
        else{
            $count =  $projectTable->select()
                ->from($projectTable->info('name'),new Zend_Db_Expr('COUNT(DISTINCT project_id)'))
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'")
                ->where("$projectTablesName.initiative_id = ?", $initiative_id)
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                ->query()->fetchColumn();
        }

        return $count;

    }

    // used in organisation initiative list - list its project for each initiative
    // without pagination
    public function getProjectsByPageIdAndInitiativesId($page_id,$initiativesId,$project_id) {

        $currentDate = date('Y-m-d H:i:s');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        // Initiatives
        $initiative= Engine_Api::_()->getItem('sitepage_initiative', $initiativesId);
        $sections = preg_split('/[,]+/', $initiative['sections']);
        $sections = array_filter(array_map("trim", $sections));

        // get page projects
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        if(count($sections) > 0 ){
            // Tags
            $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $tagMapTableName = $tagMapTable->info('name');

            $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
            $tagTableName = $tagTable->info('name');

            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                ->joinleft($tagMapTableName,"$tagMapTableName.resource_id = $projectTablesName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'",array())
                ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id",array())
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                ->where("$tagTableName.text IN (?) OR $projectTablesName.initiative_id=$initiativesId",$sections)
                ->order(new Zend_Db_Expr("-project_order DESC"));

            if($project_id) {
                $select->where("$projectTablesName.project_id <> ?", $project_id);
            }

            $result =  $select->query()->fetchAll();
            return $result;

        }
        // if initiative has no project-galleries, then check if initiative_id is attached in project modal
        else{

            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                ->where("$projectTablesName.initiative_id = ?", $initiativesId)
                ->order(new Zend_Db_Expr("-project_order DESC"));

            if($project_id) {
                $select->where("$projectTablesName.project_id <> ?", $project_id);
            }
            $result =  $select->query()->fetchAll();
            return $result;

        }


    }

    public function getProjectsByPageId($projectsIds) {
        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        $select =  $projectTable->select()
            ->setIntegrityCheck(false)
            ->from($projectTable->info('name'))
            ->where("$projectTablesName.state = ?", 'published')
            ->where("$projectTablesName.approved = ?", 1)
            ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
            ->order(new Zend_Db_Expr("-project_order DESC"));

        $result =  $select->query()->fetchAll();
        return $result;
    }
    public function getProjectByCategory($category_id) {
        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        $select =  $projectTable->select()
            ->setIntegrityCheck(false)
            ->from($projectTable->info('name'))
            ->where("$projectTablesName.state = ?", 'published')
            ->where("$projectTablesName.approved = ?", 1)
            ->where('category_id =?', $category_id)
            ->order(new Zend_Db_Expr("-project_order DESC"));

        $result =  $select->query()->fetchAll();
        return $result;
    }

    // with pagination
    public function getProjectPaginatorByPageIdAndInitiativesId($page_id,$initiativesId,$project_id) {
        return Zend_Paginator::factory($this->getProjectsSelectByPageIdAndInitiativesId($page_id,$initiativesId,$project_id));
    }
    public function getProjectsSelectByPageIdAndInitiativesId($page_id,$initiativesId,$project_id) {

        $currentDate = date('Y-m-d H:i:s');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        // Initiatives
        $initiative= Engine_Api::_()->getItem('sitepage_initiative', $initiativesId);
        $sections = preg_split('/[,]+/', $initiative['sections']);
        $sections = array_filter(array_map("trim", $sections));

        // get page projects
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        if(count($sections) > 0 ){
            // Tags
            $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $tagMapTableName = $tagMapTable->info('name');

            $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
            $tagTableName = $tagTable->info('name');

            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                ->joinleft($tagMapTableName,"$tagMapTableName.resource_id = $projectTablesName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'",array())
                ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id",array())
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                ->where("$tagTableName.text IN (?) OR $projectTablesName.initiative_id=$initiativesId",$sections)
                ->order(new Zend_Db_Expr("-project_order DESC"));

            if($project_id) {
                $select->where("$projectTablesName.project_id <> ?", $project_id);
            }

            //$result =  $select->query()->fetchAll();
            return $select;

        }
        // if initiative has no project-galleries, then check if initiative_id is attached in project modal
        else{

            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                ->where("$projectTablesName.initiative_id = ?", $initiativesId)
                ->order(new Zend_Db_Expr("-project_order DESC"));

            if($project_id) {
                $select->where("$projectTablesName.project_id <> ?", $project_id);
            }
            //$result =  $select->query()->fetchAll();
            return $select;

        }


    }

    // used in organisation initiative list - list its project where not in initiative using single page_id
    public function getNonInitiativeProjectsByPageId($page_id) {

        $currentDate = date('Y-m-d H:i:s');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        // Initiatives
        $initiatives = Engine_Api::_()->getDbTable('initiatives','sitepage')->getAllInitiativesByPageId($page_id);
        $sectionsArr = array();
        if(count($initiatives) > 0 ){
            foreach($initiatives as $initiative){
                $sections = preg_split('/[,]+/', $initiative['sections']);
                $sections = array_filter(array_map("trim", $sections));
                $sectionsArr = array_merge($sections,$sectionsArr);
            }
        }

        // get page projects
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        // if no sections, then check if initiative_id is null in project modal
        if(count($sectionsArr) > 0){

            // Tags
            $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $tagMapTableName = $tagMapTable->info('name');

            $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
            $tagTableName = $tagTable->info('name');

            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                ->joinleft($tagMapTableName,"$tagMapTableName.resource_id = $projectTablesName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'",array())
                ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id",array())
                ->where("($tagTableName.text is null OR $tagTableName.text NOT IN (?))", $sectionsArr)
                ->where("$projectTablesName.initiative_id IS NULL")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'");

            $result =  $select->query()->fetchAll();

            return $result;

        }
        // if initiative has no project-galleries, then check if initiative_id is not attached to any projects in project modal
        else{
            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                ->where("$projectTablesName.initiative_id IS NULL")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'");

            $result =  $select->query()->fetchAll();

            return $result;
        }

    }

    // used in organisation initiative list - list its project where not in initiative using multiple page_id
    // without pagination
    public function getNonInitiativeProjectsByPageIds($page_ids) {

        $currentDate = date('Y-m-d H:i:s');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        // Initiatives
        $initiatives = Engine_Api::_()->getDbTable('initiatives','sitepage')->getAllInitiativesByPageIds($page_ids);
        $sectionsArr = array();
        if(count($initiatives) > 0 ){
            foreach($initiatives as $initiative){
                $sections = preg_split('/[,]+/', $initiative['sections']);
                $sections = array_filter(array_map("trim", $sections));
                $sectionsArr = array_merge($sections,$sectionsArr);
            }
        }

        // get page projects
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageIds($page_ids);

        // if no sections, then check if initiative_id is null in project modal
        if(count($sectionsArr) > 0){

            // Tags
            $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $tagMapTableName = $tagMapTable->info('name');

            $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
            $tagTableName = $tagTable->info('name');

            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                ->joinleft($tagMapTableName,"$tagMapTableName.resource_id = $projectTablesName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'",array())
                ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id",array())
                ->where("($tagTableName.text is null OR $tagTableName.text NOT IN (?))", $sectionsArr)
                ->where("$projectTablesName.initiative_id IS NULL")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'");

            $result =  $select->query()->fetchAll();

            return $result;

        }
        // if initiative has no project-galleries, then check if initiative_id is not attached to any projects in project modal
        else{
            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                ->where("$projectTablesName.initiative_id IS NULL")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'");

            $result =  $select->query()->fetchAll();

            return $result;
        }

    }
    // with pagination
    public function getNonInitiativeProjectsPaginatorByPageIds($page_ids) {
        return Zend_Paginator::factory($this->getNonInitiativeProjectsSelectByPageIds($page_ids));
    }
    public function getNonInitiativeProjectsSelectByPageIds($page_ids) {

        $currentDate = date('Y-m-d H:i:s');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        // Initiatives
        $initiatives = Engine_Api::_()->getDbTable('initiatives','sitepage')->getAllInitiativesByPageIds($page_ids);
        $sectionsArr = array();
        if(count($initiatives) > 0 ){
            foreach($initiatives as $initiative){
                $sections = preg_split('/[,]+/', $initiative['sections']);
                $sections = array_filter(array_map("trim", $sections));
                $sectionsArr = array_merge($sections,$sectionsArr);
            }
        }

        // get page projects
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageIds($page_ids);

        // if no sections, then check if initiative_id is null in project modal
        if(count($sectionsArr) > 0){

            // Tags
            $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $tagMapTableName = $tagMapTable->info('name');

            $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
            $tagTableName = $tagTable->info('name');

            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                ->joinleft($tagMapTableName,"$tagMapTableName.resource_id = $projectTablesName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'",array())
                ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id",array())
                ->where("($tagTableName.text is null OR $tagTableName.text NOT IN (?))", $sectionsArr)
                ->where("$projectTablesName.initiative_id IS NULL")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'");

            $result =  $select->query()->fetchAll();

            return $result;

        }
        // if initiative has no project-galleries, then check if initiative_id is not attached to any projects in project modal
        else{
            $select =  $projectTable->select()
                ->setIntegrityCheck(false)
                ->from($projectTable->info('name'))
                ->distinct()
                ->where("$projectTablesName.initiative_id IS NULL")
                ->where($projectTablesName . '.project_id IN (?)', $projectsIds)
                // naaziya: 15th july 2020: show only published projects only in pages
                // $select->where("$projectTableName.state <> ?", 'draft')
                ->where("$projectTablesName.state = ?", 'published')
                ->where("$projectTablesName.approved = ?", 1)
                ->where("$projectTablesName.start_date <= '$currentDate'");

            $result =  $select->query()->fetchAll();

            return $result;
        }

    }

    // used in project details - to get project initiative names
    public function getProjectInitiatives($page_id, $tags){

        if(is_array($tags)) {
            if(count($tags) > 0){

                // initiatives
                $initiativesTable = Engine_Api::_()->getDbtable('initiatives', 'sitepage');
                $initiativesTableName = $initiativesTable->info('name');

                $select = $initiativesTable->select()->from($initiativesTableName);

                if(is_array($tags)) {
                    foreach($tags as $tag){
                        $select->orwhere("$initiativesTableName.sections LIKE ?", '%' . $tag . '%');
                    }
                }

                $select ->where("$initiativesTableName.page_id = ?", $page_id);

                $result =  $select->query()->fetchAll();

                return $result;

            }
        }
        return array();

    }

    public function getAllActiveProjectsByPageId($page_id){
        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTablesName = $projectTable->info('name');

        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getProjectsIdsByPageId($page_id);

        $currentDate = date('Y-m-d H:i:s');

        $select = $projectTable->select()
            ->from($projectTable->info('name'), array('project_id'))
            ->where("$projectTablesName.project_id IN (?)", $projectsIds)
            ->where("$projectTablesName.state = ?", 'published')
            //->where("$projectTablesName.approved = ?", 1)
            //->where("$projectTablesName.start_date <= '$currentDate'")
            ->order(new Zend_Db_Expr("-project_order DESC"));

        $result =  $select->query()->fetchAll();

        return $result;
    }

}