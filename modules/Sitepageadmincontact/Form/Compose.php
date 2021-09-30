<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageadmincontact
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Compose.php 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageadmincontact_Form_Compose extends Engine_Form {

  public function init() {

    $this->setTitle('Compose Message');
    $this->setDescription('Create your new message with the form below.')
            ->setAttrib('id', 'messages_compose');

    // init title
    $this->addElement('Text', 'title', array(
        'label' => 'Subject',
        'order' => 1,
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));

    // init body
    $this->addElement('Textarea', 'body', array(
        'label' => 'Message',
        'order' => 2,
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
        'label' => 'Send Messages',
        'order' => 3,
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>