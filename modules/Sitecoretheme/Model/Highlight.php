<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Highlight.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Model_Highlight extends Core_Model_Item_Abstract
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

    $thumb_file = $path . '/in_' . $name;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(64, 64)
      ->write($thumb_file)
      ->destroy();
    try {
      $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
        'parent_type' => 'sitecoretheme_highlight',
        'parent_id' => $this->getIdentity()
      ));
      // Remove temp file
      @unlink($thumb_file);
    } catch( Exception $e ) {
      
    }

    $this->file_id = $thumbFileRow->file_id;
    $this->save();

    return $this;
  }

}