<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Compose.php 9835 2012-11-29 00:35:00Z pamela $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitealbum_Form_Compose extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Compose Message');
    $this->setDescription('Create your new message with the form below. Your message can be addressed to up to 10 recipients.')
       ->setAttrib('id', 'messages_compose');;
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    
    // init to
    $this->addElement('Text', 'to',array(
        'label'=>'Send To',
        'autocomplete'=>'off',
'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                ),
        ));

    Engine_Form::addDefaultDecorators($this->to);

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'label' => 'Send To',
      'required' => true,
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