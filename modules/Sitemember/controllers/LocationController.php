<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LocationController.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_LocationController extends Core_Controller_Action_Standard {

  //ACTION FOR USER AUTO SUGGEST.
  public function getmemberAction() {

    $data = array();
    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $select = $usersTable->select()
                    ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
                    ->order('displayname ASC')->limit('40');
    $users = $usersTable->fetchAll($select);

    foreach ($users as $user) {
      $user_photo = $this->view->itemPhoto($user, 'thumb.icon');
      $data[] = array(
          'id' => $user->user_id,
          'label' => $user->displayname,
          'photo' => $user_photo
      );
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR EDIT LOCATION
  public function editLocationAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->viewer = Engine_Api::_()->user()->getViewer();

    if ($this->_getParam('resource_type') && $this->_getParam('user_id') && $this->_getParam('seao_locationid')) {
      $this->view->resource_type = $this->_getParam('resource_type');
      $this->view->resource_id = $this->_getParam('user_id');
      $this->view->seao_locationid = $value['id'] = $seao_locationid = $this->_getParam('seao_locationid');
    } else {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('user_edit', array(), 'user_edit_location');
      if (!Engine_Api::_()->core()->hasSubject()) {
        $this->view->user = $subject = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->core()->setSubject($subject);
      }
      $subject = Engine_Api::_()->core()->getSubject();
      $this->view->resource_type = 'user';
      $this->view->resource_id = $subject->getIdentity();
      $this->view->seao_locationid = $value['id'] = $seao_locationid = $subject->seao_locationid;
      $this->view->editlocation = 1;
    }

    $this->view->resource = $resource = Engine_Api::_()->getItem($this->view->resource_type, $this->view->resource_id);

    $getMemberLocationViews = Engine_Api::_()->sitemember()->getMemberLocationViews();
    $seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');

    if (Engine_Api::_()->user()->getViewer()->getIdentity() != $resource->user_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $this->view->location = $location = $seLocationsTable->getLocations($value);

    //Get form
    if (!empty($location)) {

      $this->view->form = $form = new Seaocore_Form_Location(array('item' => $resource, 'location' => $location->location));

      if (!$this->getRequest()->isPost()) {
        $form->populate($location->toarray());
        return;
      }

      //FORM VALIDAITON
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      //FORM VALIDAITON
      if ($form->isValid($this->getRequest()->getPost())) {
        $values = $form->getValues();
        unset($values['submit']);
        $values['location'] = $values['formatted_address'];
        $seLocationsTable->update($values, array('locationitem_id =?' => $seao_locationid));
        if (!empty($getMemberLocationViews))
          Engine_Api::_()->getApi('settings', 'core')->setSetting('sitemember.viewtypeinfo.type', 0);
      }

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
    $this->view->location = $seLocationsTable->getLocations($value);
  }

  //ACTION FOR EDIT ADDRESS
  public function editAddressAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $resource_type = $this->_getParam('resource_type');
    if ($resource_type == 'user') {
      $id = 'user_id';
      $itemTable = Engine_Api::_()->getDbtable('users', 'user');
      $route = 'sitemember_userspecific';
    }

    //$seao_locationid = $this->_getParam('seao_locationid');
    $resource_id = $this->_getParam($id);
    $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

    $this->view->form = $form = new Sitemember_Form_Address(array('item' => $resource));

    
    $prevPrivacy = 'everyone';
    if ($resource_id) {
        $fields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($resource);
        $field_id = '';
        
        $aliasedFields = $resource->fields()->getFieldsObjectsByAlias();
        $topLevelId = $aliasedFields['profile_type']->field_id;
        $profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitemember');
        $profilemapsTablename = $profilemapsTable->info('name');
        $select = $profilemapsTable->select()->from($profilemapsTablename, array('profile_type'));
        $select->where($profilemapsTablename . '.option_id = ?', $topLevelId);

        $profile_type = $select->query()->fetchColumn();

        foreach ($fields as $value) {
            if (isset($value['type']) && $value['type'] == 'location' && $profile_type == $value['field_id']) {
                $field_id = $value['field_id'];
            } elseif(isset($value['type']) && $value['type'] == 'city' && $profile_type == $value['field_id']) {
                $field_id = $value['field_id'];
            }
        }
        if ($field_id) {
            $values = Engine_Api::_()->fields()->getFieldsValues($resource);
            $valueRows = $values->getRowsMatching(array(
                'field_id' => $field_id,
                'item_id' => $resource->getIdentity()
            ));
            foreach ($valueRows as $valueRow) {
                $prevPrivacy = $valueRow->privacy;
            }
        }
    }
    
    //POPULATE FORM
    if (!$this->getRequest()->isPost()) {
      $form->populate($resource->toArray());
      $form->location_privacy->setValue($prevPrivacy);
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {

      $values = $form->getValues();

      //Update field value
      //if ($resource_type == 'user') {

        $aliasedFields = $resource->fields()->getFieldsObjectsByAlias();
        $topLevelId = $aliasedFields['profile_type']->field_id;
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_user_fields_search\'')->fetch();
        if (!empty($table_exist)) {
          $column_exist = $db->query('SHOW COLUMNS FROM engine4_user_fields_search LIKE \'location\'')->fetch();
        }

        $profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitemember');
        $profilemapsTablename = $profilemapsTable->info('name');

        $select = $profilemapsTable->select()->from($profilemapsTablename, array('profile_type'));
       
        $select->where($profilemapsTablename . '.option_id = ?', $topLevelId);
        $profile_type = $select->query()->fetchColumn();

        if (!empty($profile_type)) {

          $valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
          $valuesTableName = $valuesTable->info('name');

          $select = $valuesTable->select()
                  ->from($valuesTableName, array('value'))
                  ->where($valuesTableName . '.item_id = ?', $resource_id)
                  ->where($valuesTableName . '.field_id = ?', $profile_type);
          $valuesResultsLocation = $select->query()->fetchAll();

          if (empty($valuesResultsLocation)) {
                    Engine_Api::_()->fields()->getTable('user', 'values')->insert(array('value' => $values['location'], 'item_id' => $resource_id, 'field_id' => $profile_type, 'privacy' => $values['location_privacy']));
            } else {
                Engine_Api::_()->fields()->getTable('user', 'values')->update(array('value' => $values['location'], 'privacy' => $values['location_privacy']), array('item_id =?' => $resource_id, 'field_id =?' => $profile_type));
            }
                
            if (!empty($column_exist)) {
              Engine_Api::_()->fields()->getTable('user', 'search')->update(array('location' => $values['location']), array('item_id =?' => $resource_id));
            }
        }
      //}

      $resource->location = $values['location'];
      if (empty($values['location'])) {
        //DELETE THE RESULT FORM THE TABLE.
        Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resource_id, 'resource_type = ?' => $resource_type));
        $resource->seao_locationid = '0';
      }
      $resource->save();
      unset($values['submit']);

      if (!empty($values['location'])) {

        //DELETE THE RESULT FORM THE TABLE.
        Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resource_id, 'resource_type = ?' => $resource_type));

        $seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($values['location'], '', $resource_type, $resource_id);

        //group table entry of location id.
        $itemTable->update(array('seao_locationid' => $seaoLocation), array("$id =?" => $resource_id));
        
      }
      
      if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.change.user.location', 0)) {
          if(!empty($values['location'])) {
              $getMyLocationDetailsCookie=array();
            $locationRow = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocation(array('location' => $values['location']));
            $getMyLocationDetailsCookie['location'] = $values['location'];
            $getMyLocationDetailsCookie['latitude'] = $locationRow->latitude;
            $getMyLocationDetailsCookie['longitude'] = $locationRow->longitude;
            $getMyLocationDetailsCookie['changeLocationWidget'] = 1;
            Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($getMyLocationDetailsCookie);
          } else {
            $seaocore_myLocationDetails=array();
            $seaocore_myLocationDetails['location'] = '';
            $seaocore_myLocationDetails['latitude'] = 0;
            $seaocore_myLocationDetails['longitude'] = 0;
            $seaocore_myLocationDetails['changeLocationWidget'] = 1;
            $seaocore_myLocationDetails = Zend_Json::encode($seaocore_myLocationDetails);
            Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($getMyLocationDetailsCookie);
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            setcookie('seaocore_myLocationDetails', $seaocore_myLocationDetails, time() - 60 * 60 * 24 * 30, $view->url(array(), 'default', true));
          }
      }
      $db->commit();
      if ($this->_getParam('params')) {
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your location has been modified successfully.'))
        ));
      } else {

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRedirect' => $this->_helper->url->url(array('action' => 'edit-location', 'seao_locationid' => $seaoLocation, "$id" => $resource_id, "resource_type" => $resource_type), "$route", true),
            'parentRedirectTime' => '2',
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your location has been modified successfully.'))
        ));
      }
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR BROWSE LOCATION PAGES.
  public function userbyLocationsAction() {
    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse') && empty($viewer_id)) {
			return $this->_forward('requireauth', 'error', 'core');
    }
    $this->_helper->content->setEnabled();
  }

  //ACTION FOR GETTING THE AUTOSUGGESTED MEMBERS BASED ON SEARCHING
  public function getSearchMembersAction() {

    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $select = $usersTable->select()
                    ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
                    ->order('displayname ASC')->limit('40');
    $usersitemembers = $usersTable->fetchAll($select);

    $data = array();
    $mode = $this->_getParam('struct');
    $count = count($usersitemembers);
    
    if ($mode == 'text') {
      $i = 0;
      foreach ($usersitemembers as $usersitemember) {
        $sitemember_url = $this->view->url(array('id' => $usersitemember->user_id), "user_profile", true);
        $i++;
        $content_photo = $this->view->itemPhoto($usersitemember, 'thumb.icon');
        $data[] = array(
            'id' => $usersitemember->user_id,
            'label' => $usersitemember->displayname,
            'photo' => $content_photo,
            'sitemember_url' => $sitemember_url,
            'total_count' => $count,
            'count' => $i
        );
      }
    } else {
      $i = 0;
      foreach ($usersitemembers as $usersitemember) {
        $sitemember_url = $this->view->url(array('id' => $usersitemember->user_id), "user_profile", true);
        $content_photo = $this->view->itemPhoto($usersitemember, 'thumb.icon');
        $i++;
        $data[] = array(
            'id' => $usersitemember->user_id,
            'label' => $usersitemember->displayname,
            'photo' => $content_photo,
            'sitemember_url' => $sitemember_url,
            'total_count' => $count,
            'count' => $i
        );
      }
    }
    
    if (!empty($data) && $i >= 1) {
      if ($data[--$i]['count'] == $count) {
        $data[$count]['id'] = 'stopevent';
        $data[$count]['label'] = $this->_getParam('text');
        $data[$count]['sitemember_url'] = 'seeMoreLink';
        $data[$count]['total_count'] = $count;
      }
    }
    return $this->_helper->json($data);
  }

}