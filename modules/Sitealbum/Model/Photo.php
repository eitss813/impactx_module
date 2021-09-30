<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photo.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_Photo extends Core_Model_Item_Abstract {

  protected $_searchTriggers = array('title', 'description', 'search');
  public $skipAlbumDeleteHook;
  protected $_type = 'album_photo';
 
  public function getMediaType() {

    return 'photo';
  }

  public function getType($inflect = false) {
    if ($inflect) {
      return str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_type)));
    }

    return $this->_type;
  }

  public function getHref($params = array()) {

    $params = array_merge(array(
        'route' => 'sitealbum_extended',
        'reset' => true,
        'album_id' => $this->album_id,
        'photo_id' => $this->getIdentity(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  public function getAlbum() {
    return Engine_Api::_()->getItem('album', $this->album_id);
  }

  public function getParent($type = null) {
    if (null === $type || $type === 'album') {
      return $this->getAlbum();
    } else {
      return $this->getAlbum()->getParent($type);
    }
  }

  /**
   * Gets a url to the current photo representing this item. Return null if none
   * set
   *
   * @param string The photo type (null -> main, thumb, icon, etc);
   * @return string The photo url
   */
  public function getPhotoUrl($type = null) {
    $photo_id = $this->file_id;
    if (!$photo_id) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, $type);
    if (!$file) {
      return null;
    }

    return $file->map();
  }

  public function isSearchable() {
    $album = $this->getAlbum();
    if (!($album instanceof Core_Model_Item_Abstract)) {
      return false;
    }
    return $album->isSearchable();
  }

  public function getAuthorizationItem() {
    return $this->getAlbum();
  }

  public function isOwner($user) {
    if (empty($this->album_id)) {
      return (($this->owner_id == $user->getIdentity()) && ($this->owner_type == $user->getType()));
    }
    return parent::isOwner($user);
  }

  public function setPhoto($photo) {
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
        'user_id' => $this->owner_id,
        'name' => $fileName,
    );
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    $mainHeight = $coreSettings->getSetting('main.photo.height', 1600);
    $mainWidth = $coreSettings->getSetting('main.photo.width', 1600);

    
    // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
    $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
    if(!empty($hasVersion)) {
      // Resize image (main)
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize($mainWidth, $mainHeight)
              ->write($mainPath)
              ->destroy();

      $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
      $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
      // Resize image (normal)
      $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file)
              ->resize($normalWidth, $normalHeight)
              ->write($normalPath)
              ->destroy();

      $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
      $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
      // Resize image (normal)
      $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file)
              ->resize($normalLargeWidth, $normalLargeHeight)
              ->write($normalLargePath)
              ->destroy();
    }else {
      // Resize image (main)
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->autoRotate()
              ->resize($mainWidth, $mainHeight)
              ->write($mainPath)
              ->destroy();

      $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
      $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
      // Resize image (normal)
      $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file)
              ->autoRotate()
              ->resize($normalWidth, $normalHeight)
              ->write($normalPath)
              ->destroy();

      $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
      $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
      // Resize image (normal)
      $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file)
              ->autoRotate()
              ->resize($normalLargeWidth, $normalLargeHeight)
              ->write($normalLargePath)
              ->destroy();
    }
    
    
    
    
    
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
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
      $iIconNormal = $filesTable->createFile($normalPath, $params);
      $iMain->bridge($iIconNormal, 'thumb.normal');
      $iIconNormalLarge = $filesTable->createFile($normalLargePath, $params);
      $iMain->bridge($iIconNormalLarge, 'thumb.medium');
      
      $iSquare = $filesTable->createFile($squarePath, $params);
      $iMain->bridge($iSquare, 'thumb.icon');
      
    } catch (Exception $e) {
      // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
      @unlink($normalLargePath);
      @unlink($squarePath);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    // Remove temp files
    @unlink($mainPath);
    @unlink($normalPath);
    @unlink($normalLargePath);
    @unlink($squarePath);
    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
    $this->save();

    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }

    return $this;
  }

  public function getPhotoIndex() {
    return $this->getTable()
                    ->select()
                    ->from($this->getTable(), new Zend_Db_Expr('COUNT(photo_id)'))
                    ->where('album_id = ?', $this->album_id)
                    ->where('`order` < ?', $this->order)
                    ->order('order ASC')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }

  public function getNextPhoto() {
    $table = $this->getTable();
    $select = $table->select()
            ->where('album_id = ?', $this->album_id)
            ->where('`order` > ?', $this->order)
            ->order('order ASC')
            ->limit(1);
    $photo = $table->fetchRow($select);

    if (!$photo) {
      // Get first photo instead
      $select = $table->select()
              ->where('album_id = ?', $this->album_id)
              ->order('order ASC')
              ->limit(1);
      $photo = $table->fetchRow($select);
    }

    return $photo;
  }

  public function getPreviousPhoto() {
    $table = $this->getTable();
    $select = $table->select()
            ->where('album_id = ?', $this->album_id)
            ->where('`order` < ?', $this->order)
            ->order('order DESC')
            ->limit(1);
    $photo = $table->fetchRow($select);

    if (!$photo) {
      // Get last photo instead
      $select = $table->select()
              ->where('album_id = ?', $this->album_id)
              ->order('order DESC')
              ->limit(1);
      $photo = $table->fetchRow($select);
    }

    return $photo;
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

  /**
   * Delete the photo and belongings
   * 
   */
  protected function _postDelete() {

    $photo_id = $this->photo_id;

    $mainPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
    $thumbPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normal');

    // Delete thumb
    if ($thumbPhoto && $thumbPhoto->getIdentity()) {
      try {
        $thumbPhoto->delete();
      } catch (Exception $e) {
        
      }
    }

    // Delete main
    if ($mainPhoto && $mainPhoto->getIdentity()) {
      try {
        $mainPhoto->delete();
      } catch (Exception $e) {
        
      }
    }

    //DELETE PHOTO ENTRY FROM RATING TABLE
    $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitealbum');
    $ratingTable->delete(array('resource_id =?' => $photo_id, 'resource_type =?' => 'album_photo'));

    //DELETE PHOTO ENTRY FROM ITEMOFTHEDAY TABLE
    $itemofthedays = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum');
    $itemofthedays->delete(array('resource_id =?' => $photo_id, 'resource_type =?' => 'album_photo'));

    // Change album cover if applicable
    try {
      if (!empty($this->album_id) && !$this->skipAlbumDeleteHook) {
        $album = $this->getAlbum();
        $nextPhoto = $this->getNextPhoto();
        if (($album instanceof Sitealbum_Model_Album) &&
                ($nextPhoto instanceof Sitealbum_Model_Photo) &&
                (int) $album->photo_id == (int) $this->getIdentity()) {
          $album->photo_id = $nextPhoto->getIdentity();
          $album->save();
        }
        $album->photos_count = $album->photos_count - 1;
        $album->save();
      }

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
        $addlocationsTable = Engine_Api::_()->getDbTable('addlocations', 'sitetagcheckin');
        $addlocationsSelect = $addlocationsTable->select()
                ->from($addlocationsTable->info('name'), array('addlocation_id'))
                ->where('resource_id = ?', $photo_id)
                ->where('resource_type = ?', 'album_photo');
        $addlocations = $addlocationsTable->fetchAll($addlocationsSelect);
        foreach ($addlocations as $addlocation) {
          $addlocation->delete();
        }
      }

      $locationitemsTable = Engine_Api::_()->getDbTable('locationitems', 'seaocore');
      $locationitemsSelect = $locationitemsTable->select()
              ->from($locationitemsTable->info('name'), array('locationitem_id'))
              ->where('resource_id = ?', $photo_id)
              ->where('resource_type = ?', 'album_photo');
      $locationitems = $locationitemsTable->fetchAll($locationitemsSelect);
      foreach ($locationitems as $locationitem) {
        $locationitem->delete();
      }
    } catch (Exception $e) {
      
    }

    parent::_postDelete();
  }

}
