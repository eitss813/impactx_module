<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id:Composerl.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagealbum_Plugin_Composer extends Core_Plugin_Abstract {

  public function onAttachSitepagephoto($data) {

    if (!is_array($data) || empty($data['photo_id'])) {
      return;
    }

    $photo = Engine_Api::_()->getItem('sitepage_photo', $data['photo_id']);

    // make the image public
    // CREATE AUTH STUFF HERE
    /*
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
      foreach( $roles as $i=>$role )
      {
      $auth->setAllowed($photo, $role, 'view', ($i <= $roles));
      $auth->setAllowed($photo, $role, 'comment', ($i <= $roles));
      } */

    if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) {
      return;
    }
    
     if (!empty($data['actionBody']) && empty($photo->description)) {
      $photo->description = $data['actionBody'];
      $photo->save();
    }

    return $photo;
  }

}