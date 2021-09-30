<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Metas.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_DbTable_Metas extends Engine_Db_Table {

    protected $_name = 'album_fields_meta';
    protected $_rowClass = 'Sitealbum_Model_Meta';

    /**
     * Get Default Profile Id
     *
     */
    public function defaultProfileId() {

        //GET DEFAULT PROFILE ID
        $defaultProfileId = $this->select()
                ->from($this->info('name'), array('field_id'))
                ->where('type = ?', 'profile_type')
                ->where('alias = ?', 'profile_type')
                ->query()
                ->fetchColumn();

        //RETURN DEFAULT PROFILE ID
        return $defaultProfileId;
    }

    public function getProfileFields($map_id) {
        if (empty($map_id))
            return;
        //Pickup the dynamic values in the fields_meta table according to the profile type
        $rmetaName = $this->info('name');
        $maptable = Engine_Api::_()->getDBTable('maps', 'sitealbum');
        $rmapName = $maptable->info('name');
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($this, array($rmetaName . '.field_id', $rmetaName . '.label', $rmetaName . '.type'))
                ->join($rmapName, $rmapName . '.child_id = ' . $rmetaName . '.field_id', array())
                ->where($rmapName . '.option_id = ?', $map_id)
                ->order($rmapName . ".order");
        $checkval = $this->fetchAll($select);

        //Dynamic select_option created here
        $storeIndex;
        $selectOption = array();
        foreach ($checkval->toarray() as $key => $value) {

            foreach ($value as $k => $v) {

                if ($k == 'field_id')
                    $storeIndex = $v;
                if ($k == 'label')
                    $selectOption[$storeIndex]['lable'] = $v;
                if ($k == 'type')
                    $selectOption[$storeIndex]['type'] = $v;
            }
        }

        return $selectOption;
    }

}