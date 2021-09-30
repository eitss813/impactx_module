<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
class Yndynamicform_Plugin_Menus
{
    public function onMenuInitialize_YndynamicformMainManageEntries()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity()) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_YndynamicformMainManageModeratedForms()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer -> getIdentity()) {
            $table = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform');
            $select = $table -> select() -> where('moderator_id = ?', $viewer -> getIdentity()) -> limit(1);
            if (!$table -> fetchRow($select)) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
}