<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CategorieBannerContent.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_Widget_CategorieBannerContent extends Engine_Form {

    public function init() {

        $this
                ->setAttrib('id', 'form-upload');

        // Get available files
        $logoOptions = array('' => 'Text-only (No logo)');
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
            $logoOptions['public/admin/' . $basename] = $basename;
        }

        $this->addElement('Select', 'logo', array(
            'label' => 'Choose the background image. (You can upload a new file from: "Layout" > "File & Media Manager")',
            'multiOptions' => $logoOptions,
        ));

        $this->addElement('Text', 'height', array(
            'label' => " Enter the background image height.",
            'value' => 555
        ));
        $this->addElement('Text', 'categoryHeight', array(
            'label' => " Enter the category image height.",
            'value' => 400
        ));
        $this->addElement('Radio', 'fullWidth', array(
            'label' => "Do you want to display the slideshow in full width ?",
            'multiOptions' => array(1 => 'Yes', 0 => 'No'),
            'value' => 0
        ));
        $this->addElement('Radio', 'showExplore', array(
            'label' => "Do you want to show Explore Now button ?",
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
            'label' => '',
            'order' => 10001,
        ));
    }

}
?>
<script type="text/javascript">
    function setHiddenValues(element_id) {
        $('hidden_' + element_id).value = $(element_id).value;
    }
</script>
