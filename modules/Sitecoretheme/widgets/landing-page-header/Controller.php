<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Widget_LandingPageHeaderController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer && $viewer->getIdentity()) {
      return $this->setNoRender(); // This widget work only for non logged in users.
    }
    Zend_Registry::set('Sitecoretheme_Widget_LandingPageHeader_Render', 1);
    $this->getElement()->removeDecorator('Title');
    $this->view->coreSettings = $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->isSitemenuExist = $isSitemenuExist = Engine_Api::_()->hasModuleBootstrap('sitemenu');
    $this->view->showLogo = $coreSettings->getSetting('sitecoretheme.landing.header.showLogo', 1);
    $this->view->logo = $coreSettings->getSetting('sitecoretheme.landing.header.logo');
    $this->view->alternateLogo = $coreSettings->getSetting('sitecoretheme.landing.header.fixed.logo');
    $this->view->showSearch = $coreSettings->getSetting("sitecoretheme.landing.header.showSearch", 1);
    $this->view->isSitemenuEnable = $isSitemenuEnable = Engine_Api::_()->hasModuleBootstrap('sitemenu');
    $this->view->headerStyle = $coreSettings->getSetting('sitecoretheme.header.style', '2');
    // Advanced Menu settings
    $params = array();
    if ($isSitemenuEnable) {
      $params['sitemenu_totalmenu'] = $params['sitemenu_totalmenu_tablet'] = $coreSettings->getSetting("sitecoretheme.landing.header.max", 6);
      $params['sitemenu_truncation_limit_content'] = $params['sitemenu_truncation_limit_category'] = $coreSettings->getSetting("sitecoretheme.landing.header.truncationContent", 6);
      $params['sitemenu_show_cart'] = $coreSettings->getSetting("sitecoretheme.landing.header.showCart", 1);
      $params['sitemenu_show_extra_on'] = $coreSettings->getSetting("sitecoretheme.landing.header.showCartOn", 1);
      $params['sitemenu_on_logged_out'] = 1;
      $params['sitemenu_show_link_icon'] = 0;
      $params['sitemenu_is_more_link'] = 1;
      $params['sitemenu_show_in_main_options'] = 1;
      $params['changeMyLocation'] = 0;
      $params['sitemenu_box_shadow'] = 0;
      $params['sitemenu_separator_style'] = 0;
      $params['sitemenu_more_link_icon'] = 0;
      $params['sitemenu_is_arrow'] = 1;
      $params['sitemenu_menu_corners_style'] = 0;
      $params['sitemenu_main_menu_height'] = 20;
      $params['sitemenu_is_fixed'] = 0;
      $params['sitemenu_fixed_height'] = 0;
      $params['sitemenu_style'] = 1;
    }
    $this->view->menuParams = $params;

    $location = $coreSettings->getSetting('sitecoretheme.landing.header.display.location', 0);
    $showIcons = $coreSettings->getSetting('sitecoretheme.landing.header.minimenu.design', 1);
    $this->view->miniMenuParams = array(
      'enable_login_lightbox' => 0,
      'enable_signup_lightbox' => 0,
      'changeMyLocation' => $location,
      'show_icons' => $showIcons,
      'show_icons' => $showIcons,
      'sitemenu_show_icon' => $showIcons,
      'show_in_mini_options' => 6, //IN CASE OF 6 ADV MENU SEARCH BAR WILL NOT BE SHOWN IN HEADER
      'sitemenu_show_in_mini_options' => 1000,
      'location_box_width' => ''
    );
    $this->view->signupLoginPopup = $coreSettings->getSetting('sitecoretheme.signin.popup.enable', 1);
    $this->view->popupVisibilty = $coreSettings->getSetting('sitecoretheme.signin.popup.visibility', 0);
    $this->view->popupClosable = $coreSettings->getSetting('sitecoretheme.signin.popup.close', 1);
    $this->view->autoShowPopup = $coreSettings->getSetting('sitecoretheme.signin.popup.display', 1);
  }

}