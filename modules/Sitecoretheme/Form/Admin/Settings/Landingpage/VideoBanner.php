<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoBanner.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Landingpage_VideoBanner extends Engine_Form
{

  public function init()
  {
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Here you can configure settings related to the video banner. <a title='Preview - Highlight Block' href='application/modules/Sitecoretheme/externals/images/screenshots/VideoBanner-section.png' target='_blank' class='sitecoretheme_icon_view' > </a>"));

    $this->setTitle("Video Banner Settings");
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

    $this->addElement('Select', 'sitecoretheme_landing_videobanner_image', array(
      'label' => 'Select Center Image',
      'multiOptions' => $imgOptions,
      'value' => $coreSettings->getSetting('sitecoretheme.landing.videobanner.image', ''),
    ));

    $this->addElement('Radio', 'sitecoretheme_landing_videobanner_videoType', array(
      'label' => 'Attach Video',
      'description' => "Do you want to attach a video along with centered image?",
      'multiOptions' => array(
        1 => 'Video URL',
        0 => 'Video Embed Code'
      ),
      'onclick' => 'showVideoUrl()',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.videobanner.videoType', 0),
    ));

    $this->addElement('Textarea', 'sitecoretheme_landing_videobanner_videoEmbed', array(
      'label' => 'Video Embed Code',
      'description' => "Please enter embed code of video.",
      'value' => $coreSettings->getSetting('sitecoretheme.landing.videobanner.videoEmbed', ''),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_videobanner_videoUrl', array(
      'label' => 'Video URL',
      'description' => "Please enter the URL of video. [Note: Please verify entered URL will open / play in iframe].",
      'value' => $coreSettings->getSetting('sitecoretheme.landing.videobanner.videoUrl', ''),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_videobanner_heading', array(
      'label' => 'Video Heading',
      'description' => "Please enter heading for video.",
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 256)),
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.videobanner.heading', ''),
    ));
    $name = 'sitecoretheme_landing_videobanner_color';
    $labelString = 'Video Heading Color';
    $this->addElement('Text', $name, array(
      'label' => $labelString,
      'decorators' => array(array('ViewScript', array(
            'viewScript' => '_formColor.tpl',
            'name' => $name,
            'description' => 'Select color for video heading',
            'value' => $coreSettings->getSetting('sitecoretheme.landing.videobanner.color' , '#ffffff'),
            'label' => $labelString
          )))
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