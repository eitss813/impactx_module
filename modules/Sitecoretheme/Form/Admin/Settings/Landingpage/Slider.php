<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Slider.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Landingpage_Slider extends Engine_Form
{

  public function init()
  {
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Here you can manage slider appearing on landing page. You have the privilege to select preferred images for the slider. To upload new Slider images please go to Slider Images >> Landing Page Slider Images section in the admin panel of this theme. <a title='Preview - Slider' href='application/modules/Sitecoretheme/externals/images/screenshots/banner.png' target='_blank' class='sitecoretheme_icon_view' > </a>"));
    $this->setTitle("Manage Slider");
    $this->setDescription("$description");

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->setAttrib('id', 'form-upload');
    $this->addElement('Radio', 'sitecoretheme_landing_slider_images', array(
      
      'label' => 'Slider Images',
      'description' => "Select images that you want to show in image slider on landing page??",
      'multiOptions' => array(
        1 => 'Show All Images.',
        0 => 'Select particular images.'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.images', 1),
      'onclick' => 'showMultiCheckboxImageOptions()'
    ));

    $listImage = Engine_Api::_()->getItemTable('sitecoretheme_image')->getImages(array('enabled' => 1));
    $listArray = array();
    foreach( $listImage->toArray() as $images ) {
      $listArray[$images['image_id']] = $images['title'];
    }

    $this->addElement('MultiCheckbox', 'sitecoretheme_landing_slider_selectedImages', array(
      'multiOptions' => $listArray,
      'label' => '',
      'description' => 'Please select images for Slider.',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.selectedImages', ''),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_slider_height', array(
      'label' => 'Slider Height',
      'description' => 'Enter height for the images.',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.height', 583),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_slider_speed', array(
      'label' => 'Slider Speed',
      'description' => 'Enter time delay for images to rotate in image slider (in milliseconds (ms)).',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.speed', 5000),
    ));

    $this->addElement('Radio', 'sitecoretheme_landing_slider_order', array(
      'label' => 'Order for Images',
      'description' => 'In which sequence do you want to rotate the images?',
      'multiOptions' => array(
        2 => 'Randomly',
        1 => 'Descending',
        0 => 'Ascending'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.order', 2),
    ));
		
		$opacityOptions = array();
		for ($i = 75; $i >= 0; $i--) {
			$opacity = $i / 100;
			$opacityOptions[$i] = $opacity;
		}
		$this->addElement('Select', 'sitecoretheme_landing_slider_overlay_opacity', array(
			'label' => 'Slider Overlay Opacity',
			'multiOptions' => $opacityOptions,
			'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.overlay.opacity', '20'),
		));

    $this->addElement('Text', 'sitecoretheme_landing_slider_bannerTitle', array(
      'label' => 'Slider Title',
      'description' => 'Enter the title text you want to display on the image slider?',
      'style' => 'width:350px;',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.bannerTitle', 'Explore the world with us'),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_slider_description1', array(
      'label' => 'Moving Text on Slider',
      'description' => 'Enter the text you want to display on the image slider. These texts will be shown as moving texts. [Note: Maximum three lines can be added.]',
      'style' => 'width:350px;',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.description1', 'A true social community is when you feel connected and responsible for what happens around.'),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_slider_description2', array(
      'label' => '',
      'description' => '',
      'style' => 'width:350px;',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.description2', ''),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_slider_description3', array(
      'label' => '',
      'description' => '',
      'style' => 'width:350px;',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.description3', ''),
    ));


    $this->addElement('Radio', 'sitecoretheme_landing_slider_showButton', array(
      'label' => 'Sign In & Sign Up Buttons',
      'description' => "Do you want to show Sign In and Sign Up buttons on this image slider?",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.showButton', 1),
    ));

    $imgOptions = array('' => 'None', 'application/modules/Sitecoretheme/externals/images/dating-front.png' => 'Default Image');
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

    $this->addElement('Select', 'sitecoretheme_landing_slider_frontImage_src', array(
      'label' => 'Select Front Image',
      'description' => 'Select the image for slider. [Note: Front image must be uploaded prior from Admin Panel → Appearance → File & Media Manager.]',
      'multiOptions' => $imgOptions,
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.frontImage.src', ''),
    ));
    $this->addElement('Select', 'sitecoretheme_landing_slider_frontImage_position', array(
      'label' => 'Front Image Display',
      'description' => "Select the side where you want to show the front image?",
      'multiOptions' => array(
        'left' => 'Left Side',
        'right' => 'Right Side',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.frontImage.position', 'left'),
    ));
    $this->addElement('Select', 'sitecoretheme_landing_slider_form_type', array(
      'label' => 'Show the Form',
      'description' => "Select the form which you want to display?",
      'multiOptions' => array(
        0 => 'None',
				'user_login_signup' => 'User Login & Signup Forms',
        'user_login' => 'User Login Form',
        'user_search' => 'User Search Form',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.form.type', 0),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_slider_form_heading', array(
      'label' => 'Slider Form Heading',
      'description' => 'Enter the heading text you want to display on the form in image slider?',
      'style' => 'width:350px;',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.form.heading', 'Welcome to community!'),
    ));

    $this->addElement('Select', 'sitecoretheme_landing_slider_form_position', array(
      'label' => 'Form Display',
      'description' => "Select the side where you want to show the form?",
      'multiOptions' => array(
        'left' => 'Left Side',
        'right' => 'Right Side',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.form.position', 'left'),
    ));
		
    $this->addElement('Select', 'sitecoretheme_landing_slider_form_style', array(
      'label' => 'Form Style',
      'description' => "Select the style for showing the form section?",
      'multiOptions' => array(
        'filled' => 'Filled',
        'transparent' => 'Transparent',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.form.style', 'filled'),
    ));

    $contentOptions = array(0 => 'None');
    $searchApi = Engine_Api::_()->getApi('search', 'core');
    $availableTypes = $searchApi->getAvailableTypes();
    if( is_array($availableTypes) && count($availableTypes) > 0 ) {
      foreach( $availableTypes as $index => $type ) {
        if( $type === 'sitereview_listing' ) {
          $listingTypes = Engine_Api::_()->getItemTable('sitereview_listingtype')->getAllListingTypes();
          foreach( $listingTypes as $listingData ) {
            $contentOptions[$type . '_' . $listingData->listingtype_id] = ucfirst($listingData->slug_plural);
          }
        } else {
          $contentOptions[$type] = strtoupper('ITEM_TYPE_' . $type);
        }
      }
    }

    $this->addElement('Select', 'sitecoretheme_landing_slider_form_bottom_item', array(
      'label' => 'Select Content Type',
      'multiOptions' => $contentOptions,
      'style' => 'width:350px;',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.form.bottom.item', 'user'),
    ));
    $this->addElement('Select', 'sitecoretheme_landing_slider_form_bottom_sort', array(
      'label' => 'Choose the Sort Criteria',
      'multiOptions' => array(
        'creation_date' => 'Recently Created',
        'modified_date' => 'Recently Modified',
        'view_count' => 'Most View Count',
        'like_count' => 'Most Liked',
        'comment_count' => 'Most Commented',
      ),
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.form.bottom.sort', 'creation_date'),
    ));

    $this->addElement('Text', 'sitecoretheme_landing_slider_form_bottom_heading', array(
      'label' => 'Slider Form Item Listing Heading',
      'description' => 'Enter the heading text you want to display on the form in image slider?',
      'style' => 'width:350px;',
      'value' => $coreSettings->getSetting('sitecoretheme.landing.slider.form.bottom.heading', 'Latest Registered Members'),
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

<script type="text/javascript">
  var form = document.getElementById("form-upload");
  window.addEvent('domready', function () {
    showMultiCheckboxImageOptions();
  });

  function showMultiCheckboxImageOptions() {
    if (form.elements["sitecoretheme_landing_slider_images"].value == 1) {
      $('sitecoretheme_landing_slider_selectedImages-wrapper').style.display = 'none';
    } else {
      $('sitecoretheme_landing_slider_selectedImages-wrapper').style.display = 'block';
    }
  }
	
	function toggleFiledsOfSearchFields() {
		
	}

</script>