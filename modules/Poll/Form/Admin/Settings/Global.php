<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Poll_Form_Admin_Settings_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'perpage', array(
      'label' => 'Polls Per Page',
      'description' => 'How many polls will be shown per page? (Enter a number between 1 and 999)',
      'validators' => array(
        array('Int', true),
        array('LessThan', true, 999),
        new Engine_Validate_AtLeast(1),
      ),
      'value' => 10,
    ));

    $this->addElement('Text', 'maxoptions', array(
      'label' => 'Maximum Options',
      'description' => 'How many possible poll answers do you want to permit?',
      'value' => 15,
      'validators' => array(
        array('Int', true),
        array('LessThan', true, 100),
        new Engine_Validate_AtLeast(2),
      ),
    ));

    $this->addElement('Radio', 'canchangevote', array(
      'label' => 'Change Vote?',
      'description' => 'Do you want to permit your members to change their vote?',
      'multiOptions' => array(
        1 => 'Yes, members can change their vote.',
        0 => 'No, members cannot change their vote.',
      ),
      'value' => false,
    ));
      $this->addElement('Radio', 'poll_allow_unauthorized', array(
          'label' => 'Make unauthorized poll searchable?',
          'description' => 'Do you want to make a unauthorized polls searchable? (If set to no, polls that are not authorized for the current user will not be displayed in the poll search results and widgets.)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.allow.unauthorized', 0),
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
