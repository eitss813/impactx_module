<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {

    parent::init();

    $this
            ->setTitle('Member Level Settings')
            ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    if (!$this->isPublic()) {
    	
    // Privacy
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1))
      $ownerTitle = "Page Admins";
    else
      $ownerTitle="Just Me";
      
      $this->addElement('MultiCheckbox', 'auth_tag', array(
          'label' => 'Album Tag Options',
          'description' => 'Your users can choose from any of the options checked below when they decide who can post tags on their album photos. If you do not check any options, everyone will be allowed to post tags on album photos.',
          'multiOptions' => array(
              'registered' => 'All Registered Members',
              'owner_network' => 'Friends and Networks',
              'owner_member_member' => 'Friends of Friends',
              'owner_member' => 'Friends Only',
              'owner' => $ownerTitle
          ),
          'value' => array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));
    }
  }

}

?>