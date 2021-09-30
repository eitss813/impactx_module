<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecredit
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Send.tpl 2017-02-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Admin_Send extends Engine_Form
{
  public function init()
  {
    $i = 100000;
    $this->setTitle('Send Messages')
      ->setDescription('You can send messages to site users as information.');

    $this->addElement('Select', 'type', array(
      'label' => 'Based On',
      'multiOptions' => array('0' => 'Profile Type', '1' => 'Member Level'),
      'onchange' => 'ontypeChange(this)'
    ));

    // Element: profile_type
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
    if( count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type' ) {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      $options_array = array();
      foreach( $options as $value )
        $options_array[] = $value['option_id'];
      if( count($options) > 1 ) {
        $options = $profileTypeField->getElementParams('user');
        unset($options['options']['order']);
        unset($options['options']['required']);
        $this->addElement('Select', 'profile_type', array_merge($options['options'], array(
        )));
      } else if( count($options) == 1 ) {
        $this->addElement('Hidden', 'profile_type', array(
          'order' => $i++,
          'value' => $options[0]->option_id
        ));
      }
    }


    $levelOptions = array();
    $levelOptions[0] = "All member levels";
    foreach( Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level ) {
      $levelOptions[$level->level_id] = $level->getTitle();
    }

    // Element: level_id
    $this->addElement('Select', 'member_level', array(
      'label' => 'Member Level',
      'multiOptions' => $levelOptions,
      'onchange' => 'onLevelChange(this)'
    ));



    $this->addElement('Select', 'member', array(
      'label' => 'Send To',
      'multiOptions' => array(
        1 => 'All Members',
        0 => 'Specific User'),
      'onchange' => 'onMemberChange(this)'
    ));
    $this->addElement('Text', 'user_name', array(
      'label' => 'Member Name',
      'description' => 'Start typing the name of the user.',
      'autocomplete' => 'off'));

    $this->addElement('Hidden', 'user_id', array(
      'order' => 200,
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    Engine_Form::addDefaultDecorators($this->user_id);

    $this->addElement('textarea', 'message', array(
      'label' => 'Message',
      'description' => '',
      'allowEmpty' => FALSE,
      'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
      'validators' => array(
        array('NotEmpty', true),
      ), 'filters' => array(
        'StripTags',
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Send Message',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick' => 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}
