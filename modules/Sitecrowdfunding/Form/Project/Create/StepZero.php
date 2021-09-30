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
class Sitecrowdfunding_Form_Project_Create_StepZero extends Engine_Form {

    public $_error = array();

    public function init() {


        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Create A Project")))
            ->setAttrib('id', 'sitecrowdfunding_project_new_step_zero');

        $page_id = $this->_attribs['page_id'];
        $initiative_id = $this->_attribs['initiative_id'];
        if(empty($initiative_id)){
            $initiative_id = null;
        }

        /**** organisation drop down ****/
        $pagesColumnArray = array('page_id', 'title', 'page_url');
        $pages =  Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('recent', array(), null, null, $pagesColumnArray);
        $multiPageOptions = array();
        $multiPageOptions[null]='';
        foreach( $pages as  $page ){
            $multiPageOptions[$page->page_id] = $page->title;
        }
        $this->addElement('Select', 'page_id', array(
            'label' => 'Organisation',
            'description' => 'Please select the organization this project is associated with.',
            'multiOptions' => $multiPageOptions,
            'required' => true,
            'onchange' => 'initiativeOptions(this.value,null);',
            'value' => $page_id
        ));

        /**** initiative drop down ****/
        $multiOptions = array('' => '');
        if(!empty($page_id)){
            $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($page_id);
            if (count($initiatives) > 0) {
                $multiOptions = array('' => '');
                foreach ($initiatives as $initiative) {
                    $multiOptions[$initiative['initiative_id']] = $initiative['title'];
                }
            }
        }

        $this->addElement('Select', 'initiative_id', array(
            'label' => 'Initiative',
            'description' => 'Please select the Initiative this project is part of',
            'multiOptions' => $multiOptions,
            'value' => $initiative_id
        ));

        $this->addElement('Button', 'execute', array(
            'label' => 'Next',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

    }

}
