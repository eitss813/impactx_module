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
class Sitecrowdfunding_Form_Admin_Notes extends Engine_Form {

    public $_error = array();

    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = $user->level_id;
        $viewer_id = $user->getIdentity();
        $this->loadDefaultDecorators();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //PACKAGE BASED CHECKS
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Reject this project")));
        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Please enter a notes for project admins.")))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitecrowdfundings_admin_notes')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfundings_admin_notes_form');
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);


        $this->addElement('textarea', 'description', array(
            'required' => false,
            'allowEmpty' => true,
            'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
            'filters' => array(
                'StripTags',
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));


        $this->addElement('Button', 'execute', array(
            'label' => 'Reject',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

    }

}
