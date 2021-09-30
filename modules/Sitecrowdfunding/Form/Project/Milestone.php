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
class Sitecrowdfunding_Form_Project_Milestone extends Engine_Form {

    public $_error = array();

    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $this->loadDefaultDecorators();
        //PACKAGE BASED CHECKS

        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Add New Milestone using below form for this project.")))
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

        $this->addElement('textarea', 'question', array(
            'label' => "How will you determine if this milestone is met",
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

        $this->addElement('File', 'photo', array(
            'label' => 'Milestone Logo',
            'allowEmpty' => true,
            'required' => false,
        ));
        $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');


        $this->addElement('CalendarDateTimeCustom', 'starttime', array(
            'label' => 'Milestone Start Date',
            'allowEmpty' => false,
            'required' => true
        ));

        $this->addElement('CalendarDateTimeCustom', 'endtime', array(
            'label' => 'Milestone End Date',
            'allowEmpty' => true,
            'required' => false
        ));

        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array("yettostart" => "Yet to start", "inprogress" => "In Progress", 'completed'=> 'Completed'),
            'allowEmpty'=> false,
            'required' => true
        ));


        $this->addElement('Button', 'execute', array(
            'label' => 'Save',
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
