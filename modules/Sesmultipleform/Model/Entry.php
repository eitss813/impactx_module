<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Entry.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Model_Entry extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
	
  public function getTable() {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('entries', 'sesmultipleform');
    }
    return $this->_table;
  }
	public function setAttachment($attachment, $id = null){

    if ($attachment instanceof Zend_Form_Element_File){
      $file = $attachment->getFileName();
			$filename = $file;
		}
    else if (is_array($attachment) && !empty($attachment['tmp_name'])){
      $file = $attachment['tmp_name'];
			$filename = $attachment['name'];
		}
    else
      return false;
    
    $params = array(
        'parent_id' => $id,
        'parent_type' => "sesmultipleform_entry",
    );
		if (empty($file))
        return;
    $extension = str_replace(".", "", strrchr($filename, "."));
    if (in_array($extension, array('bmp', 'jpg', 'png', 'psd', 'jpeg'))) {
      $mainName = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . '/' . basename($filename);
      $image = Engine_Image::factory();
      $image->open($file)
              ->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
              ->write($mainName)
              ->destroy();
    }

    try {
      if (in_array($extension, array('bmp', 'jpg', 'png', 'psd', 'jpeg')))
        $photoFile = Engine_Api::_()->storage()->create($mainName, $params);
      else
        $photoFile = Engine_Api::_()->storage()->create($file, $params);
    } catch (Exception $e) {
      if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE) {
        echo $e->getMessage();
        exit();
      }
    }

    //Delete temp file.
    @unlink($mainName);
    return $photoFile;
  	
	}

}
