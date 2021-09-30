<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TwoContentBlocks.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_TwoContentBlocks extends Engine_Form {

	public function init() {


		$sortOptions = array(
			'creation_date' => 'Recently Created',
			'modified_date' => 'Recently Modified',
			'view_count' => 'Most View Count',
			'like_count' => 'Most Liked',
			'comment_count' => 'Most Commented',
			'member_count' => 'Most Members',
			'featured' => 'Show Featured First',
			'sponsored' => 'Show Sponsored First',
		);
		$viewTypeOptions = array(
			'card' => 'Card View',
			'grid' => 'Grid View',
			'list' => 'List View',
			'image' => 'Image View',
		);
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
			'label' => 'Select 1st Content Type',
			'multiOptions' => $contentOptions,
		));
		$this->addElement('Select', 'viewType', array(
			'label' => 'Select View Type for 1st Content',
			'multiOptions' => $viewTypeOptions
		));

		$this->addElement('Select', 'sortBy', array(
			'label' => 'Choose the Sort Criteria for 1st Content',
			'multiOptions' => $sortOptions,
		));
		$this->addElement('Text', 'readMoreText', array(
			'label' => 'Read More Label for 1st Content',
			'value' => 'Read More',
		));

		$this->addElement('Text', 'limit', array(
			'label' => 'No of Items (Max 18) for 1st Content',
//			'multiOptions' => array(3 => '3 items', 6 => '6 items', 9 => '9 items', 12 => '12 items', 15 => '15 items', 18 => '18 items'),
			'value' => 6,
		));


		//

		$this->addElement('Select', 'itemType2', array(
			'label' => 'Select 2nd Content Type',
			'multiOptions' => $contentOptions,
		));

		$this->addElement('Select', 'viewType2', array(
			'label' => 'Select View Type for 2nd Content',
			'multiOptions' => $viewTypeOptions,
		));

		$this->addElement('Select', 'sortBy2', array(
			'label' => 'Choose the Sort Criteria for 2nd Content.',
			'multiOptions' => $sortOptions,
		));

		$this->addElement('Text', 'readMoreText2', array(
			'label' => 'Read More Label for 2nd Content',
			'value' => 'Read More',
		));

		$this->addElement('Text', 'limit2', array(
			'label' => 'No of Items (Max 18) for 2nd Content',
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