<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$setttingsUrl = $view->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page'));
$footerTemplate = $view->url(array('module' => 'sitecoretheme', 'controller' => 'footer-templates'));
$headerSettings = $view->url(array('module' => 'sitecoretheme', 'controller' => 'header'));
$informativeURL = $view->url(array('module' => 'sitecoretheme', 'controller' => 'blocks'));
$teamUrl = $view->url(array('module' => 'sitecoretheme', 'controller' => 'teams'));
$innerImagesSettings = $view->url(array('module' => 'sitecoretheme', 'controller' => 'settings', 'action' => 'inner-images'));

$onloadScript = " <script>
 window.addEvent('domready', function () {
      $('title-wrapper').style.display = 'none';
});


</script>";
return array(
//  array(
//    'title' => SITECORETHEME_PLUGIN_NAME.' - Vertical Main Menu',
//    'description' => 'Displays site-wide main menu. You can edit its content via menu editor.',
//    'category' => 'SEAO '.SITECORETHEME_PLUGIN_NAME,
//    'type' => 'widget',
//    'name' => 'sitecoretheme.menu-main',
//    'requirements' => array(
//      'header-footer',
//    ),
//    'autoEdit' => true,
//    'adminForm' => array(
//      'elements' => array(
//        array(
//          'Select',
//          'mobuleNavigations',
//          array(
////            'label' => 'Menu Nested Navigatinos',
//            'description' => 'Do you want to show nested navigations?',
//            'multiOptions' => array(
//              '1' => 'Yes',
//              '0' => 'No'
//            ),
//            'value' => '1'
//          )),
//        array(
//          'Select',
//          'menuType',
//          array(
////            'label' => 'Menu Pannel Type',
//            'description' => 'Select the type of pannel of menu.',
//            'multiOptions' => array(
//              'slide' => 'Slide Panel',
//              'overlay' => 'Overlay Panel'
//            ),
//            'value' => '1'
//          )),
//      )
//    ),
//  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Informative Block',
    'description' => 'Displays the selected informative block. To create the content for informative block <a href="' . $informativeURL . '" > click here</a>.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.block',
    'autoEdit' => true,
    'adminForm' => 'Sitecoretheme_Form_Admin_Widget_Block',
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Landing Page Intro Block',
    'description' => 'Landing Page - Intro Block',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.landing-page-intro-block',
    'autoEdit' => true,
    'adminForm' => array(),
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Landing Page Heading Block',
    'description' => 'Add the Attractive Heading with Descriptions',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.heading',
    'autoEdit' => true,
    'adminForm' => 'Sitecoretheme_Form_Admin_Widget_Heading',
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Video Banner',
    'description' => 'Displays the video accompanied with an enticing image in centre on your landing page.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.video-banner',
    'autoEdit' => true,
    'adminForm' => array(),
  ),
//  array(
//    'title' => SITECORETHEME_PLUGIN_NAME.' - Header Navigation Tabs',
//    'description' => "Displays the site wide navigation menus of your website. This widget should be placed in header.",
//    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
//    'type' => 'widget',
//    'name' => 'sitecoretheme.main-navigation',
//    'defaultParams' => array(
//      'title' => '',
//      'titleCount' => true,
//    ),
//    'adminForm' => array(
//    ),
//  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Scroll Content Menu',
    'description' => "Displays the scroll content menu for scroll the window corresponding block. [Note: Its take only the content which added the title].",
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.scroll-content-menus',
    'defaultParams' => array(
      'title' => '',
      'titleCount' => true,
    ),
    'adminForm' => array(
    ),
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Highlight Content Block',
    'description' => 'Highlights the content of selected entity on the landing page.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.content-blocks',
    'autoEdit' => true,
    'adminForm' => 'Sitecoretheme_Form_Admin_Widget_ContentBlocks'
  ),
	array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Highlight Two Content Blocks',
    'description' => 'Highlights the content of selected two entity on the landing page.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.two-content-blocks',
    'autoEdit' => true,
    'adminForm' => 'Sitecoretheme_Form_Admin_Widget_TwoContentBlocks'
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Highlight Content Circular Block',
    'description' => 'Highlights the content of selected entity on the landing page in circular views.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.landing-page-listing',
    'autoEdit' => true,
    'adminForm' => 'Sitecoretheme_Form_Admin_Widget_ContentListing'
  ),
//  array(
//    'title' => SITECORETHEME_PLUGIN_NAME.' - Footer Text',
//    'description' => 'You can place this widget in the footer and can set text accordingly from the ‘Language Manager’ under ‘Appearance’ section available in the admin panel of your site.', 
//    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
//    'type' => 'widget',
//    'name' => 'sitecoretheme.homepage-footertext',
//    'adminForm' => array(
//    'elements' => array(
//      )
//    )
//  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Footer Menu',
    'description' => 'Displays the site-wide footer menu. You can edit its content in your menu editor. To edit other settings and content click <a href = "' . $footerTemplate . '" target = "_blank">here</a>',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.menu-footer',
    'requirements' => array(
      'header-footer',
    ),
    'adminForm' => array(
      'elements' => array(
      )
    ),
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Inner Page Slider Images',
    'description' => 'Displays the slider images uploaded by you in Admin Panel => Slider Images =>  Inner page Slider Images . This widget can be placed on any widgetized page. To upload more images click <a href = "' . $innerImagesSettings . '" target = "_blank">here</a>',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.banner-images',
    'adminForm' => 'Sitecoretheme_Form_Admin_Widget_BannerContent',
    'autoEdit' => 'true'
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Landing Page Slider',
    'description' => 'Displays images uploaded by you in Admin Panel => Slider Images => Landing Page Image Slider Images on landing page. To upload more images click <a href = "' . $setttingsUrl . '/slider" target = "_blank">here</a>',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.images',
    'autoEdit' => 'true'
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Landing Page Header',
    'description' => 'Displays the header options on your site landing page. You can edit its content in your menu editor. Click on <a href = "' . $headerSettings . '" target = "_blank">edit</a> to modify the other content for this widget.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.landing-page-header',
    'autoEdit' => 'true'
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Markers',
    'description' => 'Displays the markers of the website. Click on <a href = "' . $setttingsUrl . '/markers" target = "_blank">edit</a> to modify the settings for this widget.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.markers',
    'autoEdit' => 'true',
    'defaultParams' => array(
      'title' => 'What have we gained?',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Services Block',
    'description' => 'We are providing you with aspects which are no doubt the must for the success of a social community.',
    'decorators' => array('ViewHelper', array('Description', array('placement' => 'PREPEND', 'escape' => false))),
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
		'adminForm' => 'Sitecoretheme_Form_Admin_Widget_Services',
		'autoEdit' => 'true',
    'name' => 'sitecoretheme.our-services',
    'defaultParams' => array(
      'title' => 'Checkout Our Services',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Achievements Block',
    'description' => 'Displays the achievements along with the achievement count. Click on <a href = "' . $setttingsUrl . '/stats" target = "_blank">edit</a> to modify the content for this widget..',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.stats-block',
    'defaultParams' => array(
      'title' => '',
      'titleCount' => true,
    ),
//      'adminForm' => 'Sitecoretheme_Form_Admin_Widget_Stats',
    'autoEdit' => 'true',
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Highlights Block',
    'description' => 'Displays the highlights of the website. Click on <a href = "' . $setttingsUrl . '/highlights" target = "_blank">edit</a> to modify the content for this widget.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.highlights-block',
    'defaultParams' => array(
      'title' => '',
      'titleCount' => true,
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'description',
          array(
            'label' => 'Description',
            'value' => '',
          )
        ),
      ),
    ),
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Sign In and Sign Up Banner',
    'description' => 'Displays the banner image behind the Sign In and Sign Up form. Upload preferred banner image in Admin Panel => Appearance => File & Media Manager.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.form-banner',
    'defaultParams' => array(
      'title' => '',
    ),
    'adminForm' => 'Sitecoretheme_Form_Admin_Widget_Banner',
    'autoEdit' => 'true',
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Header',
    'description' => 'Displays the header options on your site. You can edit its content in your menu editor. Click on <a href = "' . $headerSettings . '" target = "_blank">edit</a> to modify the other content for this widget.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.header',
    'defaultParams' => array(
      'title' => '',
    ),
    'autoEdit' => 'true',
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Action Buttons',
    'description' => 'Displays the custom content added for landing page. Click on <a href = "' . $setttingsUrl . '/cta-buttons" target = "_blank">edit</a> to modify the content for this widget.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.static-buttons',
    'defaultParams' => array(
      'title' => '',
    ),
    //  'adminForm' => 'Sitecoretheme_Form_Admin_Widget_Buttons',
    'autoEdit' => 'true',
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' - Promotional Banner',
    'description' => '<a href = "' . $setttingsUrl . '/app-banner" target = "_blank">Click here</a> for settings.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.app-promotion'
  ),
  array(
    'title' => SITECORETHEME_PLUGIN_NAME.' -  Banner Tagline Block',
    'description' => 'Displays the banner tagline on your landing page. Click on <a href = "' . $setttingsUrl . '/text-banner" target = "_blank">Click here</a> to modify the content for this widget.',
    'category' => 'SEAO - '.SITECORETHEME_PLUGIN_NAME,
    'type' => 'widget',
    'name' => 'sitecoretheme.text-banner'
  ),
  )
?>