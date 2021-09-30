<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UserFriendshipAjax.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitepagemember_View_Helper_UserFriendshipAjax extends Zend_View_Helper_Abstract {

  public function userFriendshipAjax($user, $viewer = null) {
  
    if( null === $viewer ) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if( !$viewer || !$viewer->getIdentity() || $user->isSelf($viewer) ) {
      return '';
    }

    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

    // Get data
    if( !$direction ) {
       $row = $user->membership()->getRow($viewer);
    }
    else $row = $viewer->membership()->getRow($user);

    // Render

    // Check if friendship is allowed in the network
    $eligible =  (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if($eligible == 0){
      return '';
    }
   
    // check admin level setting if you can befriend people in your network
    else if( $eligible == 1 ) {

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
        ->from($networkMembershipName, 'user_id')
        ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
        ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
        ->where("`{$networkMembershipName}_2`.user_id = ?", $user->getIdentity())
        ;

      $data = $select->query()->fetch();

      if(empty($data)){
        return '';
      }
    }
    
    if( !$direction ) {
      // one-way mode
      if( null === $row ) {
        return "<a class='buttonlink' id='addfriend_".$user->user_id."' href='javascript:void(0);' onclick='addfriend($(this), ".$user->user_id .")' style='background-image: url(".$this->view->layout()->staticBaseUrl."application/modules/User/externals/images/friends/add.png);'>" . $this->view->translate('Follow') . "</a>";
      } 
    } 
    else {
      // two-way mode
      if( null === $row ) {
        return "<a class='buttonlink' id='addfriend_".$user->user_id."' href='javascript:void(0);' onclick='addfriend($(this),".$user->user_id .")' style='background-image: url(".$this->view->layout()->staticBaseUrl."application/modules/User/externals/images/friends/add.png);'>" .$this->view->translate('Add Friend') . "</a>";
      }
    }
    return '';
  }
}