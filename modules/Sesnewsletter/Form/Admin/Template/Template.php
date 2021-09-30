<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Template.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_Admin_Template_Template extends Engine_Form
{
  public function init()
  {
    $this
      ->setMethod('post')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save', 'controller' => 'content', 'module' => 'core'), 'admin_default', true))
      ->setAttrib('class', 'admin_layoutbox_menu_editinfo_form')
      ->setAttrib('id', 'admin_content_pageinfo')
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('HtmlTag', array('tag' => 'ul'))
      ->addDecorator('FormErrors', array('placement' => 'PREPEND', 'escape' => false))
      ->addDecorator('FormMessages', array('placement' => 'PREPEND', 'escape' => false))
      ->addDecorator('Form')
      ;

    $this->addElement('Text', 'displayname', array(
      'label' => 'Template Name <span>(for your reference only)</span>',
      'decorators' => array(
        array('ViewHelper'),
        array('Label', array('tag' => 'span', 'escape' => false)),
        array('HtmlTag', array('tag' => 'li')),
      ),
    ));

    $this->addElement('Hidden', 'template_id', array(
      'validators' => array(
        array('NotEmpty'),
        array('Int'),
      ),
    ));

  }
}
