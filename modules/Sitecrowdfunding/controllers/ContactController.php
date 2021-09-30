<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TopicController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_ContactController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function contactInfoAction() {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_contactinfo';
        //IF THERE IS NO PROJECT.
        if (empty($project)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        if (!$project->isOpen()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($project);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $address = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_address');
        $phone = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_phone');
        $email = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_email');

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_ContactInfo();
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        $form->populate(array(
            'contact_address' => $address,
            'contact_phone' => $phone,
            'contact_email' => $email
        ));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            $tableOtherinfo->update(array(
                'contact_address' => $values['contact_address'],
                'contact_phone' => $values['contact_phone'],
                'contact_email' => $values['contact_email']
            ), array('project_id = ?' => $project_id));

            return $this->_helper->redirector->gotoRoute(array('controller' => 'contact', 'action' => 'contact-info', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);

        }

    }
}