<?php

/**
 * SocialEngine
 *
 * @category   Application_Module
 * @package    Siteuseravatar
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Global.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteuseravatar_Form_Admin_Settings_Global extends Engine_Form
{
  public function init()
  {

    $this->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.')
      ->setName('review_global');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Radio', 'useFirstWord', array(
      'label' => 'Avatar Text Format',
      'description' => "Choose the format of Avatar which you want to be used in absence of member profile picture.",
      'multiOptions' => array(
        1 => 'Initials of First Name.  [Eg. Name of the user is John Walker, then avatar initials will be considered from his first name i.e. John.]',
        0 => 'Initials from complete Name. [Eg. Name of the user is Mary Jane Williams, then avatar initials will be considered from every word of her name: ‘Mary’, ‘Jane’ and ‘Williams’.]'
      ),
      'value' => $settings->getSetting('siteuseravatar.useFirstWord', 0),
    ));

    $this->addElement('Select', 'chars', array(
      'label' => 'Count of Avatar Initials',
      'description' => "Set the count of characters from member’s name which will be used as Avatar Initials. [Note: Count can be set from 1 to 4.]",
      'value' => $settings->getSetting('siteuseravatar.chars', 2),
      'multiOptions' => array(
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
      ),
    ));

    $this->addElement('Radio', 'useUppercase', array(
      'label' => 'Enable Uppercase',
      'description' => "Do you want to show avatar initials in uppercase?",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $settings->getSetting('siteuseravatar.useUppercase', 0),
    ));
    $this->addElement('Text', 'fontSize', array(
      'label' => 'Font Size',
      'description' => "Set the font size of avatar initials to be shown instead of member’s profile photo. [Note: Font size is set in percentage. Value of percentage can vary from 25% to 75% of the member profile image section.]",
      'value' => $settings->getSetting('siteuseravatar.fontSize', 50),
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(24)),
        array('LessThan', true, array(76)),
      ),
    ));

    $fontOptions = array(
      'OpenSans-Bold.ttf' => 'OpenSans-Bold',
      'OpenSans-Regular.ttf' => 'OpenSans-Regular',
      'OpenSans-Semibold.ttf' => 'OpenSans-Semibold',
      'cac_champagne.ttf' => 'CAC-Champagne',
      'rockwell.ttf' => 'Rockwell',
    );
    if( file_exists(APPLICATION_PATH . '/public/Siteuseravatar/fonts') ) {
      $it = new DirectoryIterator(APPLICATION_PATH . '/public/Siteuseravatar/fonts/');
      foreach( $it as $file ) {
        if( $file->isDot() || !$file->isFile() )
          continue;
        $basename = basename($file->getFilename());
        if( !($pos = strrpos($basename, '.')) )
          continue;
        $fontOptions['public/Siteuseravatar/fonts/' . $file->getFilename()] = substr($basename, 0, $pos);
      }
    }
    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach( $it as $file ) {
      if( $file->isDot() || !$file->isFile() )
        continue;
      $basename = basename($file->getFilename());
      if( !($pos = strrpos($basename, '.')) )
        continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if( !in_array($ext, array('ttf', 'otf')) )
        continue;
      $fontOptions['public/admin/' . $file->getFilename()] = substr($basename, 0, $pos);
    };
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $URL = $view->baseUrl() . "/admin/files";
    $click = '<a href="' . $URL . '" target="_blank">File & Media Manager</a>';
    $this->addElement('Select', 'font', array(
      'label' => 'Font Style',
      'description' => "Select the font style for the avatar initials. [Note: You can also add new font styles. Go to '" . $click . "' and upload ttf/otf file of the new font style. Now, you can choose the new font style from the dropdown below.]",
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => $fontOptions,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'value' => $settings->getSetting('siteuseravatar.font', 'rockwell.ttf'),
    ));
    $this->font->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    $this->addElement('Radio', 'enableBackgroundColor', array(
      'label' => 'Customize Color',
      'description' => "Do you want to customize the background color for all avatar initials? [Note: If set to No, auto colors will get randomly generated for background of avatar initials.]",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'onclick' => 'useBKColor(this.value)',
      'value' => $settings->getSetting('siteuseravatar.enableBackgroundColor', 0),
    ));

    $this->addElement('Text', 'backgroundColor', array(
      'decorators' => array(array('ViewScript', array(
            'viewScript' => '_formImagerainbowBackgroundColor.tpl',
            'class' => 'form element'
          ))),
      'value' => $settings->getSetting('siteuseravatar.backgroundColor', '#30a7ff'),
    ));
    $this->addElement('Text', 'fontColor', array(
      'decorators' => array(array('ViewScript', array(
            'viewScript' => '_formImagerainbowFontColor.tpl',
            'class' => 'form element'
          ))),
      'value' => $settings->getSetting('siteuseravatar.fontColor', '#FFFFFF'),
    ));

    $this->addElement('Button', 'save', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

}
