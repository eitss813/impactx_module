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
class Sitecoretheme_Widget_headerController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    if (Zend_Registry::isRegistered('Sitecoretheme_Widget_LandingPageHeader_Render')) {
      return $this->setNoRender();
    }
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $deafultHeaderOptions = array(
      'logo',
      'mini_menu',
      'main_menu',
      'search_box',
      'sociallink'
    );
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->displayWidgets = $viewer && $viewer->getIdentity() ? $coreSettings->getSetting('sitecoretheme.header.loggedin.widgets', $deafultHeaderOptions) : $coreSettings->getSetting('sitecoretheme.header.loggedout.widgets', $deafultHeaderOptions);
    $this->view->displayWidgets = array_diff($this->view->displayWidgets, array('none'));
    if (empty($this->view->displayWidgets)) {
      return $this->setNoRender();
    }
    $logoPath = $coreSettings->getSetting('sitecoretheme.header.logo.image', '');
    $this->view->logoParams = array('logo' => $logoPath);
    $this->view->alternateLogoParams = array('logo' => $coreSettings->getSetting('sitecoretheme.header.fixed.logo.image', ''));
    $this->view->showMenu = true;
    $this->view->headerStyle = $coreSettings->getSetting('sitecoretheme.header.style', '2');

    $this->view->isSitemenuEnable = $isSitemenuEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu');
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $pageIdentity = join('-', array(
      $request->getModuleName(),
      $request->getControllerName(),
      $request->getActionName()
    ));

    $themes = Engine_Api::_()->getDbtable('themes', 'core')->fetchAll();
    $activeTheme = $themes->getRowMatching('active', 1);
    $headerPosition = $coreSettings->getSetting('sitecoretheme.landing.slider.header.position', 1);


    $this->view->fixedMenu = $fixedMenu = $coreSettings->getSetting('sitecoretheme.header.menu.fixed', 0);
    $this->view->headerClass = 'sitecoretheme_fullheader_fixed';
    $location = $coreSettings->getSetting('sitecoretheme.header.display.location', 0);
    $showIcons = $coreSettings->getSetting('sitecoretheme.header.minimenu.design', 1);
    $this->view->miniMenuParams = array(
      'enable_login_lightbox' => 0,
      'enable_signup_lightbox' => 0,
      'changeMyLocation' => $location,
      'show_icon' => $showIcons,
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

    $this->view->menuPosition = $coreSettings->getSetting('sitecoretheme.header.menu.position', 1);
    $desktopMenuCount = $coreSettings->getSetting('sitecoretheme.header.desktop.totalmenu', 6);
    $this->view->fixedMenu = $fixedMenu = $coreSettings->getSetting('sitecoretheme.header.menu.fixed', 0);
    if ($isSitemenuEnable) {
      $truncationLimit = 200;
      $showCart = $coreSettings->getSetting('sitecoretheme.header.display.cart', 1);
      $location = $coreSettings->getSetting('sitecoretheme.header.display.location', 0);
      $showIcons = $coreSettings->getSetting('sitecoretheme.header.minimenu.design', 1);
      if ($this->view->headerStyle == 3) {
        $this->view->fixedMenu = $fixedMenu = $coreSettings->getSetting('sitecoretheme.header.sitemenu.fixed', 0);
        $this->view->headerClass = ($fixedMenu == 2) ? 'sitecoretheme_fullheader_fixed' : ($fixedMenu == 1 ? 'sitecoretheme_topheader_fixed' : '');
      }


      $this->view->menuParams = array(
        'sitemenu_on_logged_out' => 1,
        'sitemenu_totalmenu' => $desktopMenuCount,
        'sitemenu_truncation_limit_content' => $truncationLimit,
        'sitemenu_truncation_limit_category' => $truncationLimit,
        'sitemenu_show_cart' => $showCart,
        'sitemenu_is_fixed' => 1,
        'sitemenu_show_link_icon' => $coreSettings->getSetting('sitecoretheme.header.menu.icon', 1), // $this->_getParam('sitemenu_show_link_icon', 0)
        'pannelType' => ($coreSettings->getSetting('sitecoretheme.header.menu.style', 'slide') == 'slide' ? 'fitpage' : 'overlay'),
      );
    } else {
      if ($this->view->headerStyle === 3 && $this->view->menuPosition == 1) {
        $this->view->headerStyle = 2;
      }
    }
  }

}