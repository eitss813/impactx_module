<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Classified_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');


    $this->addElement('Text', 'classified_page', array(
      'label' => 'Listings Per Page',
      'description' => 'How many classified listings will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.page', 10),
    ));
      $this->addElement('Radio', 'classified_allow_unauthorized', array(
          'label' => 'Make unauthorized classified searchable?',
          'description' => 'Do you want to make a unauthorized classifieds searchable? (If set to no, classifieds that are not authorized for the current user will not be displayed in the classified search results and widgets.)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.allow.unauthorized',0),
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
      ));
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
