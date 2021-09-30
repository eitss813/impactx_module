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
class Sitecrowdfunding_Form_ProjectNew_StepOne extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->loadDefaultDecorators();

        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Funding")))
            //->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            //->setAttrib('name', 'sitecrowdfunding_funding')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('enctype', 'multipart/form-data')
        ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-new', 'action' => 'step-one'), 'sitecrowdfunding_extended', true))
        ->setMethod("POST");
        $this->setAttrib('id', 'sitecrowdfunding_project_new_step_one');
        //$this->setAttrib('class', 'global_form sitecrowdfunding_project_new_steps');

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);


        $this->addElement('Radio', 'is_fund_raisable', array(
            'label' => 'Do you seek funding for the Project through ImpactNet',
            'multiOptions' => array(
                1 => 'Enable',
                0 => 'Disable',
            ),
            'value' => 1,
        ));

        $this->is_fund_raisable->getDecorator('Description')->setOption('placement', 'append');


        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->addElement('Text', 'goal_amount', array(
            'label' => 'How much funding do you seek through IN (in addition to funds you may have already secured elsewhere)',
            //'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Funding Goal (%s)'), $currencyName),
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
