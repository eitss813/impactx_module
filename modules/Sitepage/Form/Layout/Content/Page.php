<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Page.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Layout_Content_Page extends Engine_Form {

  public function init() {

    $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save', 'controller' => 'layout', 'module' => 'sitepage'), 'default', true))
            ->setAttrib('class', 'pagelayout_layoutbox_menu_editinfo_form')
            ->setAttrib('id', 'pagelayout_content_pageinfo')
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('HtmlTag', array('tag' => 'ul'))
            ->addDecorator('FormErrors', array('placement' => 'PREPEND', 'escape' => false))
            ->addDecorator('FormMessages', array('placement' => 'PREPEND', 'escape' => false))
            ->addDecorator('Form')
    ;

    $this->addElement('Text', 'description', array(
        'label' => 'Page Description <span>(meta tag)</span>',
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
    ));

    $this->addElement('Text', 'keywords', array(
        'label' => 'Page Keywords <span>(meta tag)</span>',
        'allowEmpty' => false,
        'validators' => array(
        ),
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
    ));

    $this->addElement('Hidden', 'contentpage_id', array(
         'order' => 900,
        'validators' => array(
            array('NotEmpty'),
            array('Int'),
        ),
    ));
  }

}

?>