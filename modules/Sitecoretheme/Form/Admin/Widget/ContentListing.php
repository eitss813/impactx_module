<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ContentListing.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_ContentListing extends Engine_Form {

	public function init() {
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
		$this->addElement('Select', 'limit', array(
			'label' => 'No of Items',
			'multiOptions' => array(3 => '3 items', 6 => '6 items', 9 => '9 items', 12 => '12 items', 15 => '15 items', 18 => '18 items'),
			'value' => 6,
		));

		$this->addElement('Select', 'crousalView', array(
			'label' => 'Show in Crousal',
			'multiOptions' => array(
				0 => 'No',
				1 => 'Yes',
			),
			'order' => 4
		));
	}

}