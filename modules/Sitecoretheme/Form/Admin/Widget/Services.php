<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Services.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_Services extends Engine_Form {

	public function init() {
		$contentOptions = array(
			'_cards _round_icons' => 'Card View With Rounded icons',
			'_cards' => 'Card View With Normal icons',
			'_round_icons' => 'Normal View With Rounded icons',
			'' => 'Normal View',
		);

		$this->addElement('Select', 'viewType', array(
			'label' => 'Select Content Type',
			'multiOptions' => $contentOptions,
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
		$this->addElement('Dummy', 'background_image_preview', array(
			'decorators' => array(array('ViewScript', array(
						'viewScript' => 'application/modules/Sitecoretheme/views/scripts/_formImagePreview.tpl',
						'bindPreviews' => array('background_image'),
					)))
		));
	}

}