<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ContentBlocks.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_ContentBlocks extends Engine_Form {

	public function init() {



		$this->addElement('Select', 'viewType', array(
			'label' => 'Select View Type',
			'multiOptions' => array(
				1 => 'Card View',
				2 => 'Carousal View',
				3 => 'Album View',
				4 => 'Grid Card View',
				5 => 'Carousal Card View',
				6 => 'List View',
				7 => 'Image View',
				8 => 'Left One Large and other small Listing',
				9 => 'Right One Large and other small Listing',
				10 => 'Round Image View'
			),
		));
		$this->addElement('Text', 'title', array(
			'label' => 'Heading',
			'value' => '',
		));
		$this->addElement('Textarea', 'description', array(
			'label' => 'Description',
			'value' => '',
		));
		$contentOptions = array();
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

		$this->addElement('Select', 'itemType', array(
			'label' => 'Select Content Type',
			'multiOptions' => $contentOptions,
		));
		$this->addElement('Select', 'sortBy', array(
			'label' => 'Choose the Sort Criteria for this widget.',
			'multiOptions' => array(
				'creation_date' => 'Recently Created',
				'modified_date' => 'Recently Modified',
				'view_count' => 'Most View Count',
				'like_count' => 'Most Liked',
				'comment_count' => 'Most Commented',
				'member_count' => 'Most Members',
				'featured' => 'Show Featured First',
				'sponsored' => 'Show Sponsored First',
			),
		));
		$this->addElement('Text', 'readMoreText', array(
			'label' => 'Read More Label',
			'value' => 'Read More',
		));

		$this->addElement('Text', 'limit', array(
			'label' => 'No of Items (Max 18)',
//			'multiOptions' => array(3 => '3 items', 6 => '6 items', 9 => '9 items', 12 => '12 items', 15 => '15 items', 18 => '18 items'),
			'value' => 6,
		));


		$imgOptions = array('' => 'No Image');
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

		$this->addElement('Select', 'background_image', array(
			'label' => 'Select Background Image',
			'multiOptions' => $imgOptions,
		));

		$this->addElement('Text', 'heading_color', array(
			'label' => 'Heading Color',
			'decorators' => array(array('ViewScript', array(
						'viewScript' => 'application/modules/Sitecoretheme/views/scripts/_formColor.tpl',
						'class' => 'form element',
						'name' => 'heading_color',
						'label' => 'Heading Color',
					)))
		));

		$this->addElement('Text', 'background_overlay_color', array(
			'label' => 'Background Overlay Color',
			'decorators' => array(array('ViewScript', array(
						'viewScript' => 'application/modules/Sitecoretheme/views/scripts/_formColor.tpl',
						'class' => 'form element',
						'name' => 'background_overlay_color',
						'label' => 'Background Overlay Color',
					)))
		));
		$opacityOptions = array();
		for ($i = 100; $i >= 0; $i--) {
			$opacity = $i / 100;
			$opacityOptions[$i] = $opacity;
		}
		$this->addElement('Select', 'background_overlay_opacity', array(
			'label' => 'Background Overlay Opacity',
			'multiOptions' => $opacityOptions,
		));
		$this->addElement('Dummy', 'background_image_preview', array(
			'decorators' => array(array('ViewScript', array(
						'viewScript' => 'application/modules/Sitecoretheme/views/scripts/_formImagePreview.tpl',
						'bindPreviews' => array('background_image'),

					)))
		));
	}

}