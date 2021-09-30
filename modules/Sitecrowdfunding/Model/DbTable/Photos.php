<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Photos extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Photo';

    public function getPhotoId($project_id = null, $file_id = null) {

        $photo_id = 0;
        $photo_id = $this->select()
                ->from($this->info('name'), array('photo_id'))
                ->where("project_id = ?", $project_id)
                ->where("file_id = ?", $file_id)
                ->query()
                ->fetchColumn();

        return $photo_id;
    }

}
