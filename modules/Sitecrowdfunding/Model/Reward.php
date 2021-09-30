<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Reward.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_Reward extends Core_Model_Item_Abstract {

    public function getAllCountries() {
        $regionTableName = Engine_Api::_()->getDbTable('regions', 'sitecrowdfunding')->info('name');
        $locationTable = Engine_Api::_()->getDbTable('rewardshippinglocations', 'sitecrowdfunding');
        $locationTableName = $locationTable->info('name');
        $select = $locationTable
                ->select()
                ->setIntegrityCheck(false)
                ->from($locationTableName)
                ->joinLeft($regionTableName, "$regionTableName.region_id = $locationTableName.region_id")
                ->where('project_id = ?', $this->project_id)
                ->where('reward_id = ?', $this->reward_id);

        $locations = $locationTable->fetchAll($select);
        return !empty($locations) ? $locations : array();
    }

    //FUNCTION TO RETURN SELECTED AND DISPATCHED QUANTITIY OF REWARD
    public function spendRewardQuantity($onlyDispatched = false) {
        $backerTable = Engine_Api::_()->getDbTable('backers', 'sitecrowdfunding');
        $backerTableName = $backerTable->info('name');
        $select = $backerTable->select();
        $select->from($backerTableName, array('count(*)'))
                ->where('project_id = ?', $this->project_id)
                ->where('reward_id = ?', $this->reward_id);
        if ($onlyDispatched) {
            $select->where('reward_status = ?', 1);
        }
        $rewardCount = $select->query()->fetchColumn();
        if (empty($rewardCount)) {
            $rewardCount = 0;
        }
        return $rewardCount;
    }

    public function findShippingLocations($projectId) {

        $shippingLocationTable = Engine_Api::_()->getDbtable('rewardshippinglocations', 'sitecrowdfunding');
        $locations = $shippingLocationTable->select()
                ->where('project_id=?', $projectId)
                ->where('reward_id=?', $this->getIdentity())
                ->query()
                ->fetchAll();
        return $locations;
    }

    protected function _delete() {

        $backersTable = Engine_Api::_()->getItemTable('sitecrowdfunding_backer');
        $backersTable->update(array('reward_id' => null), array('reward_id=?' => $this->reward_id));
        $rewardShippingTable = Engine_Api::_()->getItemTable('sitecrowdfunding_rewardshippinglocation');
        $rewardShippingTable->delete(array('reward_id=?' => $this->reward_id));
        //DELETE THE REWARD PHOTO
        if (!empty($this->photo_id) && Engine_Api::_()->getItem('storage_file', $this->photo_id))
            Engine_Api::_()->getItem('storage_file', $this->photo_id)->remove();
        parent::_delete();
    }

    /**
     * Set project photo
     *
     * */
    public function setPhoto($photo, $param = array()) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new Zend_Exception("invalid argument passed to setPhoto");
        }
        if (!$fileName) {
            $fileName = basename($file);
        }

        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => 'sitecrowdfunding_reward',
            'parent_id' => $this->getIdentity(),
            'user_id' => $this->owner_id,
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        //Fetching the width and height of thumbmail
        $mainHeight = 720;
        $mainWidth = 720;
        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize($mainWidth, $mainHeight)
                ->write($mainPath)
                ->destroy();

        // Resize image (profile)
        $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_l.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(300, 500)
                ->write($profilePath)
                ->destroy();

        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($normalPath)
                ->destroy();
        if ($this->photo_id) {
            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.profile');
            if ($file)
                $file->remove();
            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.normal');
            if ($file)
                $file->remove();
            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.main');
            if ($file)
                $file->remove();
        }
        // Store
        $iMain = $filesTable->createFile($mainPath, $params);
        $iProfile = $filesTable->createFile($profilePath, $params);
        $iIconNormal = $filesTable->createFile($normalPath, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iMain, 'thumb.main');

        // Remove temp files
        @unlink($mainPath);
        @unlink($profilePath);
        @unlink($normalPath);

        $this->photo_id = $iMain->getIdentity();
        $this->save();
        return $this;
    }

}
