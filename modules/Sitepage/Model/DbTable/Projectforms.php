<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Pages.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Projectforms extends Engine_Db_Table {

   // protected $_rowClass = "Sitepage_Model_Projectforms";
    protected $_rowClass = 'Sitepage_Model_Projectforms';



    public function projectForms($project_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        $formsSubmissionTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $formsSubmissionName = $formsSubmissionTable->info('name');

        $entriesSelect = $formsSubmissionTable->select()
            ->from($formsSubmissionName, array('form_id'))
            ->where('project_id IN (?)', $project_id)
            ->where('submission_status = ?', 'submitted')
            ->order('creation_date DESC');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->from($projectformsName, '*')
            ->where("$projectformsName.project_id IN (?)", $project_id)
            ->where("$projectformsName.form_id NOT IN (?)", $entriesSelect)
            ->order('creation_date DESC');

        $paginator = Zend_Paginator::factory($select);
        return $paginator;
    }

    public function userForms($user_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        $formsSubmissionTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $formsSubmissionName = $formsSubmissionTable->info('name');

        $entriesSelect = $formsSubmissionTable->select()
            ->from($formsSubmissionName, array('form_id'))
            ->where('user_id IN (?)', $user_id)
            ->where('submission_status = ?', 'submitted')
            ->order('creation_date DESC');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->from($projectformsName, '*')
            ->where('user_id IN (?)', $user_id)
            ->where("form_id NOT IN (?)", $entriesSelect)
            ->order('creation_date DESC');

        $paginator = Zend_Paginator::factory($select);
        return $paginator;
    }

    public function projectFormsSubmitted($project_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $projectformsName = $projectformsTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->from($projectformsName, '*')
            ->where('project_id IN (?)', $project_id)
            ->where('submission_status = ?', 'submitted')
            ->order('creation_date DESC');
        //  return $this->fetchAll($select);
        $paginator = Zend_Paginator::factory($select);
        return $paginator;
    }

    public function userFormsSubmitted($user_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $projectformsName = $projectformsTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->from($projectformsName, '*')
            ->where('user_id IN (?)', $user_id)
            ->where('submission_status = ?', 'submitted')
            ->order('creation_date DESC');

        $paginator = Zend_Paginator::factory($select);
        return $paginator;
    }

    public function getProjectsIdsByformId($form_id){
        //MAKE QUERY
        $projectformsTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $projectformsName = $projectformsTable->info('name');

        $select = $projectformsTable->select()
            ->where('form_id = ?', $form_id);
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
    public function getProjectsIdsAssignedByformId($form_id){

        //MAKE QUERY
        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        $select = $projectformsTable->select('project_id')
            ->where('form_id = ?', $form_id);

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
    public function getProjectAssiginedCount($form_id,$page_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->from($projectformsName, 'project_id')
            ->where('form_id = ?', $form_id)
            ->where('page_id = ?', $page_id)
             ->where('project_id IS NOT NULL');
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        return $result;
//            ->from($projectformsName,array('count(*)'))
//            ->where('form_id =', $form_id);
//          return $this->fetchAll($select);
//        $paginator = Zend_Paginator::factory($select);
//        return $paginator;
    }
    public function getUserAssiginedCount($form_id,$page_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->from($projectformsName, 'user_id')
            ->where('form_id = ?', $form_id)
            ->where('page_id = ?', $page_id)
            ->where('project_id IS NULL');
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        return $result;

    }
    public function getProjectAssiginedCountByFormId($form_id,$project_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

                //REVIEW PROFILE TYPE UPDATION WORK
                $select = $projectformsTable->select()
                    ->where('form_id = ?', $form_id)
                    ->where('project_id = ?', $project_id);
                //RETURN RESULTS
                $result =  $select->query()->fetchAll();
                return count($result);
//            ->from($projectformsName,array('count(*)'))
//            ->where('form_id =', $form_id);
//          return $this->fetchAll($select);
//        $paginator = Zend_Paginator::factory($select);
//        return $paginator;
    }

    public function getUserAssiginedCountByFormId($form_id,$user_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->where('form_id = ?', $form_id)
            ->where('user_id = ?', $user_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        return count($result);

    }
    public function getProjectAssiginedCountByFormIds($form_id,$project_id,$page_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->where('form_id = ?', $form_id)
            ->where('page_id = ?', $page_id)
            ->where('project_id = ?', $project_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        return count($result);
//            ->from($projectformsName,array('count(*)'))
//            ->where('form_id =', $form_id);
//          return $this->fetchAll($select);
//        $paginator = Zend_Paginator::factory($select);
//        return $paginator;
    }
    public function getUserAssiginedCountByFormIds($form_id,$user_id,$page_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->where('form_id = ?', $form_id)
            ->where('page_id = ?', $page_id)
            ->where('user_id = ?', $user_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        return count($result);

    }
    public function getProjectIdsByFormIdPageId($form_id,$page_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');
        $linked_projects_id = array();
        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->from($projectformsName, 'project_id')
            ->where('form_id = ?', $form_id)
            ->where('page_id = ? ', $page_id);
        // return   $projectformsTable->fetchAll($select);
         //$paginator = Zend_Paginator::factory($select);
        $arr = $select->query()->fetchAll();
        foreach ($arr as $value){
          array_push($linked_projects_id, $value['project_id']);
        }
           return $linked_projects_id;
    }
    public function getFormsAssiginedCountByPageId($form_id,$page_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->where('form_id = ?', $form_id)
            ->where('page_id = ?', $page_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        return count($result);

    }
    public function getEntryIdByProjectFormId($form_id,$project_id) {

        $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $entryName = $entryTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $entryTable->select()
            ->from($entryName, 'entry_id')
            ->where('form_id = ?', $form_id)
            ->where('project_id = ? ', $project_id)
            ->where('submission_status = ?', 'submitted');

        $arr = $select->query()->fetchAll();

        return $arr;
    }
    public function getEntryIdByUserFormId($form_id,$user_id) {

        $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $entryName = $entryTable->info('name');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $entryTable->select()
            ->from($entryName, 'entry_id')
            ->where('form_id = ?', $form_id)
            ->where('user_id = ? ', $user_id)
            ->where('submission_status = ?', 'submitted');

        $arr = $select->query()->fetchAll();

        return $arr;
    }
    public function getProjectFormDetails($form_id) {

        $select = $this->select()
            ->where('form_id = ?', $form_id);

        $result =  $select->query()->fetchAll();
        return $result;
    }


    public function formProjects($form_id,$page_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');
        //$projectIds = this.getProjectsIdsByformId($form_id);


        //MAKE QUERY
        $projectformsTables = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $projectformsNames = $projectformsTables->info('name');

        $selects = $projectformsTables->select()
            ->where('form_id = ?', $form_id);
        //RETURN RESULTS
        $result =  $selects->query()->fetchAll();
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

        if(count($linked_projects_id) > 0)
        {
            //REVIEW PROFILE TYPE UPDATION WORK
            $select1 = $projectformsTable->select()
                ->from($projectformsName, '*')
                ->where('form_id = (?)', $form_id)
                ->where('page_id = (?)', $page_id)
                ->where('project_id NOT IN (?)', $linked_projects_id)
                ->order('creation_date DESC');
            $paginator = Zend_Paginator::factory($select1);
        }else {
            $select1 = $projectformsTable->select()
                ->from($projectformsName, '*')
                ->where('form_id = (?)', $form_id)
                ->where('page_id = (?)', $page_id)
                ->order('creation_date DESC');
            $paginator = Zend_Paginator::factory($select1);
        }

        return $paginator;


        //  return $this->fetchAll($select);
    }

    public function formByPageId($form_id,$page_id,$page_no) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        $formsSubmissionTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $formsSubmissionName = $formsSubmissionTable->info('name');

        $entriesProjectSelect = $formsSubmissionTable->select()
            ->from($formsSubmissionName, array('project_id'))
            ->where('form_id IN (?)', $form_id)
            ->where('project_id IS NOT NULL')
            ->where('submission_status  IN (?)', array('submitted'))
            ->order('creation_date DESC');

        $entriesUserSelect = $formsSubmissionTable->select()
            ->from($formsSubmissionName, array('user_id'))
            ->where('form_id IN (?)', $form_id)
            ->where('user_id IS NOT NULL')
            ->where('submission_status  IN (?)', array('submitted'))
            ->order('creation_date DESC');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->from($projectformsName)
            ->where('page_id = ?', $page_id)
            ->where('form_id = ?', $form_id)
            ->where("(project_id not IN ($entriesProjectSelect) AND user_id IS  NULL ) OR (user_id not IN ($entriesUserSelect) AND project_id IS  NULL)")
            ->order('creation_date DESC');


         $paginator = Zend_Paginator::factory($select);



        if (!empty($page_no)) {
            $paginator->setCurrentPageNumber($page_no);
        }
        $paginator->setItemCountPerPage(5);
        return $paginator;



    }
    public function totalFormByPageId($form_id,$page_id) {

        $projectformsTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectformsName = $projectformsTable->info('name');

        $formsSubmissionTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $formsSubmissionName = $formsSubmissionTable->info('name');

        $entriesProjectSelect = $formsSubmissionTable->select()
            ->from($formsSubmissionName, array('project_id'))
            ->where('form_id IN (?)', $form_id)
            ->where('project_id IS NOT NULL')
            ->where('submission_status  IN (?)', array('submitted'))
            ->order('creation_date DESC');

        $entriesUserSelect = $formsSubmissionTable->select()
            ->from($formsSubmissionName, array('user_id'))
            ->where('form_id IN (?)', $form_id)
            ->where('user_id IS NOT NULL')
            ->where('submission_status  IN (?)', array('submitted'))
            ->order('creation_date DESC');

        //REVIEW PROFILE TYPE UPDATION WORK
        $select = $projectformsTable->select()
            ->from($projectformsName)
            ->where('page_id = ?', $page_id)
            ->where('form_id = ?', $form_id)
            ->where("(project_id not IN ($entriesProjectSelect) AND user_id IS  NULL ) OR (user_id not IN ($entriesUserSelect) AND project_id IS  NULL)")
            ->order('creation_date DESC');




        return count($this->fetchAll($select));



    }
}