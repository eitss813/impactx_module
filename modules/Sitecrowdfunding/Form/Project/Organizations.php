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
class Sitecrowdfunding_Form_Project_Organization extends Engine_Form {

    public $_error = array();
//    protected $_defaultProfileId;
//    protected $_parentTypeItem;
//
//    public function getDefaultProfileId() {
//        return $this->_defaultProfileId;
//    }
//
//    public function setDefaultProfileId($default_profile_id) {
//        $this->_defaultProfileId = $default_profile_id;
//        return $this;
//    }
//
//    public function setParentTypeItem($item) {
//        $this->_parentTypeItem = $item;
//    }
//
//    public function getParentTypeItem() {
//        return $this->_parentTypeItem;
//    }

    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = $user->level_id;
        $viewer_id = $user->getIdentity();
        $this->loadDefaultDecorators();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //PACKAGE BASED CHECKS

        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Add New Milestone using below form for this project.")))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitecrowdfundings_milestone_create')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfundings_milestone_create_form');
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);


        $this->addElement('Text', 'title', array(
            'label' => "Milestone Title",
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));


        $this->addElement('textarea', 'description', array(
            'label' => "Milestone Description",
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
            'filters' => array(
                'StripTags',
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));



        $this->addElement('CalendarDateTime', 'starttime', array(
            'label' => 'Milestone Start Date',
            'allowEmpty' => true,

            //'value' => date('d/m/Y'),
        ));
        $this->addElement('CalendarDateTime', 'endtime', array(
            'label' => 'Milestone End Date',
            'allowEmpty' => true,
            //'value' => date('d/m/Y'),
        ));

        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array("yettostart" => "Yet to start", "inprogress" => "In Progress", 'completed'=> 'Completed'),
            'allowEmpty'=> false,
            'required' => true
        ));


        $this->addElement('Button', 'execute', array(
            'label' => 'Create',
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
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage-milestone', 'controller'=>'milestone', 'project_id'=> $project_id), "sitecrowdfunding_extended", true),
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
