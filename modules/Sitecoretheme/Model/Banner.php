<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Banner.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Model_Banner extends Core_Model_Item_Abstract
{

  protected $_searchTriggers = false;

  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Engine_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';


    $image = Engine_Image::factory();
    $thumb_file = $path . '/in_' . $name;
    $image->open($file)
      ->autoRotate()
      ->resize(140, 160)
      ->write($thumb_file)
      ->destroy();

    $thumb_file_main = $path . '/m_' . $name;
    $image = Engine_Image::factory();
    $image->open($file)
      ->autoRotate()
      ->resize(1600, 500)
      ->write($thumb_file_main)
      ->destroy();


    try {
      $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
        'parent_type' => $this->getType(),
        'parent_id' => $this->getIdentity()
      ));

      $thumbFileRowMain = Engine_Api::_()->storage()->create($thumb_file_main, array(
        'parent_type' => $this->getType(),
        'parent_id' => $this->getIdentity()
      ));

      // Remove temp file
      @unlink($thumb_file);
      @unlink($thumb_file_main);
    } catch( Exception $e ) {
      
    }

    $this->icon_id = $thumbFileRow->file_id;
    $this->file_id = $thumbFileRowMain->file_id;

    $this->save();

    return $this;
  }

}