<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Block.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Model_Block extends Core_Model_Item_Abstract
{

  // Properties
  protected $_parent_type = null;
  protected $_searchTriggers = array();
  protected $_parent_is_owner = false;

  
  protected $_disableHooks = true;

  // General
  
  public function getCTAHref($button = 'cta_1')
  {

    $params = $this->params;
    if( !empty($params[$button.'_uri']) ) {
      return $params[$button.'_uri'];
    }
  }

  public function getDescription()
  {

    return $this->body;
  }

  public function getCTALabel($button = 'cta_1')
  {
    return isset($this->params[$button.'_label']) ? $this->params[$button.'_label'] : '';
  }

  public function getVideoURL()
  {
     return !empty($this->params['video_uri']) ? $this->params['video_uri'] : '';
  }
  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
      $fileName = $file;
    } elseif( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } elseif( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new Core_Model_Exception('invalid argument passed to setPhoto');
    }

    if( !$fileName ) {
      $fileName = basename($file);
    }

    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

    $params = array(
      'parent_type' => 'sitecoretheme_block',
      'parent_id' => $this->getIdentity(),
      'name' => $fileName,
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->write($mainPath)
      ->destroy();
    // Store
    $iMain = $filesTable->createSystemFile($mainPath);

    // Remove temp files
    @unlink($mainPath);
    $this->photo_id = $iMain->file_id;
    $this->save();
    return $this;
  }

}