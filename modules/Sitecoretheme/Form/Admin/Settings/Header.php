<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Header.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Settings_Header extends Engine_Form {

	public function init() {
		$this->setTitle("Manage Header");
		$this->setDescription("Here you can manage settings related to header.");

		$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		$isSitemenuEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu');
		$isStoreproductEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');

		$imgOptions = array('' => 'Text-only (No logo)');
		$imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
		$files = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
		foreach ($files as $file) {
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

		$this->addElement('Select', 'sitecoretheme_header_logo_image', array(
			'label' => 'Select Logo',
			'description' => 'Select the logo for your website. [Note: Logo must be uploaded prior from Admin Panel → Appearance → File & Media Manager.]',
			'multiOptions' => $imgOptions,
			'value' => $coreSettings->getSetting('sitecoretheme.header.logo.image', ''),
		));

		$this->addElement('Select', 'sitecoretheme_header_fixed_logo_image', array(
			'label' => 'Select Alternate Logo',
			'description' => 'Select the alternate logo for your website. [Note: Logo must be uploaded prior from Admin Panel → Appearance → File & Media Manager.]',
			'multiOptions' => $imgOptions,
			'value' => $coreSettings->getSetting('sitecoretheme.header.fixed.logo.image', ''),
		));

		$headerOptions = array(
			'logo' => 'Site Logo',
			'mini_menu' => 'Mini Menu',
			'main_menu' => 'Main Menu ',
			'search_box' => 'Search Box',
			'sociallink' => 'Follows / Social Site Links',
		);

		$deafultHeaderOptions = array(
			'logo',
			'mini_menu',
			'main_menu',
			'search_box',
			'sociallink'
		);

		$this->addElement('MultiCheckbox', 'sitecoretheme_header_loggedin_widgets', array(
			'label' => 'Header Elements for Logged In Members',
			'description' => 'Select the elements you want to display in header section for logged in members.',
			'multiOptions' => $headerOptions,
			'value' => $coreSettings->getSetting('sitecoretheme.header.loggedin.widgets', $deafultHeaderOptions),
		));

		$this->addElement('MultiCheckbox', 'sitecoretheme_header_loggedout_widgets', array(
			'label' => 'Header Elements for Non-Logged In Members',
			'description' => 'Select the options you want to display in header section for non-logged in / guest members.',
			'multiOptions' => $headerOptions,
			'value' => $coreSettings->getSetting('sitecoretheme.header.loggedout.widgets', $deafultHeaderOptions),
		));

		$this->addElement('Radio', 'sitecoretheme_header_menu_position', array(
			'label' => 'Main menu Position',
			'description' => 'How do you want to show main menu?',
			'multiOptions' => array(
				'1' => 'Horizontal',
				'2' => 'Vertical'
			),
			'onchange' => 'setTheMainMenuOptions(this.value)',
			'value' => $coreSettings->getSetting('sitecoretheme.header.menu.position', 1),
		));

		$options = array(
			'1' => 'One lined header excluding social links',
			'2' => 'Two lined header excluding social links'
		);
		if ($isSitemenuEnable) {
			$options[3] = 'Adv Main Menu Base Header';
		}
		$this->addElement('Radio', 'sitecoretheme_header_style', array(
			'label' => 'Header Style',
			'description' => 'Which type of styling to be used for Header?',
			'multiOptions' => $options,
			'onchange' => 'headerStyleBaseOptions()',
			'value' => $coreSettings->getSetting('sitecoretheme.header.style', '2'),
		));


		$this->addElement('Radio', 'sitecoretheme_header_menu_style', array(
			'label' => 'Main Menu Opening Styling',
			'description' => 'Which type of styling the Main menu use for Vertical Main Menu?',
			'multiOptions' => array(
				'Overlay' => 'Overlay',
				'slide' => 'Slide'
			),
			'value' => $coreSettings->getSetting('sitecoretheme.header.menu.style', 'slide'),
		));
		$this->addElement('Radio', 'sitecoretheme_header_menu_alwaysOpen', array(
			'label' => 'Main menu Always Open',
			'description' => 'Do you want to always show the Main Menu?',
			'multiOptions' => array(
				'1' => 'Yes',
				'0' => 'No'
			),
			'value' => $coreSettings->getSetting('sitecoretheme.header.menu.alwaysOpen', 0),
		));

		if (!empty($isSitemenuEnable)) {
			$this->addElement('Radio', 'sitecoretheme_header_sitemenu_fixed', array(
				'label' => 'Fix Header',
				'description' => 'How do you want Header to behave on scroll?',
				'multiOptions' => array(
					'2' => 'Fix header along with main menu',
					'1' => 'Fix header without main menu',
					'0' => 'Don\'t fix the header'
				),
				'value' => $coreSettings->getSetting('sitecoretheme.header.sitemenu.fixed', 0),
			));
		}

		$this->addElement('Radio', 'sitecoretheme_header_menu_fixed', array(
			'label' => 'Fix Header',
			'description' => 'How do you want Header to behave on scroll?',
			'multiOptions' => array(
				'1' => 'Yes, Fix the header',
				'0' => 'Don\'t fix the header'
			),
			'value' => $coreSettings->getSetting('sitecoretheme.header.menu.fixed', 0),
		));


		$this->addElement('Radio', 'sitecoretheme_header_menu_submenu', array(
			'label' => 'Show Plugins Main Navigation Menu',
			'description' => 'Do you want to show plugins main navigation menu with the  corresponding Main Menus?',
			'multiOptions' => array(
				'1' => 'Yes',
				'0' => 'No'
			),
			'value' => $coreSettings->getSetting('sitecoretheme.header.menu.submenu', 1),
		));
		$this->addElement('Radio', 'sitecoretheme_header_menu_icon', array(
			'label' => 'Main menu icons',
			'description' => 'Do you want to show icons in main menu (sub menus will not be affected)?',
			'multiOptions' => array(
				'1' => 'Yes',
				'0' => 'No'
			),
			'value' => $coreSettings->getSetting('sitecoretheme.header.menu.icon', 1),
		));

		$this->addElement('Text', 'sitecoretheme_header_desktop_totalmenu', array(
			'label' => 'Menu Count with "More +" / "3 Dots"',
			'description' => 'How many menu items you want to show? (Note: Other menu items will go under "More +" / "3 Dots" section.)',
			'value' => $coreSettings->getSetting('sitecoretheme.header.desktop.totalmenu', 6),
		));

		//--- Mini Menu Sections
		$this->addElement('Dummy', 'sitecoretheme_header_minimenu_label', array(
			'label' => 'Mini Menu Settings',
		));

		$this->addElement('Radio', 'sitecoretheme_header_minimenu_design', array(
			'label' => 'Mini menu icons / label',
			'description' => 'Select the design type you want to show for your mini menu. (Note: Not all menus support icons. Also, you will be able to configure the icons only for the menus you add from the Menu Editor.)',
			'multiOptions' => array(
				'1' => 'Show icons',
				'0' => 'Show labels with icons'
			),
			'value' => $coreSettings->getSetting('sitecoretheme.header.minimenu.design', 1),
		));
		$this->addElement('radio', 'sitecoretheme_header_display_location', array(
			'label' => "Display Location field",
			'description' => "Do you want to enable 'Location' field, using which users can set their default location?",
			'multiOptions' => array(
				'1' => 'Yes',
				'0' => 'No',
			),
			'value' => $coreSettings->getSetting('sitecoretheme.header.display.location', 0),
		));

		if ($isSitemenuEnable) {
			$this->addElement('Radio', 'sitecoretheme_header_siteminimenu_enable', array(
				'label' => 'Enable Adv. Mini Menu',
				'description' => 'Do you want to enable adv. mini menu widget?',
				'multiOptions' => array(
					'1' => 'Yes',
					'0' => 'No'
				),
				'onchange' => 'sitemenuWidgetOptions()',
				'value' => $coreSettings->getSetting('sitecoretheme.header.siteminimenu.enable', 1),
			));

			if ($isStoreproductEnable) {
				$this->addElement('radio', 'sitecoretheme_header_display_cart', array(
					'label' => 'Do you want to show cart icon?',
					'description' => '(Note: After enabling this setting, caching of Main Menu will not work on your website.)',
					'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
					),
					'value' => $coreSettings->getSetting('sitecoretheme.header.display.cart', 1),
				));
			}
		}

		$this->addElement('Dummy', 'sitecoretheme_header_header_label', array(
			'label' => 'Landing Page header Widget Settings',
		));
		// Get available files
		$this->addElement('Radio', 'sitecoretheme_landing_header_showLogo', array(
			'label' => 'Display Logo',
			'description' => "Do you want to display your website's logo on the top-left side of landing page header?",
			'multiOptions' => array(
				1 => 'Yes',
				0 => 'No'
			),
			'value' => $coreSettings->getSetting('sitecoretheme.landing.header.showLogo', 1),
		));

		$this->addElement('Select', 'sitecoretheme_landing_header_logo', array(
			'label' => 'Select Logo',
			'description' => 'Select the site logo for your website. [Note: You can upload logo from: "Appearance" > "File & Media Manager".]',
			'value' => $coreSettings->getSetting('sitecoretheme.landing.header.logo', ''),
			'multiOptions' => $imgOptions,
		));

		$this->addElement('Select', 'sitecoretheme_landing_header_fixed_logo', array(
			'label' => 'Select Alternate Logo',
			'description' => 'Select the alternate site logo for your website. [Note: Logo must be uploaded prior from Admin Panel → Appearance → File & Media Manager.]',
			'multiOptions' => $imgOptions,
			'value' => $coreSettings->getSetting('sitecoretheme.landing.header.fixed.logo', ''),
		));

		$this->addElement('Radio', 'sitecoretheme_landing_header_showSearch', array(
			'label' => 'Display Search Box',
			'description' => 'Do you want to show search box on the top of landing page header?',
			'multiOptions' => array(
				1 => 'Yes',
				0 => 'No'
			),
			'value' => $coreSettings->getSetting('sitecoretheme.landing.header.showSearch', 1),
		));

		$this->addElement('Text', 'sitecoretheme_landing_header_max', array(
			'label' => 'Menu Items Count',
			'description' => "How many menu items do you want to show in the header?",
			'value' => $coreSettings->getSetting('sitecoretheme.landing.header.max', 6),
		));

		if (Engine_Api::_()->hasModuleBootstrap('sitemenu')) {


			$this->addElement('Text', 'sitecoretheme_landing_header_truncationContent', array(
				'label' => 'Menu Truncation Limit',
				'description' => "Enter the title truncation limit for the menu and categories title.",
				'value' => $coreSettings->getSetting('sitecoretheme.landing.header.truncationContent', 20),
			));
		}

		if (Engine_Api::_()->hasModuleBootstrap('sitemenu') && $isStoreproductEnable) {
			$this->addElement('Radio', 'sitecoretheme_landing_header_showCart', array(
				'label' => 'Show cart icon',
				'description' => 'Do you want to show cart icon? (Note: This setting will work only if you have our Stores / Marketplace - Ecommerce Plugin. After enabling this setting, caching of Main Menu will not work on your website.)',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => $coreSettings->getSetting('sitecoretheme.landing.header.showCart', 1),
			));

			$this->addElement('Radio', 'sitecoretheme_landing_header_showCartOn', array(
				'label' => 'When to show "Cart Icon"',
				'description' => 'When do you want to show "Cart Icon" (if you have our Stores / Marketplace - Ecommerce Plugin)',
				'multiOptions' => array(
					1 => 'Always',
					0 => 'On Scroll'
				),
				'value' => $coreSettings->getSetting('sitecoretheme.landing.header.showCartOn', 1),
			));
		}

//		$this->addElement('Radio', 'sitecoretheme_landing_header_showMiniMenu', array(
//			'label' => 'Display Mini Menu',
//			'description' => "Do you want to show mini menu on the top of landing page header?",
//			'multiOptions' => array(
//				1 => 'Yes',
//				0 => 'No'
//			),
//			'value' => $coreSettings->getSetting('sitecoretheme.landing.header.showMiniMenu', 1),
//		));
//
//		$this->addElement('Radio', 'sitecoretheme_landing_header_minimenu_design', array(
//			'label' => 'Mini menu icons / label',
//			'description' => 'Select the design type you want to show for your mini menu. (Note: Not all menus support icons. Also, you will be able to configure the icons only for the menus you add from the Menu Editor.)',
//			'multiOptions' => array(
//				'1' => 'Show icons',
//				'0' => 'Show labels with icons'
//			),
//			'value' => $coreSettings->getSetting('sitecoretheme.landing.header.minimenu.design', 1),
//		));
//		$this->addElement('radio', 'sitecoretheme_landing_header_display_location', array(
//			'label' => "Display Location field",
//			'description' => "Do you want to enable 'Location' field, using which users can set their default location?",
//			'multiOptions' => array(
//				'1' => 'Yes',
//				'0' => 'No',
//			),
//			'value' => $coreSettings->getSetting('sitecoretheme.landing.header.display.location', 0),
//		));
		$this->addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true
		));
	}

}