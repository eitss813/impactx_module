<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Compose.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Form_Compose extends Engine_Form {

  public function init() {
  
    $this->setTitle('Compose Message');
    $this->setDescription('Create your new message with the form below. Your message can be addressed to up to 10 recipients.')
       ->setAttrib('id', 'messages_compose');

    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

    // init to
    $this->addElement('Text', 'to',array(
        'label'=>'Send To',
        'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        'autocomplete'=>'off'));
    Engine_Form::addDefaultDecorators($this->to);

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'label' => 'Send To',
      'required' => false,
      'allowEmpty' => false,
      'order' => 200,
      'validators' => array(
        'NotEmpty'
      ),
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    Engine_Form::addDefaultDecorators($this->toValues);

           // Element : restriction
    $this->addElement('Radio', 'coupon_mail', array(
			'label' => 'Message Who?',
			'multiOptions' => array(
				'0' => 'All Page Members',
                                '2' => 'All Page Members of selected role',
				'1' => 'Particular Members (You can enter the members using the auto-suggest below.)',
			),
			'value' => 0,
			'onclick' => 'showprofileOption(this.value)',
    ));
    
    $this->addElement('Select', 'roles_id', array(
                                    'label' => 'ROLE',
      'description' => 'Select the role from below :',

                            ));

    // init to
    $this->addElement('Text', 'user_ids',array(
        'label'=>'Members to Message',
        'description' => 'Start typing the name of the member...',
        'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        'autocomplete'=>'off'));
    Engine_Form::addDefaultDecorators($this->user_ids);

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'label' => '',
      'order' => 600,
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    Engine_Form::addDefaultDecorators($this->toValues);

    // init title
    $this->addElement('Text', 'title', array(
      'label' => 'Subject',
      'order' => 6,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
    ));
    
    // init body - editor
    $editor = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $user_level, 'editor');
    
    if ( $editor == 'editor' ) {
      $this->addElement('TinyMce', 'body', array(
        'disableLoadDefaultDecorators' => true,
        'order' => 7,
        'required' => false,
        'editorOptions' => array(
          'bbcode' => true,
          'html' => true,
        ),
        'allowEmpty' => false,
        'decorators' => array(
            'ViewHelper',
            'Label',
            array('HtmlTag', array('style' => 'display: block;'))),
        'filters' => array(
          new Engine_Filter_HtmlSpecialChars(),
          new Engine_Filter_Censor(),
          new Engine_Filter_EnableLinks(),
        ),
      )); 
    } else {
      // init body - plain text
      $this->addElement('Textarea', 'body', array(
        'label' => 'Message',
        'order' => 7,
        'required' => false,
        'allowEmpty' => false,
        'filters' => array(
          'StripTags',
          new Engine_Filter_HtmlSpecialChars(),
          new Engine_Filter_Censor(),
          new Engine_Filter_EnableLinks(),
        ),
      ));
    }
    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Send Message',
      'order' => 8,
      'type' => 'submit',
      'ignore' => true
    ));
  }
}