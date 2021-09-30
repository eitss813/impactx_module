<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: RemovePhoto.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Form_Edit_EditLocation extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Edit Location')
          ->setDescription("Edit your location below, then click 'Save Location' to save your location.")
          ->setMethod('post');

    $this->addElement('Text', 'location', array(
      'label' => 'Location',
      'description' => 'Eg: Fairview Park, Berkeley, CA',
      'filters' => array(
          'StripTags',
          new Engine_Filter_Censor(),
      )));
    $this->location->getDecorator('Description')->setOption('placement', 'append');

    include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';

    $this->addElement('Hidden', 'locationParams', array('order' => 800000));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save location',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');

    $button_group = $this->getDisplayGroup('buttons');
  }
}