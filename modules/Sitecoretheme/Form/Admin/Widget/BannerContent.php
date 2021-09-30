<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BannerContent.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Widget_BannerContent extends Engine_Form {

    public function init() {

        $this
                ->setAttrib('id', 'form-upload');

        $this->addElement('Radio', 'showBanners', array(
            'label' => "Select the banners that you want to show in this widget.",
            'multiOptions' => array(
                1 => 'Show All Banners.',
                0 => 'Select the Banners.'
            ),
            'value' => 1,
            'onclick' => 'showMultiCheckboxBannerOptions()'
        ));

        $listBanner = Engine_Api::_()->getItemTable('sitecoretheme_banner')->getBanners(array('enabled' => 1));
        $listArray = array();
        foreach ($listBanner->toArray() as $banners) {
            $listArray[$banners['banner_id']] = $banners['title'];
        }

        $this->addElement('MultiCheckbox', 'selectedBanners', array(
            'multiOptions' => $listArray,
            'label' => 'Please select the banner.',
        ));

        $this->addElement('Text', 'width', array(
            'label' => 'Enter width for the Banners (Left blank for 100% width)',
            'value' => '',
        ));

        $this->addElement('Text', 'height', array(
            'label' => 'Enter the height for Banners.',
            'value' => 300,
        ));

        $this->addElement('Text', 'speed', array(
            'label' => 'Enter the duration in milliseconds (ms) after which images in the banner should rotate.',
            'value' => 5000,
        ));

        $this->addElement('Radio', 'order', array(
            'label' => 'In which sequence do you want to rotate the images?',
            'multiOptions' => array(
                2 => 'Random',
                1 => 'Descending',
                0 => 'Ascending'
            ),
            'value' => 2,
        ));

        $this->addElement('Text', 'verticalHtmlTitle', array(
            'label' => 'Enter the title text you want to display on banner images.',
            'value' => "Videos that you'd love"
        ));

        $this->addElement('Text', 'verticalHtmlDescription', array(
            'label' => 'Enter the description that you want to display on the banner images.',
            'value' => 'The foremost source to explore and watch videos.'
        ));
    }

}
?>

<script type="text/javascript">
    var form = document.getElementById("form-upload");
    window.addEvent('domready', function () {
        showMultiCheckboxBannerOptions();
    });

    function showMultiCheckboxBannerOptions() {
        if (form.elements["showBanners"].value == 1) {
            $('selectedBanners-wrapper').style.display = 'none';
        } else {
            $('selectedBanners-wrapper').style.display = 'block';
        }
    }
</script>