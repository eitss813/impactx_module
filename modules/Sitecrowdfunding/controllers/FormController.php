<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FormController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_FormController extends Core_Controller_Action_Standard
{

  public function indexAction() {
      //ONLY LOGGED IN USER CAN CREATE
      if (!$this->_helper->requireUser()->isValid())
          return;

      $this->view->project_id = $project_id = $this->_getParam('project_id');
      //GET PROJECT ITEM
      $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

      $request = Zend_Controller_Front::getInstance()->getRequest();
       $params = $request->getParams();
       $type =  $this->_getParam('type',null);

      $this->view->tab_link = $tab_link = $type ? $type :  $this->_getParam('tab_link');

       if($tab_link == 'forms_assigned') {
           $this->view->paginator = $paginator =Engine_Api::_()->getDbTable('projectforms', 'sitepage')->projectForms($project_id);
       }
      if($tab_link == 'forms_submitted') {

          $this->view->paginator = $paginator =Engine_Api::_()->getDbTable('projectforms', 'sitepage')->projectFormsSubmitted($project_id);

      }


//      if (!Engine_Api::_() -> core() -> hasSubject('yndynamicform_form')) {
//          $this -> setNoRender();
//      }
//
//      // Get subject
//      $form = Engine_Api::_() -> core() -> getSubject();
//      $viewer = Engine_Api::_() -> user() -> getViewer();
//
//      $this -> view -> form = $form;
//      $this -> view -> viewer = $viewer;
//      $this -> view -> isModerator = $isModerator = $form -> isModerator($viewer);
  }



}