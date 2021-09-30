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
class Sitecrowdfunding_Form_Project_ExternalFunding extends Engine_Form {

    public $_error = array();


    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = $user->level_id;
        $viewer_id = $user->getIdentity();
        $this->loadDefaultDecorators();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //PACKAGE BASED CHECKS

        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Add External funding using below form for this project.")))
            //->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitecrowdfundings_externalfunding_create')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfundings_externalfunding_create_form');
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);

        $this->addElement('Radio', 'resource_type', array(
            'label' => 'Funding source',
            'multiOptions' => array(
                'organization' => 'Organization',
                'member' => 'Member',
            ),
            'required' => true,
            'allowEmpty' => false,
            'value' => 'organization',
            'onchange' => 'checkingResourceType(this.value);'
        ));

        $this->addElement('Radio', 'is_organisation_listed', array(
            'label' => 'Is the organisation is listed in impactx ?',
            'multiOptions' => array(
                'yes' => 'Yes',
                'no' => 'No',
            ),
            'required' => true,
            'allowEmpty' => false,
            'value' => 'yes',
            'onchange' => 'checkIsOrganisationListed(this.value);'
        ));

        $externalorganizations = Engine_Api::_()->getDbtable('organizations','sitecrowdfunding')->fetchOrganizationNamesByProjectId($project_id);
        $internalorganizations = Engine_Api::_()->getDbtable('pages','sitecrowdfunding')->getPagesIdAndName($project_id);

        $this->addElement('Select', 'organization_id', array(
            'label' => 'Organizations',
            'allowEmpty' => true,
            'required' => false,
            'multiOptions' => array('Listed Organizations' => $internalorganizations, 'Unlisted Organizations' => $externalorganizations) ,
        ));

        $this->addElement('Text', 'organization_name', array(
            'label' => 'Organization Name',
            'allowEmpty' => true,
            'required' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Cancel', 'org_nav', array(
            'label' => 'Add organization',
            'link' => true,
            'prependText' => '',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'editorganizations', 'project_id'=> $project_id), "sitecrowdfunding_organizationspecific", true),
            'decorators' => array(
                'ViewHelper',
            ),
            'class' =>'icon seaocore_icon_add',
            'target' => "_blank"
        ));

        $membersData = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listAllJoinedMembers($project_id);
        $members = array();
        foreach ($membersData as $member){
            if($member['user_id'] && Engine_Api::_()->getItem('user', $member['user_id'])->getTitle()){
                $members[$member['user_id']] =Engine_Api::_()->getItem('user', $member['user_id'])->getTitle();;
            }
        }

        $this->addElement('Select', 'member_id', array(
            'label' => 'Members',
            'allowEmpty' => true,
            'required' => false,
            'multiOptions' => $members,
        ));

        $this->addElement('Cancel', 'mem_nav', array(
            'label' => 'Add member',
            'link' => true,
            'prependText' => '',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'list-members', 'controller' => 'member', 'project_id'=> $project_id), "sitecrowdfunding_extended", true),
            'decorators' => array(
                'ViewHelper',
            ),
            'class' =>'icon seaocore_icon_add',
            'target' => "_blank"
        ));

        $this->addElement('CalendarDateTime', 'funding_date', array(
            'label' => 'Funding Date',
            'allowEmpty' => false,
            'required' => true
            //'value' => date('d/m/Y'),
        ));

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $this->addElement('Text', 'funding_amount', array(
            'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Funding Amount (%s)'), $currencyName),
            'attribs' => array('class' => 'se_quick_advanced'),
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Float', false),
                array('GreaterThan', false, array(0))
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $this->addElement('textarea', 'notes', array(
            'label' => "Description",
            'required' => false,
            'allowEmpty' => true,
            'attribs' => array('rows' => 5),
            'filters' => array(
                'StripTags',
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
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
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
//            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'external-funding', 'controller'=>'funding', 'project_id'=> $project_id), "sitecrowdfunding_extended", true),
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
