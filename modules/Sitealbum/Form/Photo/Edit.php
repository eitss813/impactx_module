<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Photo_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Edit Photo')
      ->setMethod('POST')
      ->setAction($_SERVER['REQUEST_URI'])
      ->setAttrib('class', 'global_form_popup')
      ;
      
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Caption',
      'rows' => 2,
      'cols' => 120,
      'filters' => array(
                            'StripTags',
                             new Engine_Filter_Censor(),
                         ),
    ));

    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'execute',
      'cancel'
    ), 'buttons');
  }
}