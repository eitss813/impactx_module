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

class Sitepage_Form_AddPartners extends Engine_Form {

    public
    function init()
    {

        $this->setTitle('Add sister pages to this pages');
        $this->setDescription('Select the pages you want to add as sister in this pages')
            ->setAttrib('id', 'messages_compose');
        $Button = 'Add';

        // init to
        $this->addElement('Text', 'page_ids', array(
            'description' => 'Start typing the name...',
            'autocomplete' => 'off',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
            ),
        ));
        Engine_Form::addDefaultDecorators($this->page_ids);

        // Init to Values
        $this->addElement('Hidden', 'toValues', array(
            'order' => 500,
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