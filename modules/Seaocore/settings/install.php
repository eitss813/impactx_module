<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Installer extends Engine_Package_Installer_Module
{

  function onInstall()
  {
    $db = $this->getDb();
    $db->query("INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `enabled`, `custom`, `order`) VALUES ('seaocore_mini_friend_request', 'seaocore', 'Friend Requests', 'Seaocore_Plugin_Menus', '', 'core_mini', '1', '0', '3');");
    /*$db->query("INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `enabled`, `custom`, `order`) VALUES ('seaocore_mini_home', 'seaocore', 'Home', 'Seaocore_Plugin_Menus', '{\"route\":\"user_general\",\"action\":\"home\",\"icon\":\"fa-home\"}', 'core_mini', '1', '0', '1');");*/
    //DELETED THE "Plugins Information" TAB FROM ADMIN PANEL OF SEAO CORE PLUGIN
//    $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'seaocore_admin_info';");
//    $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'seaocore_admin_main_integrated'");
    //END FOR UPDATE SEAO IN THE MENU OF THE ADMIN PANL.
    parent::onInstall();
  }

  private function checkVersion($databaseVersion, $checkDependancyVersion)
  {
    $f = $databaseVersion;
    $s = $checkDependancyVersion;
    if( strcasecmp($f, $s) == 0 )
      return -1;

    $fArr = explode(".", $f);
    $sArr = explode('.', $s);
    if( count($fArr) <= count($sArr) )
      $count = count($fArr);
    else
      $count = count($sArr);

    for( $i = 0; $i < $count; $i++ ) {
      $fValue = $fArr[$i];
      $sValue = $sArr[$i];
      if( is_numeric($fValue) && is_numeric($sValue) ) {
        if( $fValue > $sValue )
          return 1;
        elseif( $fValue < $sValue )
          return 0;
        else {
          if( ($i + 1) == $count ) {
            return -1;
          } else
            continue;
        }
      }
      elseif( is_string($fValue) && is_numeric($sValue) ) {
        $fsArr = explode("p", $fValue);

        if( $fsArr[0] > $sValue )
          return 1;
        elseif( $fsArr[0] < $sValue )
          return 0;
        else {
          return 1;
        }
      } elseif( is_numeric($fValue) && is_string($sValue) ) {
        $ssArr = explode("p", $sValue);

        if( $fValue > $ssArr[0] )
          return 1;
        elseif( $fValue < $ssArr[0] )
          return 0;
        else {
          return 0;
        }
      } elseif( is_string($fValue) && is_string($sValue) ) {
        $fsArr = explode("p", $fValue);
        $ssArr = explode("p", $sValue);
        if( $fsArr[0] > $ssArr[0] )
          return 1;
        elseif( $fsArr[0] < $ssArr[0] )
          return 0;
        else {
          if( $fsArr[1] > $ssArr[1] )
            return 1;
          elseif( $fsArr[1] < $ssArr[1] )
            return 0;
          else {
            return -1;
          }
        }
      }
    }
  }

}
?>
