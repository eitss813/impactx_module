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
class Sitecrowdfunding_Form_Project_Funding extends Engine_Form {

    public $_error = array();

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
        $hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
        $isAllowedLifeTimeProject = false;
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($hasPackageEnable) {
            $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $package_id);
            $isAllowedLifeTimeProject = $package->lifetime;
        } else {
            $this->setTitle("Edit funding details");
            $isAllowedLifeTimeProject = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "lifetime");
        }

        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if($paymentMethod == 'escrow'){
            $isAllowedLifeTimeProject = false;
        }

        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Edit funding related details in this project using the form below.")))
            //->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitecrowdfundings_editfunding')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfundings_editfunding_form');
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);


        $this->addElement('Radio', 'is_fund_raisable', array(
            'label' => 'Do you seek funding for the Project through ImpactNet?',
            'multiOptions' => array("1"=>"Yes", "0"=>"No"),
            'required' => true,
            'allowEmpty' => false,
            'value' => 1,
            'onchange' => 'checkIsFundable(this.value);'
        ));

        $this->is_fund_raisable->getDecorator('Description')->setOption('placement', 'append');

        /*if ($isAllowedLifeTimeProject) {
            $this->addElement('Radio', 'lifetime', array(
                'label' => 'Project Duration',
                'multiOptions' => array(
                    1 => 'Upto 5 years',
                    0 => '1-90 days',
                ),
                'onclick' => "initializeCalendar(this.value,'" . date('Y-m-d') . "')",
                'required' => false,
                'allowEmpty' => true,
                'value' => 0,
            ));
        }*/


        $this->addElement('CalendarDateTimeCustom', 'starttime', array(
            'label' => 'Funding Start Date',
            'allowEmpty' => false,
            'required' => true
        ));
        $this->addElement('CalendarDateTimeCustom', 'endtime', array(
            'label' => 'Funding End Date',
            'allowEmpty' => false,
            'required' => false,

        ));
        $viewer = Engine_Api::_()->user()->getViewer();
        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->addElement('Text', 'goal_amount', array(
            'label' => 'How much funding do you seek through IN (in addition to funds you may have already secured elsewhere)?',
            'description' => 'Please enter amount in USD($).',
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

        $this->addElement('Text', 'invested_amount', array(
            'label' => 'How much funding has the project team already invested?',
            //'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Funding Goal (%s)'), $currencyName),
            'description' => 'Please enter amount in USD($).',
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

        $this->addElement('Text', 'payment_action_label', array(
            'label' => "Display name on the Call to Action Button. (e.g. Donate, Contribute)",
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $this->addElement('Radio', 'payment_is_tax_deductible', array(
            'label' => 'Are donations to this project is tax deductible ?',
            'multiOptions' => array("1"=>"Yes", "0"=>"No"),
            'required' => true,
            'allowEmpty' => false,
            'value' => 0,
            'onchange' => 'onChangeIsTaxDeductible(this.value);'
        ));

        $this->addElement('Text', 'payment_tax_deductible_label', array(
            'label' => "Text display to encourge funding ?",
            'allowEmpty' => false,
            'required' => false
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
//            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), "sitecrowdfunding_general", true),
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
