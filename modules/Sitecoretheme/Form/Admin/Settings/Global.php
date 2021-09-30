<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Global extends Engine_Form
{

  public function init()
  {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Global Settings")))
      ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("These settings affect all members in your community.")));

    // Get available files
    $imgOptions = array('0' => 'No Image');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach ($it as $file) {
        if ($file->isDot() || !$file->isFile())
            continue;
        $basename = basename($file->getFilename());
        if (!($pos = strrpos($basename, '.')))
            continue;
        $ext = strtolower(ltrim(substr($basename, $pos), '.'));
        if (!in_array($ext, $imageExtensions))
            continue;
        $imgOptions['public/admin/' . $basename] = $basename;
    }

    $this->addElement('Select', 'sitecoretheme_theme_website_body_background_image', array(
        'label' => 'Website\'s Body Background Image',
        'description' => 'Choose the Website\'s Body Background Image for your website. (You can upload a new file from: "Apperance" > "File & Media Manager")',
        'multiOptions' => $imgOptions,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.theme.website.body.background.image', 0),
    ));

    $this->addElement('Radio', 'sitecoretheme_circular_image', array(
      'label' => 'Member\'s Thumbnail Images in Circular Shape',
      'description' => 'Do you want to display thumbnails of member profile picture in circular shape?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.circular.image', 0),
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