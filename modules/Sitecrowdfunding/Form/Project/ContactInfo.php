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
class Sitecrowdfunding_Form_Project_ContactInfo extends Engine_Form {

    public $_error = array();


    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = $user->level_id;
        $viewer_id = $user->getIdentity();
        $this->loadDefaultDecorators();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //PACKAGE BASED CHECKS

        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Edit contact info of this project.")))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitecrowdfundings_contact_info_create')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfundings_contact_info_create_form');
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);

        $this->addElement('textarea', 'contact_address', array(
            'label' => "Address",
            'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'height:120px;'),
            'filters' => array(
                'StripTags',
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'contact_email', array(
            'label' => 'Email Address',
            'validators' => array(
                array('EmailAddress', true)
            ),
        ));

        $this->addElement('Text', 'contact_phone', array(
            'label' => 'Phone',
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));


        $this->addElement('Button', 'execute', array(
            'label' => 'Save changes',
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
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage-outcome', 'controller'=>'outcome', 'project_id'=> $project_id), "sitecrowdfunding_extended", true),
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
