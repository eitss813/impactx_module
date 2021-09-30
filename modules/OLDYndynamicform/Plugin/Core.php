<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 9/5/2016
 * Time: 6:42 PM
 */
class Yndynamicform_Plugin_Core
{
    public function onItemDeleteBefore($event)
    {
        $yndform = $event -> getPayLoad();
            // Remove entries
            if ($yndform instanceof Yndynamicform_Model_Form) {
                $list_entries = Engine_Api::_() -> getDbTable('entries', 'yndynamicform') -> getEntriesPaginator(array('form_id' => $yndform -> getIdentity()));
                if (count($list_entries) > 0) {
                    foreach ($list_entries as $item) {
                        Engine_Api::_()->getApi('core', 'fields') -> removeItemValues($item);
                        $item -> delete();
                    }
                }

                // Remove confirmations
                $list_confirmations = Engine_Api::_() -> getDbTable('confirmations', 'yndynamicform') -> getConfirmations(array('form_id' => $yndform -> getIdentity()));
                if (count($list_confirmations) > 0 ) {
                    foreach ($list_confirmations as $item)
                        $item -> delete();
                }

                // Remove notifications
                $list_notifications = Engine_Api::_() -> getDbTable('notifications', 'yndynamicform') -> getNotifications(array('form_id' => $yndform -> getIdentity()));
                if (count($list_notifications) > 0 ) {
                    foreach ($list_notifications as $item)
                        $item -> delete();
                }

                // Remove moderators
                $list_moderators = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform') -> getModerators(array('form_id' => $yndform -> getIdentity()));
                if (count($list_moderators) > 0 ) {
                    foreach ($list_moderators as $item)
                        $item -> delete();
                }

                // Remove fields
                $fields_api = Engine_Api::_()->getApi('core', 'fields');
                $mapsTable = $fields_api -> getTable('yndynamicform_entry', 'maps');
                $maps = $mapsTable -> fetchAll($mapsTable->select()->where('option_id = ?', $yndform -> option_id)->order('order'));
                if (count($maps) > 0) {
                    foreach ($maps as $map) {
                        if ($map -> field_id && $map -> option_id == $yndform -> option_id)
                        {
                            $fields_api -> deleteMap($map);
                        }
                    }
                }
                $fields_api -> deleteOption('yndynamicform_entry', $yndform -> option_id);
            }
        }

    public function onUserSignupAfter($event)
    {
        $user = $event -> getPayLoad();
        if ($user instanceof User_Model_User) {
            $entries_table = Engine_Api::_() -> getDbTable('entries', 'yndynamicform');
            $select = $entries_table->select()->where('owner_id = ? ', 0)->where('user_email = ? ', $user -> email);
            $entries = $entries_table -> fetchAll($select);
            if (count($entries) > 0) {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    foreach ($entries as $entry) {
                        $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $entry -> form_id);
                        if ($yndform) {
                            $entry->owner_id = $user->getIdentity();
                            $entry->user_email = null;
                            $entry->save();
                        }
                    }
                    Engine_Api::_()->getDbTable('notifications', 'activity')->addNotification($user,$user,$user,'yndynamicform_linked_form_submission', array(
                        'email_address' => $user -> email,
                        'site_name' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'My Communication'),
                        'manage_entries_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'yndynamicform_entry_general', true),
                    ));

                    $db -> commit();
                } catch (Exception $e) {
                    $db -> rollBack();
                    throw $e;
                }
            }
        }
    }
}