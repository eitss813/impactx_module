<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Compose.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Compose extends Engine_Form {

    public function init() {
        $this->setTitle('Compose Message');
        $this->setDescription('Create your new message with the form below. Your message can be addressed to up to 10 recipients.')
                ->setAttrib('id', 'messages_compose');
        ;
        $user_level = Engine_Api::_()->user()->getViewer()->level_id;

        // init to
        $this->addElement('Text', 'to', array(
            'label' => 'Send To',
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
            'autocomplete' => 'off'));

        Engine_Form::addDefaultDecorators($this->to);

        // Init to Values
        $this->addElement('Hidden', 'toValues', array(
            'label' => 'Send To',
            'required' => true,
            'allowEmpty' => false,
            'order' => 20002,
            'validators' => array(
                'NotEmpty'
            ),
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);

        // init title
        $this->addElement('Text', 'title', array(
            'label' => 'Subject',
            'order' => 3,
            'filters' => array(
                'StripTags',
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
                'StripTags',
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
