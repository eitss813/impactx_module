<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Project_Create_StepTwo extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->loadDefaultDecorators();

        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Create A Project")))
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfunding_project_new_step_two');

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);


        $this->addElement('MultiCheckbox', 'reason', array(
            'label' => 'Select why you want to create a project on ImpactNet?',
            'description' => 'Select all that apply',
            'multiOptions' => array(
                1 => 'To manage the Project with ImpactNet’s online tools',
                2 => 'To make the Project visible to others',
                3 => 'To get donations',
                4 => 'To find investors'
            ),
            'required' => true
        ));

        $this->reason->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Button', 'execute', array(
            'label' => 'Next',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));


        $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-one',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);
        $this->addElement('Button', 'previous', array(
            'label' => 'Previous',
            'onclick' => "window.location.href='".$backURL."'",
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

    }

}
