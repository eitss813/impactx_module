<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Global extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.

    public function init() {

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this
                ->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');

        $this->addElement('Text', 'language_phrases_page', array(
            'label' => 'Singular Page Title',
            'description' => 'Please enter the Singular Title for page. This text will come in places like feeds generated, widgets etc.',
            'allowEmpty' => FALSE,
            'validators' => array(
                array('NotEmpty', true),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("language.phrases.page", "page"),
        ));

        $this->addElement('Text', 'language_phrases_pages', array(
            'label' => 'Plural Page Title',
            'description' => 'Please enter the Plural Title for pages. This text will come in places like Main Navigation Menu, Page Navigation Menu, widgets etc.',
            'allowEmpty' => FALSE,
            'validators' => array(
                array('NotEmpty', true),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("language.phrases.pages", "pages"),
        ));

        $this->addElement('Text', 'sitepage_manifestUrlP', array(
            'label' => 'Pages URL alternate text for "pageitems"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "pageitems" in the URLs of this plugin.',
            'value' => $coreSettings->getSetting('sitepage.manifestUrlP', "pageitems"),
        ));

        $this->addElement('Text', 'sitepage_manifestUrlS', array(
            'label' => 'Pages URL alternate text for "pageitem"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "pageitem" in the URLs of this plugin.',
            'value' => $coreSettings->getSetting('sitepage.manifestUrlS', "pageitem"),
        ));
        // check extension installed or not
        $featureExtension = $coreSettings->getSetting('sitepage.feature.extension', 0);
        if ($featureExtension) {
            $this->addElement('Radio', 'sitepage_category_enable', array(
                'label' => 'Enable Categories section?',
                'description' => 'Do you want to allow the users to choose the categories before page creation?. (If set to No, category selection options would be displayed in the page creation form.)',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $coreSettings->getSetting('sitepage.category.enable', 0),
            ));

            $this->addElement('Radio', 'sitepage_operating_hours_enable', array(
                'label' => 'Enable Operating Hours Feature?',
                'description' => 'Do you want to enable the setting so that users can add timing for their page?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $coreSettings->getSetting('sitepage.operating.hours.enable', 0),
            ));

            $this->addElement('Radio', 'sitepage_quick_enable', array(
                'label' => 'Enable quick page creation?',
                'description' => 'Do you want to allow the users to create page quickly?. (If set to Yes, then non-required fields will not show in the creation form)',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $coreSettings->getSetting('sitepage.quick.enable', 0),
            ));

            $this->addElement('Radio', 'sitepage_layout_enable', array(
                'label' => 'Enable Layout Mapping?',
                'description' => 'Do you want to enable Layout mapping?',
                'multiOptions' => array(
                    0 => 'No mapping',
                    1 => 'Category based mapping',
                    2 => 'Package based mapping',
                    3 => 'User will select layout'
                ),
                'value' => $coreSettings->getSetting('sitepage.layout.enable', 0),
            ));

            $this->addElement('Radio', 'sitepage_tips_enable', array(
                'label' => 'Enable Tips section?',
                'description' => 'Do you want to enable tips section.',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'onclick' => 'showtipOption(this.value)',
                'value' => $coreSettings->getSetting('sitepage.tips.enable', 0),
            ));
            $this->addElement('TinyMce', 'sitepage_tip', array(
                'label' => 'Tips',
                'value' => $coreSettings->getSetting('sitepage.tip','Make sure you create a great page listing by optimizing your "About" section, adding a good profile picture and choosing a suitable call-to-action button.')
            ));
            $this->sitepage_tip->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
        }
        $this->addElement('Radio', 'sitepage_package_view', array(
            'label' => 'Package View',
            'description' => 'Select the view type of packages that will be shown in the first step of page creation.',
            'multiOptions' => array(
                1 => 'Vertical',
                0 => 'Horizontal',
                2 => 'Custom'
            ),
            'onclick' => 'showcustomOption(this.value)',
            'value' => $coreSettings->getSetting('sitepage.package.view', 1),
            
        ));
        //VALUE FOR ENABLE/DISABLE PACKAGE
        $this->addElement('Radio', 'sitepage_package_enable', array(
            'label' => 'Packages',
            'description' => 'Do you want Packages to be activated for Directory Items / Pages? Packages can vary based on the features available to the pages created under them. If enabled, users will have to select a package in the first step while creating a new page. Page admins will be able to change their package later. To manage page packages, go to Manage Page Packages section. (Note: If packages are enabled, then feature settings for pages will depend on packages, and member levels based feature settings will be off. If packages are disabled, then feature settings for pages could be configured for member levels.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showpackageOption(this.value)',
            'value' => $coreSettings->getSetting('sitepage.package.enable', 1),
        ));

        //FOOTER FOR CUSTOM TEMPLATES
        $this->addElement('Select', 'sitepage_footer_enabled', array(
                'label' => 'Terms & Conditions / Notes',
                'description' => 'Do you want to show any Terms & Conditions / Notes to your users at the bottom of packages selection page? <a title="Preview - Terms & Conditions" href="application/modules/Sitepage/externals/images/footer_preview.png" target="_blank" class="seaocore_icon_view" > </a>',
                'multiOptions' => array(
                    0 => "No",
                    1 => "Yes",
                ),
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.footer.enabled', '0'),
                'onclick' => 'showOption(this.value)',
            ));
        $this->sitepage_footer_enabled->getDecorator('Description')->setOption('escape', false);
        
        $this->addElement('Textarea', 'sitepage_footer_text', array(
            'label' => 'Terms & Conditions / Notes Text',
            'description' => "Enter the text for the Terms & Conditions / Notes you want to show to your users at the bottom of packages selection page.",
        ));
        $this->sitepage_footer_text->setValue(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.footer.text', ''));
                
        $packageInfoArray = array('price' => 'Price', 'billing_cycle' => 'Billing Cycle', 'duration' => 'Duration', 'featured' => 'Featured', 'sponsored' => 'Sponsored', 'tellafriend' => 'Tell a friend', 'print' => 'Print', 'overview' => 'Rich Overview', 'map' => 'Map', 'insights' => 'Insights', 'contactdetails' => 'Contact Details', 'sendanupdate' => 'Send an Update', 'apps' => 'Apps available', 'description' => 'Description');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) {
            $packageInfoArray = array_merge($packageInfoArray, array('twitterupdates' => 'Display Twitter Updates'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
            $packageInfoArray = array_merge($packageInfoArray, array('ads' => 'Ads Display'));
        }
        
        $this->addElement('MultiCheckbox', 'sitepage_package_information', array(
            'label' => 'Package Information',
            'description' => 'Select the information options that you want to be available in package details.',
            'multiOptions' => $packageInfoArray,
            'value' => $coreSettings->getSetting('sitepage.package.information', array_keys($packageInfoArray)),
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->addElement('Dummy', 'sitepage_currency', array(
            'label' => 'Currency',
            'description' => "<b>" . $currencyName . "</b> <br class='clear' /> <a href='" . $view->url(array('module' => 'payment', 'controller' => 'settings'), 'admin_default', true) . "' target='_blank'>" . Zend_Registry::get('Zend_Translate')->_('edit currency') . "</a>",
        ));
        $this->getElement('sitepage_currency')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

        $this->addElement('Radio', 'sitepage_payment_benefit', array(
            'label' => 'Payment Status for Directory Item / Page Activation',
            'description' => "Do you want to activate directory items / pages immediately after payment, before the payment passes the gateways' fraud checks? This may take any time from 20 minutes to 4 days, depending on the circumstances and the gateway. (Note: If you want to manually activate pages, then you can set this while creating a page package.)",
            'multiOptions' => array(
                'all' => 'Activate page immediately.',
                'some' => 'Activate if member has an existing successful transaction, wait if this is their first.',
                'none' => 'Wait until the gateway signals that the payment has completed successfully.',
            ),
            'value' => $coreSettings->getSetting('sitepage.payment.benefit', 'all'),
        ));

        $this->addElement('Radio', 'sitepage_manageadmin', array(
            'label' => 'Page Admins',
            'description' => 'Do you want there to be multiple admins for directory items / pages on your site? (If enabled, then every Page will be able to have multiple administrators who will be able to manage that Page. Page Admins will have the authority to add other users as administrators of their Page.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.manageadmin', 1),
        ));

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu')) {
            $this->addElement('Radio', 'sitepage_show_menu', array(
                'label' => 'Pages Link',
                'description' => 'Select the location of the main link for Pages.',
                'multiOptions' => array(
                    3 => 'Main Navigation Menu',
                    2 => 'Mini Navigation Menu',
                    1 => 'Footer Menu',
                    0 => 'Member Home Page Left side Navigation'
                ),
                'value' => $coreSettings->getSetting('sitepage.show.menu', 3),
            ));
        }

        //VALUE FOR ENABLE/DISABLE REPORT
        $this->addElement('Radio', 'sitepage_report', array(
            'label' => 'Report as Inappropriate',
            'description' => 'Do you want to allow logged-in members to be able to report pages as inappropriate? (Members will also be able to mention the reason why they find the page inappropriate.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.report', 1),
        ));

        //VALUE FOR ENABLE /DISABLE SHARE
        $this->addElement('Radio', 'sitepage_share', array(
            'label' => 'Community Sharing',
            'description' => 'Do you want to allow members to share directory items / pages within your community?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.share', 1),
        ));

        //VALUE FOR ENABLE /DISABLE SHARE
        $this->addElement('Radio', 'sitepage_socialshare', array(
            'label' => 'Social Sharing',
            'description' => 'Do you want social sharing to be enabled for directory items / pages? (If enabled, social sharing buttons will be shown on the Profile Page of directory items / pages.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.socialshare', 1),
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitepage_captcha_post', array(
            'label' => 'CAPTCHA For Tell a friend',
            'description' => 'Do you want visitors to enter a validation code in Tell a friend form?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.captcha.post', 1),
        ));






        /* $this->addElement('Radio', 'sitepage_profile_search', array(
          'label' => 'Profile Type in Search',
          'description' => 'Do you want the Profile Type field in the search form widget at "Pages Home" and "Browse Pages" pages?',
          'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
          ),
          'value' => $coreSettings->getSetting('sitepage.profile.search', 1),
          )); */

        //VALUE FOR ENABLE /DISABLE LOCATION FIELD
        $this->addElement('Radio', 'sitepage_locationfield', array(
            'label' => 'Location Field',
            'description' => 'Do you want the Location field to be enabled for directory items / pages?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showlocationOption(this.value)',
            'value' => $coreSettings->getSetting('sitepage.locationfield', 1),
        ));

        $this->addElement('Radio', 'sitepage_multiple_location', array(
            'label' => 'Allow Multiple Locations',
            'description' => 'Do you want to allow page admins to enter multiple locations for their Pages? (If you select ‘Yes’, then users will be able to add multiple locations for their Pages from their Page Dashboards.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.multiple.location', 0),
        ));

        //VALUE FOR ENABLE /DISABLE MAP
        $this->addElement('Radio', 'sitepage_location', array(
            'label' => 'Maps Integration',
            'description' => ' Do you want Maps Integration to be enabled for directory items / pages? (With this enabled, items / pages having location information could also be seen on Map. The "Pages Home" and "Browse Pages" also enable you to see the items plotted on Map.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showMapOptions(this.value)',
            'value' => $coreSettings->getSetting('sitepage.location', 1),
        ));

        //VALUE FOR ENABLE /DISABLE Bouncing Animation
        $this->addElement('Radio', 'sitepage_map_sponsored', array(
            'label' => 'Sponsored Items with a Bouncing Animation',
            'description' => 'Do you want the sponsored directory items / pages to be shown with a bouncing animation in the Map?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.map.sponsored', 1),
        ));

        $this->addElement('Text', 'sitepage_map_city', array(
            'label' => 'Centre Location for Map at Pages Home and Browse Pages',
            'description' => 'Enter the location which you want to be shown at centre of the map which is shown on Pages Home and Browse Pages when Map View is chosen to view Directory/Pages.(To show the whole world on the map, enter the word "World" below.)',
            'required' => true,
            'value' => $coreSettings->getSetting('sitepage.map.city', "World"),
        ));

        $this->addElement('Select', 'sitepage_map_zoom', array(
            'label' => "Default Zoom Level for Map at Pages Home and Browse Pages",
            'description' => 'Select the default zoom level for the map which is shown on Pages Home and Browse Pages when Map View is chosen to view Directory/Pages. (Note that as higher zoom level you will select, the more number of surrounding cities/locations you will be able to see.)',
            'multiOptions' => array(
                '1' => "1",
                "2" => "2",
                "4" => "4",
                "6" => "6",
                "8" => "8",
                "10" => "10",
                "12" => "12",
                "14" => "14",
                "16" => "16"
            ),
            'value' => $coreSettings->getSetting('sitepage.map.zoom', 1),
            'disableTranslator' => 'true'
        ));

        //VALUE FOR ENABLE /DISABLE Proximity Search
        $this->addElement('Radio', 'sitepage_proximitysearch', array(
            'label' => 'Proximity Search',
            'description' => 'Do you want proximity search to be enabled for directory items / pages? (Proximity search will enable users to search for items / pages within a certain distance from a location.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showlocationKM(this.value)',
            'value' => $coreSettings->getSetting('sitepage.proximitysearch', 1),
        ));

        //VALUE FOR ENABLE /DISABLE Proximity Search IN Kilometer
        $this->addElement('Radio', 'sitepage_proximity_search_kilometer', array(
            'label' => 'Proximity Search Metric',
            'description' => 'What metric do you want to be used for proximity search?',
            'multiOptions' => array(
                0 => 'Miles',
                1 => 'Kilometers'
            ),
            'value' => $coreSettings->getSetting('sitepage.proximity.search.kilometer', 0),
        ));

        //VALUE FOR COMMENT
        $this->addElement('Radio', 'sitepage_checkcomment_widgets', array(
            'label' => 'Comments',
            'description' => 'Do you want comments to be enabled for directory items / pages? (If enabled, then users will be able to comment on items / pages on their Info tabs.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.checkcomment.widgets', 1),
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitepage_sponsored_image', array(
            'label' => 'Sponsored Label',
            'description' => 'Do you want to show "SPONSORED" label on the main profile of sponsored directory items / pages above the profile picture?',
            'onclick' => 'showsponsored(this.value)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.sponsored.image', 1),
        ));

        //COLOR VALUE FOR SPONSORED
        $this->addElement('Text', 'sitepage_sponsored_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowSponsred.tpl',
                        'class' => 'form element'
                    )))
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitepage_feature_image', array(
            'label' => 'Featured Label',
            'description' => 'Do you want to show "FEATURED" label on the main profile of featured directory items / pages below the profile picture?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showfeatured(this.value)',
            'value' => $coreSettings->getSetting('sitepage.feature.image', 1),
        ));

        //COLOR VALUE FOR FEATURED
        $this->addElement('Text', 'sitepage_featured_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowFeatured.tpl',
                        'class' => 'form element'
                    )))
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitepage_fs_markers', array(
            'label' => 'Featured & Sponsored Markers',
            'description' => 'On Pages Home, Browse Pages and My Pages how do you want a Page to be indicated as featured and sponsored ?',
            'multiOptions' => array(
                1 => 'Using Labels (See FAQ for customizing the labels)',
                0 => 'Using Icons (See FAQ for customizing the icons)',
            ),
            'value' => $coreSettings->getSetting('sitepage.fs.markers', 1),
        ));

        $this->addElement('Radio', 'sitepage_network', array(
            'label' => 'Browse by Networks',
            'description' => "Do you want to show directory items / pages according to viewer's network if he has selected any? (If set to no, all the items / pages will be shown.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetwork(this.value)',
            'value' => $coreSettings->getSetting('sitepage.network', 0),
        ));

        //VALUE FOR Page Dispute Link.
        $this->addElement('Radio', 'sitepage_default_show', array(
            'label' => 'Set Only My Networks as Default in search',
            'description' => 'Do you want to set "Only My Networks" option as default for Show field in the search form widget? (This widget appears on the pages browse and home pages, and enables users to search and filter pages.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetworkType(this.value)',
            'value' => $coreSettings->getSetting('sitepage.default.show', 0),
        ));

        $this->addElement('Radio', 'sitepage_networks_type', array(
            'label' => 'Network selection for Pages',
            'description' => "You have chosen that viewers should only see Pages of their network(s). How should a Page's network(s) be decided?",
            'multiOptions' => array(
                0 => "Page Owner's network(s) [If selected, only members belonging to page owner's network(s) will see the Pages.]",
                1 => "Selected Networks [If selected, page admins will be able to choose the networks of which members will be able to see their Page.]"
            ),
            'value' => $coreSettings->getSetting('sitepage.networks.type', 0),
        ));

        $this->addElement('Radio', 'sitepage_networkprofile_privacy', array(
            'label' => 'Display Profile Page only to Network Users',
            'description' => "Do you want to show the Directory Item / Page Profile page only to users of the same network. (If set to yes and \"Browse By Networks\" is enabled then users would not be able to view the profile page of those pages which does not belong to their networks.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            // 'onclick' => 'showviewablewarning(this.value);',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.networkprofile.privacy', 0),
        ));
        $this->addElement('Radio', 'sitepage_privacybase', array(
            'label' => 'Display of All Pages in widgets',
            'description' => "Do you want to show all the pages to the user in the widgets and browse pages of this plugin irrespective of privacy? [Note: If you select 'No', then only those pages will be shown in the widgets and browse pages which are viewable to the current logged-in user. But this may slightly affect the loading speed of your website. To avoid such loading delay to the best possible extent, we are also using caching based display.)",
            'multiOptions' => array(
                0 => 'Yes',
                1 => 'No'
            ),
            // 'onclick' => 'showviewablewarning(this.value);',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.privacybase', 0),
        ));
        //Order of browse page
        $this->addElement('Radio', 'sitepage_browseorder', array(
            'label' => 'Default ordering on Browse Pages',
            'description' => 'Select the default ordering of pages on the browse pages.',
            'multiOptions' => array(
                1 => 'All pages in descending order of creation.',
                2 => 'All pages in descending order of views.',
                3 => 'All pages in alphabetical order.',
                4 => 'Sponsored pages followed by others in descending order of creation.',
                5 => 'Featured pages followed by others in descending order of creation.',
                6 => 'Sponsored & Featured pages followed by Sponsored pages followed by Featured pages followed by others in descending order of creation.',
                7 => 'Featured & Sponsored pages followed by Featured pages followed by Sponsored pages followed by others in descending order of creation.',
            ),
            'value' => $coreSettings->getSetting('sitepage.browseorder', 1),
        ));

        $this->addElement('Radio', 'sitepage_addfavourite_show', array(
            'label' => 'Linking Pages',
            'description' => 'Do you want members to be able to Link their Pages to other Pages? (Linking is useful to show related Pages. For example, a Chef\'s Page can be linked to the Restaurant\'s Page where he works, or a Store\'s Page can be linked to the Pages of the Brands that it sells. If enabled, a "Link to your Page" link will appear on Pages.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.addfavourite.show', 1),
        ));
        $show_layout = $coreSettings->getSetting('sitepage.layoutcreate', 0);
        if($show_layout) {
            $this->addElement('Radio', 'sitepage_layoutcreate', array(
                'label' => 'Edit Page Layout',
                'description' => 'Do you want to enable page admins to alter the block positions / add new available blocks on the directory item / page profile? (If enabled, then page admins will also be able to add HTML blocks on their directory item / page profile.)',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $coreSettings->getSetting('sitepage.layoutcreate', 0),
            ));
        }

        $this->addElement('Radio', 'sitepage_category_edit', array(
            'label' => 'Edit Page Category',
            'description' => 'Do you want to allow page admins to edit category of their pages?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showcategoryblock(this.value);',
            'value' => $coreSettings->getSetting('sitepage.category.edit', 0),
        ));

        //$description = Zend_Registry::get('Zend_Translate')->_('Do you want to show categories, subcategories and 3%s level categories with slug in the url.');
        //$description = sprintf($description, "<sup>rd</sup>");
        $this->addElement('Radio', 'sitepage_categorywithslug', array(
            'label' => 'Slug URL',
            'description' => 'Do you want to replace blank-space in your category name by "-" in URL?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.categorywithslug', 1),
        ));

        //$this->sitepage_categorywithslug->getDecorator('Description')->setOptions(array('placement'=> 'PREPEND', 'escape' => false));

        $this->addElement('Radio', 'sitepage_claimlink', array(
            'label' => 'Claim a Page Listing',
            'description' => 'Do you want users to be able to file claims for directory items / pages ? (Claims filed by users can be managed from the Manage Claims section.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showclaim(this.value)',
            'value' => $coreSettings->getSetting('sitepage.claimlink', 1),
        ));

        $this->addElement('Radio', 'sitepage_claim_show_menu', array(
            'label' => 'Claim a Page link',
            'description' => 'Select the position for the "Claim a Page" link.',
            'multiOptions' => array(
                2 => 'Show this link on Pages Navigation Menu.',
                1 => 'Show this link on Footer Menu.',
                0 => 'Do not show this link.'
            ),
            'value' => $coreSettings->getSetting('sitepage.claim.show.menu', 2),
        ));

        $this->addElement('Radio', 'sitepage_claim_email', array(
            'label' => 'Notification for Page Claim',
            'description' => 'Do you want to receive e-mail notification when a member claims a page?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.claim.email', 1),
        ));


        $this->addElement('Radio', 'sitepage_automatically_like', array(
            'label' => 'Automatic Like',
            'description' => "Do you want members to automatically Like a page they create?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.automatically.like', 1),
        ));

        $this->addElement('Radio', 'sitepage_hide_left_container', array(
            'label' => 'Hide Left / Right Column on Page Profile',
            'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('When you have "Advertisements / Community Ads" enabled to be shown on Page Profile from "%1$sAd Settings%2$s" section, then do you want the left / right column on Page Profile to be hidden when users click on the Page tabs other than Updates, Info and Overview?'), "<a href='" . $view->url(array('module' => 'sitepage', 'controller' => 'settings', 'action' => 'adsettings'), 'admin_default', true) . "' target='_blank'>", '</a>'),
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.hide.left.container', 0),
        ));
        $this->sitepage_hide_left_container->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Radio', 'sitepage_show_tabs_without_content', array(
            'label' => 'Show Tabs with no Respective Content',
            'description' => 'When there are content types in a Page (like Albums, Videos, etc.) with no respective content, then do you want their tabs to appear on Page profile to users who do not have permission to add that content?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.show.tabs.without.content', 0),
        ));


        $this->addElement('Radio', 'sitepage_slding_effect', array(
            'label' => 'Enable Sliding Effect on Tabs',
            'description' => 'Do you want to enable sliding effect when tabs on Page Profile are clicked?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.slding.effect', 1),
        ));


        $this->addElement('Radio', 'sitepage_mylike_show', array(
            'label' => 'Pages I Like Link',
            'description' => 'Do you want to show the "Pages I Like" link to users? This link appears on "My Pages" and enables users to see the list of Pages that they have Liked.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.mylike.show', 1),
        ));

        $this->addElement('Text', 'sitepage_page', array(
            'label' => 'Directory Items / Pages Per Page',
            'description' => 'How many directory items / pages will be shown per page in "Browse Pages" and "My Pages" pages?',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '3'))
            ),
            'value' => $coreSettings->getSetting('sitepage.page', 24),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));
        
        $this->addElement('Radio', 'sitepage_redirection', array(
            'label' => 'Redirection of Pages link',
            'description' => 'Please select the redirection page for Pages, when user click on "Pages" link at Main Navigation Menu.',
            "multiOptions" => array(
                'home' => 'Pages Home',
                'index' => 'Browse Pages'
            ),
            'value' => $coreSettings->getSetting('sitepage.redirection', 'home'),
        ));          

        $this->addElement('Text', 'sitepage_showmore', array(
            'label' => 'Tabs / Links',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'description' => 'How many tabs / links do you want to show on directory item / page profile by default? (Note that if there are more tabs / links than the limit entered by you then a "More" tab / link will appear, clicking on which will show the remaining hidden tabs / links. Tabs are available in the tabbed layout, and links in the non-tabbed layout. To choose the layout for Pages on your site, visit the "Page Layout" section.)',
            'value' => $coreSettings->getSetting('sitepage.showmore', 8),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement('Text', 'sitepageshow_navigation_tabs', array(
            'label' => 'Tabs in Pages navigation bar',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'description' => 'How many tabs do you want to show on Pages main navigation bar by default? (Note: If number of tabs exceeds the limit entered by you then a "More" tab will appear, clicking on which will show the remaining hidden tabs. To choose the tab to be shown in this navigation menu, and their sequence, please visit: "Layout" > "Menu Editor")',
            'value' => $coreSettings->getSetting('sitepageshow.navigation.tabs', 8),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement('Radio', 'sitepage_postedby', array(
            'label' => 'Posted By',
            'description' => "Do you want to enable Posted by option for the Pages on your site? (Selecting Yes here will display the member's name who has created the page.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.postedby', 1),
        ));
        $advfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
        $adddescription = '';
        if (!$advfeedmodule)
            $adddescription = "and requires it to be installed and enabled on your site. Please install this plugin after downloading it from your Client Area on SocialEngineAddOns. You may purchase this plugin <a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>over here</a>";
        $this->addElement('Radio', 'sitepage_postfbpage', array(
            'label' => 'Allow Facebook Page Linking',
            'description' => "Do you want to allow users to link their Facebook Pages with their Pages on your website? If you select 'Yes' over here, then users will see a new block in the 'Marketing' section of their Page Dashboard which will enable them to enter the URL of their Facebook Page. With this, the updates made by users on their Page on your site will also be published on their Facebook Page. Also, the Facebook Like Box for the Facebook Page will be displayed on Page Profile. The Facebook Like Box will:<br /><br /><ul style='margin-left: 20px;'><li>Show the recent posts from the Facebook Page.</li><li>Show how many people already like the Facebook Page.</li><li>Enable visitors to Like the Facebook Page from your site.</li></ul><br /><br />If you do not want to show the Facebook Like Box on Pages with linked Facebook Pages, then simply remove the widget from the 'Layout Editor'. With linked Facebook Page, if Page Admins select 'Publish this on Facebook' option while posting their updates, then these 
updates will be published on their Facebook Profile as well as Facebook Page. (Note: Publishing updates on Facebook Pages via this linking is dependent on the <a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'> Advanced Activity Feeds / Wall Plugin</a> " . $adddescription . ".)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.postfbpage', 1),
        ));
        $this->sitepage_postfbpage->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
        $publish_fb_places = array('0' => 1, '1' => 2);
        $publish_fb_places = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.publish.facebook', serialize($publish_fb_places));
        if (!empty($publish_fb_places) && !is_array($publish_fb_places)) {
            $publish_fb_places = unserialize($publish_fb_places);
        }
        $this->addElement('MultiCheckbox', 'sitepage_publish_facebook', array(
            'label' => 'Publishing Updates on Facebook',
            'description' => "Choose the places on Facebook where users will be able to publish their updates that they post on Pages of your site.",
            'multiOptions' => array(
                '1' => 'Publish this post on Facebook Page linked with this Page. [Note: This setting will only work if you choose \'Yes\' option for the setting "Allow Facebook Page Linking".]',
                '2' => 'Publish this post on my Facebook Timeline',
            ),
            'value' => $publish_fb_places
        ));

        $this->addElement('Radio', 'sitepage_tinymceditor', array(
            'label' => 'Tinymce Editor',
            'description' => 'Allow TinyMCE editor for discussion message of Pages.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitepage.tinymceditor', 1),
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $field = 'sitepage_code_share';
        $this->addElement('Dummy', "$field", array(
            'label' => 'Social Share Widget Code',
            'description' => "<a class='smoothbox' href='" . $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'social-share', 'field' => "$field"), 'admin_default', true) . "'>Click here</a> to add your social share code.",
            'ignore' => true,
        ));
        $this->$field->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));


        $this->addElement('Textarea', 'sitepage_defaultpagecreate_email', array(
            'label' => 'Alerted by email',
            'description' => 'Please enter comma-separated list, or one-email-per-line. Email is sent to the below enter emails when members create new Pages.',
            'value' => $coreSettings->getSetting('sitepage.defaultpagecreate.email', Engine_API::_()->seaocore()->getSuperAdminEmailAddress()),
        ));

        $this->addElement('Text', 'sitepage_title_truncation', array(
            'label' => 'Title Truncation Limit',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'description' => 'What maximum limit should be applied to the number of characters in the title of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
            'value' => $coreSettings->getSetting('sitepage.title.truncation', 18),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));


        //VALUE FOR ENABLE/DISABLE REPORT
//    $this->addElement('Radio', 'sitepage_specialcharacters', array(
//        'label' => 'Filters in Description',
//        'description' => 'Do you want to filter the special characters from the description of the page.',
//        'multiOptions' => array(
//            1 => 'Yes',
//            0 => 'No'
//        ),
//        'value' => $coreSettings->getSetting('sitepage.specialcharacters', 1),
//    ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}

?>
