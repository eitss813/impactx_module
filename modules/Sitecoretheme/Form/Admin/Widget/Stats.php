<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Stats.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_stats extends Engine_Form
{

  public function init()
  {
    $imgOptions = array('' => 'Default image');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
    $files = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach( $files as $file ) {
      if( $file->isDot() || !$file->isFile() )
        continue;

      $basename = basename($file->getFilename());
      if( !($pos = strrpos($basename, '.')) )
        continue;

      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if( !in_array($ext, $imageExtensions) )
        continue;

      $imgOptions['public/admin/' . $basename] = $basename;
    }

    $this->addElement('Hidden', 'title');

    $this->addElement('Select', 'bg_image', array(
      'label' => 'Background Image',
      'multiOptions' => $imgOptions,
    ));

      $this->addElement('Text', 'stat_title1', array(
        'label' => '1st stat title',
        'value' => ''
      ));

      $this->addElement('Text', 'icon1', array(
        'label' => 'Icon Class (Font Awsome icon class)',
        'value' => ''
      ));

      $this->addElement('Text', 'count1', array(
        'label' => '1st stat count',
        'value' => ''
      ));

      $this->addElement('Text', 'stat_title2', array(
        'label' => '2nd stat title',
        'value' => ''
      ));

      $this->addElement('Text', 'icon2', array(
        'label' => 'Icon Class (Font Awsome icon class)',
        'value' => ''
      ));

      $this->addElement('Text', 'count2', array(
        'label' => '2nd stat count',
        'value' => ''
      ));

      $this->addElement('Text', 'stat_title3', array(
        'label' => '3rd stat title',
        'value' => ''
      ));

      $this->addElement('Text', 'icon1', array(
        'label' => 'Icon Class (Font Awsome icon class)',
        'value' => ''
      ));

      $this->addElement('Text', 'count3', array(
        'label' => '3rd stat count',
        'value' => ''
      ));

      $this->addElement('Text', 'stat_title4', array(
        'label' => '4th stat title',
        'value' => ''
      ));

      $this->addElement('Text', 'icon4', array(
        'label' => 'Icon Class (Font Awsome icon class)',
        'value' => ''
      ));

      $this->addElement('Text', 'count4', array(
        'label' => '4th stat count',
        'value' => ''
      ));

  }

}