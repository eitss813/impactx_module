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
class Sitecrowdfunding_Form_Project_Create_StepSixPhoto extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->loadDefaultDecorators();
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Upload photo")))
            ->getDecorator('Description')->setOption('escape', false);

        $this->addElement('File', 'photo', array(
            'label' => 'Select photo',
            'allowEmpty' => false,
            'required' => true,
        ));
        $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');

        $this->addElement('Button', 'execute', array(
            'label' => 'Upload Photo',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => 'javascript:void(0);',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper',
            ),
        ));



        $this->addDisplayGroup(array(
            'execute',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));



    }

}
