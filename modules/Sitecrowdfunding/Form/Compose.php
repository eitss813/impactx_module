<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Compose.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Compose extends Engine_Form {

    public function init() {
        $this->setTitle('Compose Message');
        $this->setDescription('Create your new message with the form below. Your message can be addressed to up to 10 recipients.')
                ->setAttrib('id', 'messages_compose'); 
        // init to
        $this->addElement('Text', 'to', array(
            'label' => 'Send To',
            'placeholder' => 'Start typing backer name...',
            'autocomplete' => 'off'));

        $this->addElement('Hidden', 'user_ids', array());

        Engine_Form::addDefaultDecorators($this->to); 

        // init title
        $this->addElement('Text', 'title', array(
            'label' => 'Subject',
            'order' => 3,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
            ),
        ));

        // init body - plain text
        $this->addElement('Textarea', 'body', array(
            'label' => 'Message',
            'order' => 4,
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        // init submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Send Message',
            'order' => 5,
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
