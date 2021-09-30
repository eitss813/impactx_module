<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Customization.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Api_Customization extends Core_Api_Abstract
{

  public function setConstants($values, $fileName = 'sitecorethemeThemeGeneralConstants.css')
  {
    $successfullyAdded = false;
    $directoryPath = APPLICATION_PATH . '/public/seaocore_themes';
    $constantFilePath = $directoryPath . "/$fileName";

    $oldConstants = $this->getConstants($constantFilePath);
    $newConstantString = $this->getUpdatedConstants($oldConstants, $values);

    $isFileExist = @file_exists($constantFilePath);
    if( empty($isFileExist) ) {
      if( !is_dir($directoryPath) ) {
        @mkdir($directoryPath, 0777);
      }
      @chmod($directoryPath, 0777);

      $fh = @fopen($constantFilePath, 'w') or die('Unable to write Constant CSS file; please give the CHMOD 777 recursive permission to the directory /public/, then try again.');
      @fwrite($fh, $newConstantString);
      @fclose($fh);

      @chmod($constantFilePath, 0777);
      $successfullyAdded = true;
    } else {
      if( !is_writable($constantFilePath) ) {
        @chmod($constantFilePath, 0777);
        if( !is_writable($constantFilePath) ) {
          return false;
        }
      }
      $successfullyAdded = @file_put_contents($constantFilePath, $newConstantString);
    }
    return $successfullyAdded;
  }

  public function getConstants($filePath)
  {
    if( !file_exists($filePath) ) {
      return array();
    }

    $constantString = file_get_contents($filePath);
    $constantStringArr = explode(';', $constantString);
    $constantsArr = array();

    foreach( $constantStringArr as $constantStr ) {
      $constant = explode(':', $constantStr);
      if( !empty(trim($constant[0])) ) {
        $constantsArr[trim($constant[0])] = isset($constant[1]) ? trim($constant[1]) : '';
      }
    }
    return $constantsArr;
  }

  public function getUpdatedConstants($oldConstants, $newConstants)
  {
    $constantsArray = array_merge($oldConstants, $newConstants);

    $constants = '';
    foreach( $constantsArray as $constantKey => $constantVAl ) {
      $constantVAl = str_replace('"', '', $constantVAl);
      $constantVAl = str_replace("'", '', $constantVAl);
      $constants .= "$constantKey: " . $constantVAl . ';';
    }
    return $constants;
  }

}