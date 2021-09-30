<?php

/**
 * SocialEngine
 *
 * @category   Application_Module
 * @package    Siteuseravatar
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteuseravatar_Api_Core extends Core_Api_Abstract
{
  public function setDefaultAvatar($user_id, $title = null)
  {
    $user = Engine_Api::_()->getItemTable('user')->find($user_id)->current();
    $extension = 'png';
    $path = $tmpfile = APPLICATION_PATH_TMP . DS;
    $name = md5(time() . '_' . rand(1000, 9999)) . '.' . $extension;
    $iMainPath = $path . 'm_' . $name;
    $avatarTitle = $title ? $title : $user->getTitle();
    $this->genrateDefaultAvatar($avatarTitle, $iMainPath);
    $user->setPhoto($iMainPath);
    @unlink($iMainPath);
    Engine_Api::_()->getDbtable('avatars', 'siteuseravatar')->add($user, $avatarTitle);
  }

  public function genrateDefaultAvatar($name, $destFile, $params = array())
  {
    require_once APPLICATION_PATH_MOD . '/Siteuseravatar/Avatar/vendor/autoload.php';

    $config = include APPLICATION_PATH_MOD . '/Siteuseravatar/Avatar/config/config.php';
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params = array_merge(
      array(
      'useFirstWord' => $settings->getSetting('siteuseravatar.useFirstWord', 0),
      'useUppercase' => $settings->getSetting('siteuseravatar.useUppercase', 0),
      'chars' => $settings->getSetting('siteuseravatar.chars', 2),
      'fontSize' => $settings->getSetting('siteuseravatar.fontSize', 50),
      'font' => $settings->getSetting('siteuseravatar.font', 'rockwell.ttf'),
      'fontColor' => $settings->getSetting('siteuseravatar.fontColor', '#FFFFFF'),
      'enableBackgroundColor' => $settings->getSetting('siteuseravatar.enableBackgroundColor', 0),
      'backgroundColor' => $settings->getSetting('siteuseravatar.backgroundColor', '#30a7ff'),
      'width' => 720,
      'height' => 720,
      'shape' => 'square'
      ), $params);
    if( !empty($params['useFirstWord']) ) {
      $name = str_replace(' ', '', $name);
    }
    $config['uppercase'] = $params['useUppercase'];
    $config['chars'] = $params['chars'];
    // create your first avatar
    $avatar = new \Laravolt\Avatar\Avatar($config);
    $avatar->create($name);
    $avatar->setDimension($params['width'], $params['height']); // width = 100, height = 200

    $avatar->setFontSize($params['height'] * (0.01 * $params['fontSize']));
    $fontStyle = $params['font'];
    if( strrpos($fontStyle, 'public/admin/') !== false || strrpos($fontStyle, 'public/Siteuseravatar/fonts/') !== false ) {
      $font = APPLICATION_PATH . DS . $fontStyle;
    } else {
      $font = APPLICATION_PATH_MOD . '/Siteuseravatar/Avatar/fonts/' . $fontStyle;
    }
    $avatar->setFont($font);
    if( $params['enableBackgroundColor'] ) {
      $avatar->setBackground($params['backgroundColor']);
       $avatar->setForeground($params['fontColor']);
    }
    $avatar->setShape($params['shape']);
    if( $destFile == 'toBase64' ) {
      return $avatar->toBase64();
    } else {
      $avatar->save($destFile, 100);
    }
  }

}
