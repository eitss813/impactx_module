<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: OptionsVideos.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_OptionsVideos extends Engine_Db_Table {

    protected $_name = 'video_fields_options';
    protected $_rowClass = 'Sitevideo_Model_Option';

    public function getFieldLabel($field_id) {

        $select = $this->select()
                ->where('field_id = ?', $field_id);
        $result = $this->fetchRow($select);

        return !empty($result) ? $result->label : '';
    }

    public function getProfileTypeLabel($option_id) {

        if (empty($option_id)) {
            return;
        }

        //GET FIELD OPTION TABLE NAME
        $tableFieldOptionsName = $this->info('name');

        //FETCH PROFILE TYPE LABEL
        $profileTypeLabel = $this->select()
                ->from($tableFieldOptionsName, array('label'))
                ->where('option_id = ?', $option_id)
                ->query()
                ->fetchColumn();

        return $profileTypeLabel;
    }

}
