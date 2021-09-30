<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Maps.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_DbTable_Maps extends Engine_Db_Table {

    protected $_name = 'album_fields_maps';
    protected $_rowClass = 'Sitealbum_Model_Map';

    public function getMappingIds($profile_type_id) {

        if (empty($profile_type_id))
            return;

        $ids = array($profile_type_id);

        $meta_ids = array();

        $countIds = count($ids);
        $flag = 0;
        do {

            $select = $this->select()
                    ->from($this->info('name'), 'child_id');
            if ($flag) {
                $select->where('field_id  IN(?)', (array) $ids);
            } else {
                $select->where('option_id  IN(?)', (array) $ids);
                $select->where('field_id  = ?', 1);
            }

            $ids = $select->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
            $countIds = count($ids);
            $flag = 1;
            if ($countIds > 0) {
                $meta_ids = array_unique(array_merge($meta_ids, $ids));
            }
        } while ($countIds > 0);

        return $meta_ids;
    }

}
