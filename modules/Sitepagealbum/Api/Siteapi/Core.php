<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagealbum_Api_Siteapi_Core extends Core_Api_Abstract {
    

    /**
	* Adds photo to the album
    *
    *
    */
	public function setPhoto($photo, $subject, $needToUplode = false,$params=array()) {
		try {

			if ($photo instanceof Zend_Form_Element_File) {
				$file = $photo->getFileName();
			} else if (is_array($photo) && !empty($photo['tmp_name'])) {
				$file = $photo['tmp_name'];
			} else if (is_string($photo) && file_exists($photo)) {
				$file = $photo;
			} else {
				throw new Group_Model_Exception('invalid argument passed to setPhoto');
			}
		} catch (Exception $e) {
			
		}

		$imageName = $photo['name'];
		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

		$params = array(
			'parent_type' => 'sitepage_page',
			'parent_id' => $subject->getIdentity()
		);

		// Save
		$storage = Engine_Api::_()->storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image->open($file)
				->resize(720, 720)
				->write($path . '/m_' . $imageName)
				->destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image->open($file)
				->resize(200, 400)
				->write($path . '/p_' . $imageName)
				->destroy();

		// Resize image (normal)
		$image = Engine_Image::factory();
		$image->open($file)
				->resize(140, 160)
				->write($path . '/in_' . $imageName)
				->destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image->open($file);

		$size = min($image->height, $image->width);
		$x = ($image->width - $size) / 2;
		$y = ($image->height - $size) / 2;

		$image->resample($x, $y, $size, $size, 48, 48)
				->write($path . '/is_' . $imageName)
				->destroy();

		// Store
		$iMain = $storage->create($path . '/m_' . $imageName, $params);
		$iProfile = $storage->create($path . '/p_' . $imageName, $params);
		$iIconNormal = $storage->create($path . '/in_' . $imageName, $params);
		$iSquare = $storage->create($path . '/is_' . $imageName, $params);

		$iMain->bridge($iProfile, 'thumb.profile');
		$iMain->bridge($iIconNormal, 'thumb.normal');
		$iMain->bridge($iSquare, 'thumb.icon');

		// Remove temp files
		@unlink($path . '/p_' . $imageName);
		@unlink($path . '/m_' . $imageName);
		@unlink($path . '/in_' . $imageName);
		@unlink($path . '/is_' . $imageName);

		// Update row
		if (empty($needToUplode)) {
			$subject->modified_date = date('Y-m-d H:i:s');
			$subject->save();
		}

		// Add to album
		$viewer = Engine_Api::_()->user()->getViewer();
		$photoTable = Engine_Api::_()->getItemTable('sitepage_photo');
		if(isset($params['album_id']) && !empty($params['album_id']))
		{
			$album = Engine_Api::_()->getItem('sitepage_album', $params['album_id']);
			if(!$album->toArray())
			{
				$album = $subject->getSingletonAlbum();
				$album->owner_id = $viewer->getIdentity();
				$album->save();
			}
		}
		else
		{
			$album = $subject->getSingletonAlbum();
			$album->owner_id = $viewer->getIdentity();
			$album->save();
		}
		$photoItem = $photoTable->createRow();
		$photoItem->setFromArray(array(
			'page_id' => $subject->getIdentity(),
			'album_id' => $album->getIdentity(),
			'user_id' => $viewer->getIdentity(),
			'file_id' => $iMain->getIdentity(),
			'collection_id' => $album->getIdentity()
		));
		$photoItem->save();

		return $subject;
	}
}