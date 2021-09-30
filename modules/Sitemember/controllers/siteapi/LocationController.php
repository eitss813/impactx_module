<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    IndexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 * 
 */
class Sitemember_LocationController extends Siteapi_Controller_Action_Standard {

    //ACTION FOR EDIT LOCATION
    public function editLocationAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();

        if ($this->_getParam('resource_type') && $this->_getParam('user_id') && $this->_getParam('seao_locationid')) {
            $resource_type = $this->_getParam('resource_type');
            $resource_id = $this->_getParam('user_id');
            $seao_locationid = $value['id'] = $seao_locationid = $this->_getParam('seao_locationid');
        } else {

            if (!Engine_Api::_()->core()->hasSubject()) {
                $user = $subject = Engine_Api::_()->user()->getViewer();
                Engine_Api::_()->core()->setSubject($subject);
            }
            $subject = Engine_Api::_()->core()->getSubject();
            $resource_type = 'user';
            $resource_id = $subject->getIdentity();
            $seao_locationid = $value['id'] = $seao_locationid = $subject->seao_locationid;
            $editlocation = 1;
        }

        $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
        $getMemberLocationViews = Engine_Api::_()->sitemember()->getMemberLocationViews();
        $seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');

        if (Engine_Api::_()->user()->getViewer()->getIdentity() != $resource->user_id) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $location = $seLocationsTable->getLocations($value);

        //Get form
        if (!empty($location)) {

            $form = Engine_Api::_()->getApi('Siteapi_Core', 'sitemember')->editLocation();

            if ($this->getRequest()->isGet()) {
                $response['form'] = $form;
                $response['formValues'] = $location->toArray();
                $this->respondWithSuccess($response);
            }

            if ($this->getRequest()->isPost()) {
                $values = $location->toArray();
                foreach ($form as $element) {
                    if (isset($_REQUEST[$element['name']]))
                        $values[$element['name']] = $_REQUEST[$element['name']];
                }
                $values['location'] = $values['formatted_address'];
                $seLocationsTable->update($values, array('locationitem_id =?' => $seao_locationid));
                if (!empty($getMemberLocationViews))
                    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitemember.viewtypeinfo.type', 0);
            }

            $this->successResponseNoContent('no_content', true);
        }
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
        $form = Engine_Api::_()->getApi('Siteapi_Core', 'sitemember')->getEditAdressForm($resource);


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
                } elseif (isset($value['type']) && $value['type'] == 'city' && $profile_type == $value['field_id']) {
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
        if ($this->getRequest()->isGet()) {
            $response['form'] = $form;
            $response['formValues']['location'] = $resource->location;
            $response['formValues']['location_privacy'] = $prevPrivacy;
            $this->respondWithSuccess($response);
        }

        //FORM VALIDATION
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                $postData = $_REQUEST;
                $values = array();
                if (!empty($postData['location'])) {
                    $values = Zend_Json::decode($postData['location']);
                    $values['resource_id'] = $resource_id;
                    $values['resource_type'] = $resource_type;
                }
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

                if (!empty($values['location'])) {

                    //DELETE THE RESULT FORM THE TABLE.
                    Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resource_id, 'resource_type = ?' => $resource_type));

//                    $seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($values['location'], '', $resource_type, $resource_id);
                    $seaoLocation = $this->setLocation($values, $resource_type, $resource_id);
                    //group table entry of location id.
                    $itemTable->update(array('seao_locationid' => $seaoLocation), array("$id =?" => $resource_id));
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
        }
    }

    function setLocation($location = array(), $resource_type = null, $resource_id = null) {
        $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
        $select = $locationTable->select()
                ->where('resource_id = ?', $resource_id)
                ->where('resource_type = ?', $resource_type)
                ->where('location = ?', $location['location']);
        $row = $locationTable->fetchRow($select);
        $location['resource_type'] = $resource_type;
        $location['resource_id'] = $resource_id;
        if ($row == null) {
            $row = $locationTable->createRow();
            $row->setFromArray($location);
        }
        if (null !== $row) {
            $row->setFromArray($location);
        }
        $row->save();

        return $row->locationitem_id;
    }

}
