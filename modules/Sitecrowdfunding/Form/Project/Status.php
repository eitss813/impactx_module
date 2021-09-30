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
class Sitecrowdfunding_Form_Project_Status extends Engine_Form {

    public $_error = array();
    protected $_defaultProfileId;
    protected $_parentTypeItem;

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function setParentTypeItem($item) {
        $this->_parentTypeItem = $item;
    }

    public function getParentTypeItem() {
        return $this->_parentTypeItem;
    }

    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = $user->level_id;
        $viewer_id = $user->getIdentity();
        $this->loadDefaultDecorators();
        //PACKAGE ID
        $package_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
        if ($this->_item) {
            $package_id = $this->_item->package_id;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //PACKAGE BASED CHECKS

        $this->setTitle("Project status");
        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Please select the status of project and submit for approval to admin to getting published.")))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitecrowdfundings_status')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfundings_create_status');
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);

        $this->addElement('Radio', 'is_fund_raisable', array(
            'label' => 'Fund raising',
            'multiOptions' => array(
                1 => 'Enable',
                0 => 'Disable',
            ),
            'description' => 'Before enabling Fund raising, Please fill out funding information and setup payment gateway.',
            'value' => 1,
        ));
        $this->is_fund_raisable->getDecorator('Description')->setOption('placement', 'append');


        $this->addElement('Select', 'state', array(
            'label' => 'Status',
            'multiOptions' => array("submitted" => "Submit for approval", "draft" => "Saved As Draft",),
            'description' => 'If this entry is submit for approval, it cannot be switched back to draft mode.',
        ));
        $this->state->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Button', 'execute', array(
            'label' => 'Submit',
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
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), "sitecrowdfunding_general", true),
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
