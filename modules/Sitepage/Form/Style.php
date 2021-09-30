<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Style.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Style extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Edit Page Style')
            ->setDescription('Edit the CSS style of your page using the text area below, and then click "Save Style" to save changes.')
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'editstyle');

    // Element: style
    $this->addElement('Textarea', 'style', array(
        'label' => 'Custom Advanced Page Style',
        'description' => 'Add your own CSS code above to give your page a more personalized look.',
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
    ));
    $this->style->getDecorator('Description')->setOption('placement', 'APPEND');
    // Element: submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Style',
        'type' => 'submit',
    ));
  }

}

?>