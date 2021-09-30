<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InviteMembers.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitecrowdfunding_Form_Project_Create_StepEightAdmin extends Engine_Form
{

    public function init()
    {

        $this->setTitle('Add Project Administrator');
        $this->setDescription('Select the people you want to add as admin into this project.')
            ->setAttrib('id', 'messages_compose');
        $Button = 'Add';

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id');

        // init to
        $this->addElement('Text', 'user_ids', array(
            'description' => 'Start typing the name of the member...',
            'autocomplete' => 'off',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
            ),
        ));
        Engine_Form::addDefaultDecorators($this->user_ids);

        // Init to Values
        $this->addElement('Hidden', 'toValues', array(
            'order' => '7',
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);

        $this->addElement('Button', 'submit', array(
            'label' => $Button,
            'ignore' => true,
            'order' => '8',
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'order' => '9',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            ),
        ));
    }
}