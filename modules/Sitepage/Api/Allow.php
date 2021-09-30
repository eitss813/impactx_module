<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Allow.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Api_Allow extends Authorization_Model_DbTable_Allow {

  public function isAllowed($resource, $role, $action)
  {
    // Resource must be an instance of Core_Model_Item_Abstract
    if( !($resource instanceof Core_Model_Item_Abstract) )
    {
      // We have nothing to say about generic permissions
      return Authorization_Api_Core::LEVEL_IGNORE;
    }

    // Role must be an instance of Core_Model_Item_Abstract or a string relationship type
    if( !($role instanceof Core_Model_Item_Abstract) && !is_string($role) )
    {
      // Disallow access to unknown role types
      return Authorization_Api_Core::LEVEL_DISALLOW;
    }

    // Owner can do what they want with the resource
    if(  $role === 'owner' )
    {
      return Authorization_Api_Core::LEVEL_ALLOW;
    } 

    $allow = Engine_Api::_()->getDbtable('allow', 'authorization');
    
    // Now go over set permissions
    // @todo allow for custom types
    $rowset = $allow->_getAllowed($resource, $role, $action);

    if( is_null($rowset) || !($rowset instanceof Engine_Db_Table_Rowset) )
    {
      // No permissions have been defined for resource, disallow all
      return Authorization_Api_Core::LEVEL_DISALLOW;
    }

    // Index by type
    $perms = array();
    $permsByOrder = array();
    $items = array();
    foreach( $rowset as $row ) {
      if( empty($row->role_id) ) {
        $index = array_search($row->role, $allow->_relationships);
        if( $index === false ) { // Invalid type
          continue;
        }
        $perms[$row->role] = $row;
        $permsByOrder[$index] = $row->role;
      } else {
        $items[] = $row;
      }
    }

    // We we're passed a type role, how convenient
    if( is_string($role) ) {
      if( isset($perms[$role]) && is_object($perms[$role]) && $perms[$role]->value == Authorization_Api_Core::LEVEL_ALLOW ) {
        return Authorization_Api_Core::LEVEL_ALLOW;
      } else {
        return Authorization_Api_Core::LEVEL_DISALLOW;
      }
    }

    // Scan available types
    foreach( $permsByOrder as $perm => $type ) {
      $row = $perms[$type];
      $method = 'is_' . $type;
      if( !method_exists($allow, $method) ) continue;
      $applies = $allow->$method($resource, $role);
      if( $applies && $row->value == Authorization_Api_Core::LEVEL_ALLOW ) {
        return Authorization_Api_Core::LEVEL_ALLOW;
      }
    }

    // Ok, lets check the items then
    foreach( $items as $row ) {
      if( !Engine_Api::_()->hasItemType($row->role) ) {
        continue;
      }

      // Item itself is auth'ed
      if( is_object($role) && $role->getType() == $row->role && $role->getIdentity() == $row->role_id ) {
        return Authorization_Api_Core::LEVEL_ALLOW;
      }

      // Get item class
      $itemClass = Engine_Api::_()->getItemClass($row->role);

      // Member of
      if( method_exists($itemClass, 'membership') ) {
        $item = Engine_Api::_()->getItem($row->role, $row->role_id);
        if( $item && $item->membership()->isMember($role, null, $row->subgroup_id) ) {
          return Authorization_Api_Core::LEVEL_ALLOW;
        }
      }

      // List
      else if( method_exists($itemClass, 'has') ) {
        $item = Engine_Api::_()->getItem($row->role, $row->role_id);
        if( $item && $item->has($role) ) {
          return Authorization_Api_Core::LEVEL_ALLOW;
        }
      }
    }
    
    return Authorization_Api_Core::LEVEL_DISALLOW;
  }
}