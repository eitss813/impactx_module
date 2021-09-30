<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Template.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Admin_Template extends Engine_Form {

    public function init() {

        $this->setTitle('Layout Template Settings')
                ->setDescription('Below, you can select the desired pages and change their layout upon submission of this form.')
                ->setAttrib('name', 'template');

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $defaultProfile = "Template 1
    " . '<a href="http://demo.socialengineaddons.com/albums/view/924?otherView=tmp22" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="http://netdna.socialengineaddons.com/sites/default/files/AdvAlbums_5.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $template2Profile = "Template 2
    " . '<a href="http://demo.socialengineaddons.com/albums/view/293" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://www.socialengineaddons.com/sites/default/files/images/Album-View-Page.png" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        if(!Engine_Api::_()->hasModuleBootstrap('sitecontentcoverphoto'))
        $template2Profile .= ' The <a href="http://www.socialengineaddons.com/socialengine-content-profiles-cover-photo-banner-site-branding-plugin" target="blank">Content Profiles - Cover Photo, Banner & Site Branding Plugin </a> is not installed on your site. If you want to use cover photo feature of this plugin then please purchase this plugins from <a href="http://www.socialengineaddons.com/socialengine-content-profiles-cover-photo-banner-site-branding-plugin" target="blank">here</a>.';

//GET DECORATORS
        $this->addElement('Radio', 'sitealbum_profiletemplate', array(
            'label' => 'Album Profile Page',
            'description' => 'Choose from below the template for Album Profile page of your site.',
            'multiOptions' => array(
                'default' => $defaultProfile,
                'template2' => $template2Profile,
            ),
            'escape' => false,
            'value' => $coreSettings->getSetting('sitealbum.profiletemplate', 'default'),
        ));
        

        $albumHome = "
    " . '<a href="https://www.socialengineaddons.com/sites/default/files/images/Albums-Home-Page.png" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $albumBrowse = "
    " . '<a href="https://www.socialengineaddons.com/sites/default/files/images/Browse-Albums-Page.png" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $albumPhotosBrowse = "
    " . '<a href="https://www.socialengineaddons.com/sites/default/files/images/Browse-Photos-Page.png" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $albumLocations = "
    " . '<a href="http://demo.socialengineaddons.com/albums/map" title="View" class="seaocore_icon_view" target="_blank"></a>';

        $albumPinboard = "
    " . '<a href="http://demo.socialengineaddons.com/albums/pinboard" title="View" class="seaocore_icon_view" target="_blank"></a>';

        $albumMyPage = "
    " . '<a href="https://www.socialengineaddons.com/sites/default/files/images/My-Albums-Page.png" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $albumCategoriesHome = "
    " . ' <a href="http://demo.socialengineaddons.com/albums/categories" title="View" class="seaocore_icon_view" target="_blank"></a>';

        $memberProfilePage = "
    " . ' <a href="http://demo.socialengineaddons.com/profile/linda" title="View" class="seaocore_icon_view" target="_blank"></a>';

        $this->addElement('MultiCheckbox', 'sitealbum_otherpagestemplate', array(
            'label' => 'Albums Others Pages',
            'description' => 'Select the below template for Photo Albums pages of your site.',
            'multiOptions' => array(
                'albumHome' => "Advanced Albums - Albums Home Page $albumHome",
                'albumBrowse' => "Advanced Albums - Albums Browse Page $albumBrowse",
                'albumPhotoBrowse' => "Advanced Albums - Photos Browse Page $albumPhotosBrowse",
                'albumLocations' => "Advanced Albums - Browse Albums' Locations Page $albumLocations",
                'albumPinboardView' => "Advanced Albums - Browse Albums’ Pinboard View Page $albumPinboard",
                'albumManage' => "Advanced Albums - My Albums Page $albumMyPage",
                'albumCategoriesHome' => "Advanced Albums - Categories Home Page $albumCategoriesHome",
                'memberProfileAlbumWidgetParameter' => "Member Profile Page - Member Profile Albums and Photos Widget $memberProfilePage"
            ),
            'escape' => false,
            'value' => $coreSettings->getSetting('sitealbum.otherpagestemplate'),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            //'onclick' => 'confirmSubmit()',
            'ignore' => true
        ));
    }

}
