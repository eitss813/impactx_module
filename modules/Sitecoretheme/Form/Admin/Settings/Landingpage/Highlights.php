<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Highlights.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Landingpage_Highlights extends Engine_Form
{

  public function init()
  {
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Here you can configure settings related to the highlights block. <a title='Preview - Highlight Block' href='application/modules/Sitecoretheme/externals/images/screenshots/Aided-section.png' target='_blank' class='sitecoretheme_icon_view' > </a>"));
    
    $this->setTitle("General Settings");
    $this->setDescription("$description");
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->setAttrib('id', 'form-upload');

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

      $this->addElement('Select', 'sitecoretheme_landing_highlights_image', array(
        'label' => 'Select Center Image',
        'multiOptions' => $imgOptions,
        'value' => $coreSettings->getSetting('sitecoretheme.landing.highlights.image', ''),
      ));

      $this->addElement('Radio', 'sitecoretheme_landing_highlights_attachVideo', array(
        'label' => 'Attach Video',
        'description' => "Do you want to attach a video along with centered image?",
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'onclick' => 'showVideoUrl()',
        'value' => $coreSettings->getSetting('sitecoretheme.landing.highlights.attachVideo', 0),
      ));

      $this->addElement('Textarea', 'sitecoretheme_landing_highlights_videoEmbed', array(
        'label' => 'Video Embed Code',
        'description' => "Please enter embed code of video.",
        'value' => $coreSettings->getSetting('sitecoretheme.landing.highlights.videoEmbed', ''),
      ));

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