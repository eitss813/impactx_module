<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_OrganizationController extends Seaocore_Controller_Action_Standard {

//    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
//    public function init()
//    {
//
//        //AUTHORIZATION CHECK
//        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "view")->isValid())
//            return;
//
//        //SET SUBJECT
//        if (!Engine_Api::_()->core()->hasSubject()) {
//            if (0 != ($project_id = (int)$this->_getParam('project_id')) &&
//                null != ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id))) {
//                Engine_Api::_()->core()->setSubject($project);
//            }
//        }
//    }

    public function createAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "view")->isValid())
            return;

        //IF PROJECT IS NOT EXIST
        if (empty($project)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET PROJECT SUBJECT
        Engine_Api::_()->core()->setSubject($project);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }

        //SELECTED TAB
        $this->view->TabActive = "organization";

        $this->view->form = $form = new Sitecrowdfunding_Form_Organization_Create(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }


        if($_POST['is_internal'] == 1){
            $form->title->setRequired(false);
            $form->title->setAllowEmpty(true);

        }else{
            $form->organization_id->setRequired(false);
            $form->organization_id->setAllowEmpty(true);
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($values))
            return;

        if($values['is_internal'] == 1){

            if(empty($values['organization_id'])){
                $error = $this->view->translate('Please select organization - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $table = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
            $pagerow = $table->createRow();
            $pagerow->project_id = $project_id;
            $pagerow->page_id = $values['organization_id'];
            $pagerow->page_type = $values['organization_type'];
            $pagerow->owner_id = $viewer_id;
            $pagerow->save();

        }else{

            //title IS REQUIRED FIELD
            if (empty($values['title'])) {
                 $error = $this->view->translate('Please complete title field - it is required.');
                 $error = Zend_Registry::get('Zend_Translate')->_($error);

                 $form->getDecorator('errors')->setOption('escape', false);
                 $form->addError($error);
                return;
            }

            $table = Engine_Api::_()->getDbTable('organizations', 'sitecrowdfunding');
            $organization = $table->createRow();

            $file_id = null;
            if (!empty($values['photo'])) {
                $file_id =  $organization->setLogo($form->photo);
            }
            $organization->title = $values['title'];
            $organization->description = $values['description'];
            $organization->project_id = $project_id;
            $organization->organization_type = $values['organization_type'];
            $organization->others = $values['others'];
            $organization->link = $values['link'];
            $organization->logo = $file_id;
            $organization->save();
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'editorganizations', 'project_id' => $project_id), "sitecrowdfunding_organizationspecific", true);

    }

    public function editorganizationsAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $change_url = $this->_getParam('change_url', 0);
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "view")->isValid())
            return;

        //IF PROJECT IS NOT EXIST
        if (empty($project)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET PROJECT SUBJECT
        Engine_Api::_()->core()->setSubject($project);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

      /*  // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }
    */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }

        //SELECTED TAB
        $this->view->TabActive = "organization";
        $this->view->externalorganizations =  $externalorganizations = Engine_Api::_()->getDbtable('organizations','sitecrowdfunding')->fetchOrganizationByProjectId($project_id);
        $this->view->internalorganizations =  $internalorganizations = Engine_Api::_()->getDbtable('pages','sitecrowdfunding')->getPagesbyProjectId($project_id);


    }

    public function deleteAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->type = $type = $this->_getParam('type', null);
        $this->view->org_id  = $org_id  =  $this->_getParam('org_id', null);

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if(!empty($type) && !empty($org_id)){

            $itemName = null;
            $message = '';
            if($type == 'internal'){
                $itemName = 'sitecrowdfunding_page';
                $message = 'Organization has been unlinked successfully.';
            }else{
                $itemName= 'sitecrowdfunding_organization';
                $message = 'This organization has been removed successfully.';
            }

            $item  = Engine_Api::_()->getItem($itemName, $org_id);

            $item->delete();

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_($message))
            ));

//            // tell smoothbox to close
//            $this->view->status  = true;
//            $this->view->message = Zend_Registry::get('Zend_Translate')->_('This organization has been removed.');
//            $this->view->smoothboxClose = true;
//            return $this->render('deleteSuccess');
        }

    }


}