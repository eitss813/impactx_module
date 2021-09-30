<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: photo.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_Photo extends Core_Model_Item_Collectible {

  protected $_parent_type = 'sitepage_album';
  protected $_owner_type = 'user';
  protected $_collection_type = 'sitepage_album';

  public function getMediaType() {
    return 'photo';
  }

  // General
  public function getShortType($inflect = false) {

    if ($inflect)
      return 'Photo';

    return 'photo';
  }

  /**
   * Return page object
   *
   * @return page object
   * */
  public function getParent($recurseType = null) {

    if ($recurseType == null)
      $recurseType = 'sitepage_page';

    return Engine_Api::_()->getItem($recurseType, $this->page_id);
  }

  /**
   * Gets an absolute URL to the photo to view this item
   *
   * @param array $params
   * @return string
   */
  public function getHref($params = array()) {

    $tab_id='';
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.sitemobile-photos-sitepage', $this->page_id, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $this->page_id, $layout);
		}

    $params = array_merge(array(
        'route' => 'sitepage_imagephoto_specific',
        'reset' => true,
        'page_id' => $this->page_id,
        'album_id' => $this->collection_id,
        'photo_id' => $this->getIdentity(),
        'tab' => $tab_id
            ), $params);

    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  /**
   * Gets an absolute Photo URL to the page to view this item
   *
   * @param char $type
   * @return string
   */
  public function getPhotoUrl($type = null) {

    if (empty($this->file_id)) {
      return null;
    }
    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
    if (!$file) {
      return null;
    }
    return $file->map();
  }

  /**
   * Search of photos
   * 
   * @return collection of searchable photos
   */
  public function isSearchable() {

    $collection = $this->getCollection();
    if (!$collection instanceof Core_Model_Item_Abstract) {
      return false;
    }
    return $collection->isSearchable();
  }

  /**
   * Return a description
   *
   * @return truncated description
   * */
  public function getDescription() {

    $tmpBody = strip_tags($this->description);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  /**
   * Gets sitepage item
   * 
   * @return sitepage item
   */
  public function getAuthorizationItem() {

    return $this->getParent('sitepage_page');
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  protected function _delete() {
    // Delete create activity feed of photo album before delete photo
    Engine_Api::_()->getApi('subCore', 'sitepage')->deleteCreateActivityOfExtensionsItem($this, array('sitepagealbum_photo_new', 'sitepagealbum_admin_photo_new'));
    parent::_delete();
  }

  /**
   * Deletes the photos
   */
  protected function _postDelete() {

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $this->page_id);
    if ($sitepage->page_cover == $this->photo_id) {
      $sitepage->page_cover = 0;
      $sitepage->save();
    }
    $mainPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
    $thumbPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normal');
    if ($thumbPhoto && $thumbPhoto->getIdentity()) {
      try {
        $thumbPhoto->delete();
      } catch (Exception $e) {
        
      }
    }
    $coverPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.cover');
    if ($coverPhoto && $coverPhoto->getIdentity()) {
      try {
        $coverPhoto->delete();
      } catch (Exception $e) {
        
      }
    }
    if ($mainPhoto && $mainPhoto->getIdentity()) {
      try {
        $mainPhoto->delete();
      } catch (Exception $e) {
        
      }
    }
    try {
      if (!empty($this->collection_id)) {
        $album = $this->getCollection();
        $nextPhoto = $this->getNextCollectible();
        if (($album instanceof Core_Model_Item_Collection) &&
                ($nextPhoto instanceof Core_Model_Item_Collectible) &&
                (int) $album->photo_id == (int) $this->file_id) {
          $album->photo_id = $nextPhoto->file_id;
          $album->save();
        }
      }
    } catch (Exception $e) {
      
    }
    parent::_postDelete();
  }

  /**
   * Set a photo
   *
   * @param array photo
   * @return photo object
   */
  public function setPhoto($photo, $isCover = false) {

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
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if (!$fileName) {
      $fileName = $file;
    }

    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => $this->getType(),
        'parent_id' => $this->getIdentity(),
        'user_id' => $this->user_id,
        'name' => $fileName,
    );

    // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
    $usingLessVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
    if(!empty($usingLessVersion)) {
      $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(720, 720)
              ->write($mainPath)
              ->destroy();
      $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(140, 160)
              ->write($normalPath)
              ->destroy();

      if ($isCover) {
        $coverPath = $path . DIRECTORY_SEPARATOR . $base . '_c.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                 ->resize(1000, 1000)
                ->write($coverPath)
                ->destroy();
      }
    }else {
      $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->autoRotate()
              ->resize(720, 720)
              ->write($mainPath)
              ->destroy();
      $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->autoRotate()
              ->resize(140, 160)
              ->write($normalPath)
              ->destroy();

      if ($isCover) {
        $coverPath = $path . DIRECTORY_SEPARATOR . $base . '_c.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->autoRotate()
                 ->resize(1000, 1000)
                ->write($coverPath)
                ->destroy();
      }
    }
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
      $iIconNormal = $filesTable->createFile($normalPath, $params);
      $iMain->bridge($iIconNormal, 'thumb.normal');
      if ($isCover) {
        $iCover = $filesTable->createFile($coverPath, $params);
        $iMain->bridge($iCover, 'thumb.cover');
      }
    } catch (Exception $e) {
      @unlink($mainPath);
      @unlink($normalPath);
      if ($isCover) {
        @unlink($coverPath);
      }
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    @unlink($mainPath);
    @unlink($normalPath);
    if ($isCover) {
      @unlink($coverPath);
    }
    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
    $this->save();
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }
    return $this;
  }

}

?>