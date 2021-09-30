<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Footertemplates.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Footertemplates extends Engine_Form {

	public function init() {


		$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		$this->setTitle("Footer Templates");
		$this->setDescription("Here, you can manage settings related to footer.");
		$it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
		$logoBackgroundOptions = array('application/modules/Sitecoretheme/externals/images/default_footer_bg.png' => 'Default Image');
		$logoOptions = array('' => 'Text-only (No logo)');
		$imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		foreach ($it as $file) {
			if ($file->isDot() || !$file->isFile())
				continue;
			$basename = basename($file->getFilename());
			if (!($pos = strrpos($basename, '.')))
				continue;
			$ext = strtolower(ltrim(substr($basename, $pos), '.'));
			if (!in_array($ext, $imageExtensions))
				continue;
			$logoBackgroundOptions['public/admin/' . $basename] = $basename;
			$logoOptions['public/admin/' . $basename] = $basename;
		}

		$this->addElement('Radio', 'sitecoretheme_footer_background', array(
			'description' => 'Do you want to show footer background for your website.',
			'label' => 'Footer Background',
			'multiOptions' => array(
				2 => 'Yes, Color & Image',
				1 => 'No, Theme Footer Color',
			),
			'onclick' => 'showFooterBackgroundImage(this.value);',
			'value' => $coreSettings->getSetting('sitecoretheme.footer.background', 2),
		));

		$this->addElement('Select', 'sitecoretheme_footer_backgroundimage', array(
			'description' => 'Select background image for footer of your website.',
			'label' => 'Footer Background Image',
			'multiOptions' => $logoBackgroundOptions,
			'value' => $coreSettings->getSetting('sitecoretheme.footer.backgroundimage', 'application/modules/Sitecoretheme/externals/images/default_footer_bg.png'),
		));

		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$baseURL = $view->baseUrl();
		$template_url_1 = $baseURL . '/application/modules/Sitecoretheme/externals/images/screenshots/template-1.png';
		$template_1 = "Template - 1
    " . '<a href="' . $template_url_1 . '" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

		$template_url_2 = $baseURL . '/application/modules/Sitecoretheme/externals/images/screenshots/template-2.png';
		$template_2 = "Template - 2
    " . '<a href="' . $template_url_2 . '" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

		$template_url_3 = $baseURL . '/application/modules/Sitecoretheme/externals/images/screenshots/template-3.png';
		$template_3 = "Template - 3
    " . '<a href="' . $template_url_3 . '" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $template_4 = "Template - 4
    " . '<a title="View Screenshot" class="seaocore_icon_view"></a>';

		$this->addElement('Radio', 'sitecoretheme_footer_templates', array(
			'label' => 'Select Footer Templates',
			'description' => 'Choose the footer template for your website.',
			'multiOptions' => array(
				1 => $template_1,
				2 => $template_2,
				3 => $template_3,
                4 => $template_4
			),
			'escape' => false,
			'onclick' => 'displayFooterHtmlBlock(this.value);',
			'value' => $coreSettings->getSetting('sitecoretheme.footer.templates', 2),
		));

		$this->addElement('Radio', 'sitecoretheme_footer_show_logo', array(
			'description' => 'Do you want to show website’s logo in footer?',
			'label' => 'Show Logo in Footer',
			'multiOptions' => array(
				1 => 'Yes',
				0 => 'No',
			),
			'onclick' => 'showFooterLogo(this.value);',
			'value' => $coreSettings->getSetting('sitecoretheme.footer.show.logo', 1),
		));


		$this->addElement('Select', 'sitecoretheme_footer_select_logo', array(
			'description' => 'Select your website’s logo to be placed in the footer.',
			'label' => 'Footer Logo',
			'multiOptions' => $logoOptions,
			'value' => $coreSettings->getSetting('sitecoretheme.footer.select.logo'),
		));

		$verticalfooterLendingBlockValue = $coreSettings->getSetting('sitecoretheme.footer.lending.block', null);
		if (empty($verticalfooterLendingBlockValue) || is_array($verticalfooterLendingBlockValue)) {
			$verticalfooterLendingBlockValue = 'Be part of our social community, share your experiences with others and make the community an amazing place with your presence.';
		} else {
			$verticalfooterLendingBlockValue = @base64_decode($verticalfooterLendingBlockValue);
		}

		//WORK FOR MULTILANGUAGES START
		$localeMultiOptions = Engine_Api::_()->sitecoretheme()->getLanguageArray();

		$defaultLanguage = $coreSettings->getSetting('core.locale.locale', 'en');
		$total_allowed_languages = Count($localeMultiOptions);
		$slidableBlocks = array();
		if (!empty($localeMultiOptions)) {
			$count = 0;
			foreach ($localeMultiOptions as $key => $label) {
				$lang_name = $label;
				if (isset($localeMultiOptions[$label])) {
					$lang_name = $localeMultiOptions[$label];
				}

				$page_block_field = "sitecoretheme_footer_lending_page_block_$key";

				if (!strstr($key, '_')) {
					$key = $key . '_default';
				}

				$keyForSettings = str_replace('_', '.', $key);
				$verticalfooterLendingBlockValueMulti = $coreSettings->getSetting('sitecoretheme.footer.lending.block.languages.' . $keyForSettings, null);
				if (empty($verticalfooterLendingBlockValueMulti)) {
					$verticalfooterLendingBlockValueMulti = $verticalfooterLendingBlockValue;
				} else {
					$verticalfooterLendingBlockValueMulti = @base64_decode($verticalfooterLendingBlockValueMulti);
				}

				$page_block_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Footer Title in %s"), $lang_name);

				if ($total_allowed_languages <= 1) {
					$page_block_field = "sitecoretheme_footer_lending_page_block";
					$page_block_label = "Footer Title";
				} elseif ($label == 'en' && $total_allowed_languages > 1) {
					$page_block_field = "sitecoretheme_footer_lending_page_block";
				}

				$plugins = "directionality,advlist,autolink,lists,link,image,charmap,print,preview,hr,anchor,"
					. "pagebreak,searchreplace,wordcount,visualblocks,visualchars,code,fullscreen,insertdatetime,"
					. "media,nonbreaking,save,table,contextmenu,directionality,emoticons,paste,textcolor,imagetools,colorpicker,autosave";

				$editorOptions = array(
					'upload_url' => false,
					'menubar' => true,
					'forced_root_block' => false,
					'force_p_newlines' => false,
					'plugins' => $plugins,
					'toolbar1' => "ltr,rtl,undo,redo,removeformat,pastetext,|,code,link,media,image,emoticons,|,bullist,numlist,|,print,preview,fullscreen",
					'toolbar2' => "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,outdent,indent,blockquote",
					'image_advtab' => true,
				);
				$editorOptions['height'] = '500px';

				$this->addElement('Textarea', $page_block_field, array(
					'label' => $page_block_label,
					'description' => "Configure the title which will display on the top of the footer.",
					'attribs' => array('rows' => 24, 'cols' => 80, 'style' => 'width:200px; max-width:200px; height:240px;'),
					'value' => $verticalfooterLendingBlockValueMulti,
					'filters' => array(
						new Engine_Filter_Html(),
						new Engine_Filter_Censor()),
					'editorOptions' => $editorOptions,
				));

				if ($total_allowed_languages > 1 && $count == 0) {
					$this->addElement('Dummy', 'show_hide_link', array(
						'decorators' => array(array('ViewScript', array(
									'viewScript' => '_clickableLink.tpl',
									'class' => 'form element'
								))),
						'ignore' => true,
					));
				}

				if ($page_block_field != "sitecoretheme_footer_lending_page_block_en" && $total_allowed_languages > 1) {
					$slidableBlocks[] = $page_block_field;
				}
			}
			if ($total_allowed_languages > 1) {
				$this->addDisplayGroup($slidableBlocks, 'slideable_language_options');
			}
		}
		//WORK FOR MULTILANGUAGES END
		$tempLogoOptions = array();
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
			$tempLogoOptions['public/admin/' . $basename] = $basename;
		}

		$this->addElement('Text', 'sitecoretheme_mobile', array(
			'description' => 'Please enter your contact number.',
			'label' => 'Contact Number',
			'value' => $coreSettings->getSetting('sitecoretheme.mobile', '+1-777-777-7777')
		));

		$this->addElement('Text', 'sitecoretheme_mail', array(
			'description' => 'Please enter URL of your website.',
			'label' => 'Email Address',
			'value' => $coreSettings->getSetting('sitecoretheme.mail', 'info@test.com')
		));

		$this->addElement('Text', 'sitecoretheme_website', array(
			'description' => 'Please enter URL of your website.',
			'label' => 'Website URL',
			'value' => $coreSettings->getSetting('sitecoretheme.website', 'www.example.com')
		));

		$this->addElement('Radio', 'sitecoretheme_fotter_subscribeus', array(
			'description' => 'Do you want to display "Subscribe Us" form?',
			'label' => 'Subscribe Us',
			'multiOptions' => array(
				1 => 'Yes ',
				0 => 'No',
			),
			'value' => $coreSettings->getSetting('sitecoretheme.fotter.subscribeus', 1),
		));

		$this->addElement('Radio', 'sitecoretheme_twitter_feed', array(
			'description' => 'Do you want to display twitter feeds? [Note: Feeds will be displayed in place of third column of footer menu.]',
			'label' => 'Twitter Feed',
			'multiOptions' => array(
				1 => 'Yes ',
				0 => 'No',
			),
			'onclick' => 'showTwitterFeed(this.value);',
			'value' => $coreSettings->getSetting('sitecoretheme.twitter.feed', 0),
		));

		$this->addElement('Text', 'sitecoretheme_twitterCode', array(
			'description' => 'Paste Embed Code of Twitter.',
			'label' => 'Twitter Embed code',
			'value' => $coreSettings->getSetting('sitecoretheme.twitterCode', '')
		));


		$contentOptions = array(0 => 'None');
		$searchApi = Engine_Api::_()->getApi('search', 'core');
		$availableTypes = $searchApi->getAvailableTypes();
		if (is_array($availableTypes) && count($availableTypes) > 0) {
			foreach ($availableTypes as $index => $type) {
				if ($type === 'sitereview_listing') {
					$listingTypes = Engine_Api::_()->getItemTable('sitereview_listingtype')->getAllListingTypes();
					foreach ($listingTypes as $listingData) {
						$contentOptions[$type . '_' . $listingData->listingtype_id] = ucfirst($listingData->slug_plural);
					}
				} else {
					$contentOptions[$type] = strtoupper('ITEM_TYPE_' . $type);
				}
			}
		}
		$this->addElement('Text', 'sitecoretheme_fotter_content_heading', array(
			'label' => 'Item Listing Heading',
			'description' => 'Enter the heading text you want to display on footer listing?',
			'style' => 'width:350px;',
			'value' => $coreSettings->getSetting('sitecoretheme.fotter.content.heading', 'Latest Registered Members'),
		));
		$this->addElement('Select', 'sitecoretheme_fotter_content_item', array(
			'label' => 'Select Content Type',
			'multiOptions' => $contentOptions,
			'style' => 'width:350px;',
			'value' => $coreSettings->getSetting('sitecoretheme.fotter.content.item', 'user'),
		));
		$this->addElement('Select', 'sitecoretheme_fotter_content_viewType', array(
			'label' => 'Choose the Display View Type',
			'multiOptions' => array(
				'grid' => 'Grid View',
				'list' => 'List View',
			),
			'value' => $coreSettings->getSetting('sitecoretheme.fotter.content.viewType', 'grid'),
		));
		$this->addElement('Text', 'sitecoretheme_fotter_content_limit', array(
			'label' => 'Number of Item',
			'description' => 'Enter the limit of content you want to display on footer listing?',
			'value' => $coreSettings->getSetting('sitecoretheme.fotter.content.limit', 9),
		));
		$this->addElement('Select', 'sitecoretheme_fotter_content_sort', array(
			'label' => 'Choose the Sort Criteria',
			'multiOptions' => array(
				'creation_date' => 'Recently Created',
				'modified_date' => 'Recently Modified',
				'view_count' => 'Most View Count',
				'like_count' => 'Most Liked',
				'comment_count' => 'Most Commented',
        'featured' => 'Show Featured First',
				'sponsored' => 'Show Sponsored First',
			),
			'value' => $coreSettings->getSetting('sitecoretheme.fotter.content.sort', 'creation_date'),
		));

		$this->addElement('MultiCheckbox', 'sitecoretheme_social_links', array(
			'description' => 'Select the social links you want to be available in this block.',
			'label' => 'Social Links',
			'multiOptions' => array(
				"facebooklink" => "Facebook Link",
				"twitterlink" => "Twitter Link",
				"pininterestlink" => "Pinterest Link",
				"youtubelink" => "YouTube Link",
				"linkedinlink" => "LinkedIn Link"
			),
			'value' => $coreSettings->getSetting('sitecoretheme.social.links', array("facebooklink", "twitterlink", "pininterestlink", "youtubelink", "linkedinlink"))
		));
//
//		$this->addElement('Dummy', 'note_description', array(
//			'description' => 'Note: If you leave any URL box blank then that particular social link will not appear in the footer of your website.'
//			)
//		);

		//FOR FACEBOOK SOCIAL LINK
		$this->addElement('Dummy', 'facebook', array(
			'label' => '1. Facebook'
			)
		);

		$this->addElement('Text', 'sitecoretheme_facebook_url', array(
			'label' => 'Url',
			'value' => $coreSettings->getSetting('sitecoretheme.facebook.url', 'http://www.facebook.com/')
			)
		);

		$this->addElement('Text', 'sitecoretheme_facebook_title', array(
			'label' => 'Text on Hover',
			'value' => $coreSettings->getSetting('sitecoretheme.facebook.title', 'Like us on Facebook')
			)
		);
		$this->addDisplayGroup(array('sitecoretheme_facebook_url', 'sitecoretheme_facebook_title'), 'sitecoretheme_facebook_block');

		//WORK FOR TWITTER SOCIAL LINK
		$this->addElement('Dummy', 'twitter', array(
			'label' => '2. Twitter'
			)
		);

		$this->addElement('Text', 'sitecoretheme_twitter_url', array(
			'label' => 'Url',
			'value' => $coreSettings->getSetting('sitecoretheme.twitter.url', 'https://www.twitter.com/')
			)
		);

		$this->addElement('Text', 'sitecoretheme_twitter_title', array(
			'label' => 'Text on Hover',
			'value' => $coreSettings->getSetting('sitecoretheme.twitter.title', 'Follow us on Twitter')
			)
		);
		$this->addDisplayGroup(array('sitecoretheme_twitter_url', 'sitecoretheme_twitter_title'), 'sitecoretheme_twitter_block');

		//WORK FOR PININTEREST SOCIAL LINK
		$this->addElement('Dummy', 'pinterest', array(
			'label' => '3. Pinterest'
			)
		);

		$this->addElement('Text', 'sitecoretheme_pinterest_url', array(
			'label' => 'Url',
			'value' => $coreSettings->getSetting('sitecoretheme.pinterest.url', 'https://www.pinterest.com/')
			)
		);

		$this->addElement('Text', 'sitecoretheme_pinterest_title', array(
			'label' => 'Text on Hover',
			'value' => $coreSettings->getSetting('sitecoretheme.pinterest.title', 'Pinterest')
			)
		);
		$this->addDisplayGroup(array('sitecoretheme_pinterest_url', 'sitecoretheme_pinterest_title'), 'sitecoretheme_pinterest_block');

		//WORK FOR YOUTUBE SOCIAL LINK
		$this->addElement('Dummy', 'youtube', array(
			'label' => '4. YouTube'
			)
		);

		$this->addElement('Text', 'sitecoretheme_youtube_url', array(
			'label' => 'Url',
			'value' => $coreSettings->getSetting('sitecoretheme.youtube.url', 'http://www.youtube.com/')
			)
		);

		$this->addElement('Text', 'sitecoretheme_youtube_title', array(
			'label' => 'Text on Hover',
			'value' => $coreSettings->getSetting('sitecoretheme.youtube.title', 'Youtube')
			)
		);
		$this->addDisplayGroup(array('sitecoretheme_youtube_url', 'sitecoretheme_youtube_title'), 'sitecoretheme_youtube_block');

		//WORK FOR LinkedIn SOCIAL LINK
		$this->addElement('Dummy', 'linkedin', array(
			'label' => '5. LinkedIn'
			)
		);

		$this->addElement('Text', 'sitecoretheme_linkedin_url', array(
			'label' => 'Url',
			'value' => $coreSettings->getSetting('sitecoretheme.linkedin.url', 'https://www.linkedin.com/')
			)
		);

		$this->addElement('Text', 'sitecoretheme_linkedin_title', array(
			'label' => 'Text on Hover',
			'value' => $coreSettings->getSetting('sitecoretheme.linkedin.title', 'LinkedIn')
			)
		);
		$this->addDisplayGroup(array('sitecoretheme_linkedin_url', 'sitecoretheme_linkedin_title'), 'sitecoretheme_linkedin_block');


		$this->addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'decorators' => array(
				'ViewHelper',
			),
		));
	}

}