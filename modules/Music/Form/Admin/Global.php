<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 9909 2013-02-14 05:49:17Z matthew $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    
    // Get The Value
    $values = Engine_Api::_()->getApi('settings', 'core')->music;
    
    
    $this->addElement('Text', 'playlistsPerPage', array(
      'label' => 'Playlists Per Page',
      'description' => 'How many playlists will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsPerPage', $values['playlistsperpage']),
    ));    

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

  public function saveValues()
  {
    $values = $this->getValues();
    if (!is_numeric($values['playlistsPerPage'])
           || 0  >= $values['playlistsPerPage']
           || 999 < $values['playlistsPerPage'])
      $values['playlistsPerPage'] = 10;
    Engine_Api::_()->getApi('settings', 'core')
        ->setSetting('music.playlistsperpage', $values['playlistsPerPage']);

  }
}
