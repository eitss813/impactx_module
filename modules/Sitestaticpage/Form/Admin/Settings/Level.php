<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Level.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitestaticpage_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    if( !$this->isPublic() ) {

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Static pages?',
        'description' => 'Do you want to let members of this level comment on static pages?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all static pages, including private ones.',
          1 => 'Yes, allow members to comment on static pages.',
          0 => 'No, do not allow members to comment on static pages.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      
      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of Comment on Static pages?',
        'description' => 'Do you want to let members of this level edit comments on static pages?',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all comments on static pages.',
          1 => 'Yes, allow members to edit all their comments on static pages.',
          0 => 'No, do not allow comments to be edited.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }
    }
  }
}