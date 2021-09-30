<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {

    //CHECK IF ADMIN
    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      return;
    }

    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    $sitememberIsUserEnabled = Zend_Registry::isRegistered('sitememberIsUserEnabled') ? Zend_Registry::get('sitememberIsUserEnabled') : null;
    
		if (!empty($sitememberIsUserEnabled) && ($module == "user")) {
			if ($controller == "index" && $action == "browse") {
			$request->setModuleName('sitemember');
			$request->setControllerName('location');
			$request->setActionName('userby-locations');
      $request->setParam('module', 'sitemember');
      $request->setParam('controller', 'location');
      $request->setParam('action', 'userby-locations');
			}
		}
	}

  public function onUserCreateAfter($event) {

    $item = $event->getPayload();
    $front = Zend_Controller_Front::getInstance();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    if ($controller == 'signup' && $action == 'index') {

      if (empty($_SESSION["User_Plugin_Signup_Account"]["data"]["profile_type"])) {
        $_SESSION["User_Plugin_Signup_Account"]["data"]["profile_type"] = 1;
      }

      if (!empty($_SESSION["User_Plugin_Signup_Account"]["data"]["profile_type"])) {
        $profile_type_id = $_SESSION["User_Plugin_Signup_Account"]["data"]["profile_type"];

        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_user_fields_search\'')->fetch();
        if (!empty($table_exist)) {
          $column_exist = $db->query('SHOW COLUMNS FROM engine4_user_fields_search LIKE \'location\'')->fetch();
        }

        $option_id = Engine_Api::_()->getDbtable('profilemaps', 'sitemember')->getOptionIds(array('option_id' => $profile_type_id, 'fetchColumn' => array('profile_type')));
        $signupTable = Engine_Api::_()->getDbtable('signup', 'user');
        $fieldClass = $signupTable->select()
                                  ->from($signupTable->info('name'), 'class')
                                  ->where('class LIKE ?', '%Plugin_Signup_Fields%')
                                  ->where('enable = ?', 1)
                                  ->query()
                                  ->fetchColumn();
        if (!empty($option_id) && isset($_SESSION[$fieldClass]["data"][$option_id])) {
          $option_id_location = $_SESSION[$fieldClass]["data"][$option_id];
          if (!empty($option_id_location)) {
            if ($item instanceof User_Model_User) {

              $seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($option_id_location, '', 'user', $item->getIdentity());
              if (!empty($column_exist)) {
                Engine_Api::_()->fields()->getTable('user', 'search')->update(array('location' => $option_id_location), array('item_id =?' => $item->getIdentity()));
              }

              Engine_Api::_()->getDbtable('users', 'user')->update(array('seao_locationid' => $seaoLocation, 'location' => $option_id_location), array('user_id =?' => $item->getIdentity()));
              
              $getMyLocationDetailsCookie = array();
              if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.change.user.location', 0)) {
                $locationRow = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocation(array('location' => $option_id_location));
                $getMyLocationDetailsCookie['location'] = $option_id_location;
                $getMyLocationDetailsCookie['latitude'] = $locationRow->latitude;
                $getMyLocationDetailsCookie['longitude'] = $locationRow->longitude;
                $getMyLocationDetailsCookie['changeLocationWidget'] = 1;
                Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($getMyLocationDetailsCookie);
              }
            }
          }
        }
      }
    }
  }

  public function onUserUpdateAfter($event) {
 
    $item = $event->getPayload();
    $front = Zend_Controller_Front::getInstance();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    if (Engine_Api::_()->core()->hasSubject()) {
        $user = Engine_Api::_()->core()->getSubject();
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $payload = $event->getPayload(); 
    if (!empty($viewer) && !empty($user) && !$viewer->isSelf($user)) {
         if ((in_array('view_count', $payload->getModifiedFieldsName())) || ($controller == 'profile' && $action == 'index')) {
                Engine_Api::_()->getDbtable('views', 'sitemember')->insertView(array("viewer_id"=>$viewer->getIdentity(),"user_id" =>$user->getIdentity()));
        }
    }
    
    if ($controller == 'edit' && $action == 'profile') {
      
      // Update display name
      $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);

      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $table_exist = $db->query('SHOW TABLES LIKE \'engine4_user_fields_search\'')->fetch();
      if (!empty($table_exist)) {
        $column_exist = $db->query('SHOW COLUMNS FROM engine4_user_fields_search LIKE \'location\'')->fetch();
      }
      
      $getMemberLocationViews = Engine_Api::_()->sitemember()->getMemberLocationViews();
      if(!empty($getMemberLocationViews))
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitemember.viewtypeinfo.type', 0);
        
      $option_id = Engine_Api::_()->getDbtable('profilemaps', 'sitemember')->getOptionIds(array('option_id' => $aliasValues['profile_type'], 'fetchColumn' => array('profile_type')));
      if (!empty($option_id)) {

        $valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
        $valuesTableName = $valuesTable->info('name');

        $select = $valuesTable->select()
                ->from($valuesTableName, array('value'))
                ->where($valuesTableName . '.item_id = ?', $item->user_id)
                ->where($valuesTableName . '.field_id = ?', $option_id);
        $valuesResultsLocation = $select->query()->fetchColumn();

        if (!empty($valuesResultsLocation)) {

          //DELETE THE RESULT FORM THE TABLE.
          Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $item->user_id, 'resource_type = ?' => 'user'));

          $seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($valuesResultsLocation, '', 'user', $item->user_id);

          if (!empty($column_exist)) {
            Engine_Api::_()->fields()->getTable('user', 'search')->update(array('location' => $valuesResultsLocation), array('item_id =?' => $item->user_id));
          }

          Engine_Api::_()->getDbtable('users', 'user')->update(array('seao_locationid' => $seaoLocation, 'location' => $valuesResultsLocation), array('user_id =?' => $item->user_id));
             $getMyLocationDetailsCookie = array();
             if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.change.user.location', 1)) {
                $locationRow = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocation(array('location' => $valuesResultsLocation));
                $getMyLocationDetailsCookie['location'] = $valuesResultsLocation;
                $getMyLocationDetailsCookie['latitude'] = $locationRow->latitude;
                $getMyLocationDetailsCookie['longitude'] = $locationRow->longitude;
                $getMyLocationDetailsCookie['changeLocationWidget'] = 1;
                Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($getMyLocationDetailsCookie);
              }
        } else {
          //DELETE THE RESULT FORM THE TABLE.
          Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $item->user_id, 'resource_type = ?' => 'user'));

          //event table entry of location id.
          Engine_Api::_()->getDbtable('users', 'user')->update(array('seao_locationid' => 0, 'location' => ''), array('user_id =?' => $item->user_id));
        }
      }
    }
  }
  
  public function onUserDeleteAfter($event) {
        $payload = $event->getPayload();
        $user_id = $payload['identity'];

        // userinfo
        $table = Engine_Api::_()->getDbTable('userInfo', 'seaocore');
        $table->delete(array(
            'user_id = ?' => $user_id,
        ));

        // Reviews
        $table = Engine_Api::_()->getDbTable('reviews', 'sitemember');
        $select = $table->select()->where('resource_id = ?', $user_id)->where('resource_type =? ', 'user');
        $rows = $table->fetchAll($select);
        foreach ($rows as $row) {
            $row->delete();
        }

        $tableCompliment = Engine_Api::_()->getDbtable('compliments', 'sitemember');
        $select = $tableCompliment->select()->where('resource_id = ?', $user_id)->where('resource_type =? ', 'user');
        $rows = $tableCompliment->fetchAll($select);
        foreach ($rows as $row) {
            $row->delete();
        }
        $select = $tableCompliment->select()->where('user_id = ?', $user_id);
        $rows = $tableCompliment->fetchAll($select);
        foreach ($rows as $row) {
            $row->delete();
        }
        

    }

  public function onRenderLayoutDefault($event) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $URL = $view->url(array(), 'sitemember_userbylocation', true);
    $Browse = $view->translate("Advance Browse By Locations");
    if ($module == 'user' && $controller == 'index' && $action == 'browse') {
      $script = <<<EOF
				window.addEvent('domready', function()
				{
          var globalContentElement = en4.seaocore.getDomElements('content');
          if($(globalContentElement).getElement('.field_search_criteria')) {
            var element = $(globalContentElement).getElement('.field_search_criteria');
          }
						new Element('a', {
						'id' : 'getcodeLink',
						'class' : 'buttonlink stcheckin_icon_map_search',
						'style' : 'margin-bottom:10px;',
						'href' : "$URL",
						'html' : "$Browse",
					}).inject(element, 'before');
				});
EOF;
      $view->headScript()->appendScript($script);
    }
  }
}
