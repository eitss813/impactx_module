<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topic.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_Milestone extends Core_Model_Item_Abstract {

    public function setLogo($photo, $param = array()) {
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
            throw new Zend_Exception("invalid argument passed to setLogo");
        }
        if (!$fileName) {
            $fileName = basename($file);
        }

        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $viewer = Engine_Api::_()->user()->getViewer();

        $params = array(
            'parent_type' => 'sitecrowdfunding_milestone',
            'parent_id' => $this->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        //Fetching the width and height of thumbmail
        $mainHeight = 680;
        $mainWidth = 1400;
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

        //RESIZE IMAGE (Midum)
        $mediumPath = $path . DIRECTORY_SEPARATOR . $base . '_im.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(200, 200)
            ->write($mediumPath)
            ->destroy();

        // Resize image (icon)
        $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
            ->write($squarePath)
            ->destroy();

        // Store
        $iMain = $filesTable->createFile($mainPath, $params);
        $iProfile = $filesTable->createFile($profilePath, $params);
        $iIconNormal = $filesTable->createFile($normalPath, $params);
        $iSquare = $filesTable->createFile($squarePath, $params);
        $iMedium = $filesTable->createFile($mediumPath, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iMedium, 'thumb.midum');
        $iMain->bridge($iSquare, 'thumb.icon');
        $iMain->bridge($iMain, 'thumb.main');

        // Remove temp files
        @unlink($mainPath);
        @unlink($mediumPath);
        @unlink($profilePath);
        @unlink($normalPath);
        @unlink($squarePath);

        return $iMain->getIdentity();
    }

    /**
     * Gets a url to the current project representing this item. Return null if none
     * set
     *
     * @param string The project type (null -> main, thumb, icon, etc);
     * @return string The project url
     */
    public function getLogoUrl($type = null) {
        $logo_id = $this->logo;
        if (!$logo_id) {
            return null;
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($logo_id, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    }
}