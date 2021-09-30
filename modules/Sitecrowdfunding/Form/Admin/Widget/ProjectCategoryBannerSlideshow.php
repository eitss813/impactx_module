<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectCategoryBannerSlidesshow.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Widget_ProjectCategoryBannerSlideshow extends Engine_Form {

    public function init() {

        $this->setAttrib('id', 'form-upload');

       //    Get available files
        $logoOptions = array('' => 'Text-only (No logo)');
        $imageExtensions = array('gif', 'jpg', 'jpeg','png', 'BannerConpng');

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
            $logoOptions['public/admin/' . $basename] = $basename;
        }

        $this->addElement('Select', 'logo', array(
            'label' => 'Choose the background image. (You can upload a new file from: "Layout" > "File & Media Manager")',
            'multiOptions' => $logoOptions,
        ));

        $this->addElement('Text', 'height', array(
            'label' => "Enter the background image height.",
            'value' => 555
        ));
        $this->addElement('Text', 'categoryHeight', array(
            'label' => "Enter the category image height.",
            'value' => 400
        ));
        $this->addElement('Radio', 'fullWidth', array(
            'label' => "Do you want to display the slideshow in full width?",
           'multiOptions' => array(1 => 'Yes', 0 => 'No'),
            'value' => 0
        ));

        $categories = Engine_Api::_()->getDbtable('Categories', 'sitecrowdfunding')->
        getCategories(array('category_id', 'category_name'),null,0,0,1);
        $categories_prepared = array();
        if (count($categories) != 0) {
            foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = $category->category_name;
                }
                $this->addElement('MultiCheckbox', 'category_id', array(
                    'label' => "Choose categories ",
                    'description' => 'Select the categories that you want to display in the slideshow.',
                    'multiOptions' => $categories_prepared,
                    'RegisterInArrayValidator' => false,
                ));
            }
        $this->addElement('Radio', 'showExplore', array(
            'label' => "Do you want to show Explore Now button?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
        $this->addElement('Text', 'titleTruncation', array(
            'label' => "Truncation limit for category title.",
            'value' => 100,
        ));
        $this->addElement('Text', 'taglineTruncation', array(
            'label' => "Truncation limit for category tagline.",
            'value' => 200,
        ));
        $this->addElement('hidden', 'nomobile', array(
            'label' => ''
        ));
    }

}
?>

