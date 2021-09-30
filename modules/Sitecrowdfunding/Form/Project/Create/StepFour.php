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
class Sitecrowdfunding_Form_Project_Create_StepFour extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->loadDefaultDecorators();
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Associated Organizations")))
            ->setAttrib('id', 'sitecrowdfunding_project_new_step_four_dummy')
            ->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Radio', 'is_associated_org', array(
            'label' => 'Is the Project associated with one or more organizations?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'required' => true,
            'allowEmpty' => false,
            'value' => 1,
            'onchange' => 'checkIsAssociated(this.value);',
        ));

        $this->is_associated_org->getDecorator('Description')->setOption('placement', 'append');

    }

}
