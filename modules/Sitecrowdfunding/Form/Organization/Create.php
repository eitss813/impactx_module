<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SubEdit.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Organization_Create extends Engine_Form {
    public $_error = array();
    public function init() {
        $this->loadDefaultDecorators();
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Add Organization Name")));
        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Add New Organization of this project using below form.")));

        $this->addElement('Radio', 'is_internal', array(
            'label' => 'Is organization listed in ImpactNet?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'required' => true,
            'allowEmpty' => false,
            'value' => 1,
            'onchange' => 'checkIsInternal(this.value);'
        ));

        $this->addElement('Text', 'title', array(
            'label' => 'Organization Name',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'allowEmpty'=> false,
            'required' => true
        ));

        $this->addElement('Textarea', 'description', array(
            'label' => 'Description',
            'rows' => 2,
            'cols' => 120,
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
        ));
        $user = Engine_Api::_()->user()->getViewer();

        $viewer_id = $user->getIdentity();

        // adding organization multi-select
        $organizations = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getViewableOrganizationNames($project_id);

        $this->addElement('Select', 'organization_id', array(
            'label' => 'Organization Name',
            'allowEmpty' => false,
            'required' => true,
            'multiOptions' => $organizations,
        ));
        // adding organization multi-select


        $this->addElement('Select', 'organization_type', array(
            'allowEmpty' => false,
            'required' => true,
            'label' => 'Organization Role',
            'multiOptions' => array(
                "funder" => "Funder",
                "sponsor" => "Sponsor",
                'parent'=>'Parent',
                'partner'=> 'Partner',
                'sister'=> 'Sister',
                'others'=> 'Others'),
        ));

        $this->addElement('Textarea', 'others', array(
            'allowEmpty' => true,
            'required' => false,
            'label' => 'Please mention -> Others',
            'rows' => 1,
            'cols' => 120,
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'link', array(
            'label' => 'Organization Link',
            'allowEmpty' => true,
            'required' => false,
        ));

        $this->addElement('File', 'photo', array(
            'label' => 'Organization Logo',
            'allowEmpty' => true,
            'required' => false,
        ));
        $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');


        $this->addElement('Button', 'execute1', array(
            'label' => 'Save Changes',
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
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'editorganizations', 'project_id'=> $project_id), "sitecrowdfunding_organizationspecific", true),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array(
            'execute1',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));

    }

}
