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
class Sitecoretheme_Form_Admin_Settings_Landingpage_Stats extends Engine_Form
{

  public function init()
  {
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Here you can manage the information to be displayed on the Achievement Block. This block can be used in numerous ways. In order to make it more presentable, options to add counts, icons along with the title has been provided. <a title='Preview - Stats Block' href='application/modules/Sitecoretheme/externals/images/screenshots/count-section.png' target='_blank' class='sitecoretheme_icon_view' > </a>"));
    $this->setTitle("Manage Achievement Block");
    $this->setDescription("$description");
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->setAttrib('id', 'form-upload');

    $imgOptions = array('' => 'None');
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

      $this->addElement('Select', 'sitecoretheme_landing_stats_bgimage', array(
        'label' => 'Background Image',
        'description' => 'Select background image for Achievement Block. [Note: You can add new image from "Appearance" > "File & Media Manager".]',
        'multiOptions' => $imgOptions,
        'value' => $coreSettings->getSetting('sitecoretheme.landing.stats.bgimage', ''),
      ));

      $this->addElement('Text', 'sitecoretheme_landing_stats_title1', array(
        'label' => '1st stat title',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.stats.title1', 'Happy Clients'),
      )); 

      $this->addElement('File', 'sitecoretheme_landing_stats_icon1', array(
          'label' => 'Upload 1st stat Icon',
      )); 

      $this->addElement('Text', 'sitecoretheme_landing_stats_count1', array(
        'label' => '1st stat count',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.stats.count1', '7000'),
      ));

      $this->addElement('Text', 'sitecoretheme_landing_stats_title2', array(
        'label' => '2nd stat title',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.stats.title2', 'Launched Products'),
      ));

      $this->addElement('File', 'sitecoretheme_landing_stats_icon2', array(
          'label' => 'Upload 2nd stat Icon',
      )); 

      $this->addElement('Text', 'sitecoretheme_landing_stats_count2', array(
        'label' => '2nd stat count',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.stats.count2', '100'),
      ));

      $this->addElement('Text', 'sitecoretheme_landing_stats_title3', array(
        'label' => '3rd stat title',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.stats.title3', 'Reviews & Ratings'),
      ));

      $this->addElement('File', 'sitecoretheme_landing_stats_icon3', array(
          'label' => 'Upload 3rd stat Icon',
      )); 

      $this->addElement('Text', 'sitecoretheme_landing_stats_count3', array(
        'label' => '3rd stat count',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.stats.count3', '975'),
      ));

      $this->addElement('Text', 'sitecoretheme_landing_stats_title4', array(
        'label' => '4th stat title',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.stats.title4', 'Successful Projects'),
      ));

      $this->addElement('File', 'sitecoretheme_landing_stats_icon4', array(
          'label' => 'Upload Icon',
      )); 

      $this->addElement('Text', 'sitecoretheme_landing_stats_count4', array(
        'label' => '4th stat count',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.stats.count4', '12597'),
      ));

      $this->addDisplayGroup( array('sitecoretheme_landing_stats_title1', 'sitecoretheme_landing_stats_count1', 'sitecoretheme_landing_stats_icon1'), 'sitecoretheme_landing_stats_block1');
      $this->addDisplayGroup( array('sitecoretheme_landing_stats_title2', 'sitecoretheme_landing_stats_count2', 'sitecoretheme_landing_stats_icon2'), 'sitecoretheme_landing_stats_block2');
      $this->addDisplayGroup( array('sitecoretheme_landing_stats_title3', 'sitecoretheme_landing_stats_count3', 'sitecoretheme_landing_stats_icon3'), 'sitecoretheme_landing_stats_block3');
      $this->addDisplayGroup( array('sitecoretheme_landing_stats_title4', 'sitecoretheme_landing_stats_count4', 'sitecoretheme_landing_stats_icon4'), 'sitecoretheme_landing_stats_block4');

      $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'decorators' => array(
          'ViewHelper',
        ),
      ));
  }

}
?>