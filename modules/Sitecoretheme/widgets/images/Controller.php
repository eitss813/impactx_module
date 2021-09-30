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
class Sitecoretheme_Widget_ImagesController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->getElement()->removeDecorator('Title');
    $this->view->coreSettings = $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->isSitemenuExist = $isSitemenuExist = Engine_Api::_()->hasModuleBootstrap('sitemenu');


    $this->view->showImages = $coreSettings->getSetting('sitecoretheme.landing.slider.images', 1);
    $selectedImages = array();
    if (!$this->view->showImages) {
      $selectedImages = $coreSettings->getSetting('sitecoretheme.landing.slider.selectedImages');

      if (empty($selectedImages)) {
        return $this->setNoRender();
      }

      $this->view->list = $getImages = Engine_Api::_()->getItemTable('sitecoretheme_image')->getImages(array('enabled' => 1, 'selectedImages' => $selectedImages), array('file_id'));
    } else {
      $this->view->list = $getImages = Engine_Api::_()->getItemTable('sitecoretheme_image')->getImages(array('enabled' => 1), array('file_id'));
    }
    $order = $this->_getParam("order", 2);
    if (!COUNT($getImages)) {
      $this->view->list = Engine_Api::_()->sitecoretheme()->setImageOrder(array("1.jpg", "2.jpg", "3.jpg", "4.jpg"), $order);
    } else {
      $getImagesArray = $getImages->toArray();
      $this->view->list = Engine_Api::_()->sitecoretheme()->setImageOrder($getImagesArray, $order);
    }

    $this->view->defaultDuration = $coreSettings->getSetting('sitecoretheme.landing.slider.speed', 5000);
    $this->view->slideWidth = $coreSettings->getSetting('sitecoretheme.landing.slider.width', null);
    $this->view->slideHeight = $coreSettings->getSetting('sitecoretheme.landing.slider.height', 583);

    $this->view->verticalHtmlTitle = $coreSettings->getSetting('sitecoretheme.landing.slider.bannerTitle', 'Explore the world with us');

    $description1 = $coreSettings->getSetting("sitecoretheme.landing.slider.description1", "A true social community is when you feel connected and responsible for what happens around. ");
    $description2 = $coreSettings->getSetting("sitecoretheme.landing.slider.description2", '');
    $description3 = $coreSettings->getSetting("sitecoretheme.landing.slider.description3", '');

    $description = array();
    if (!empty(trim($description1))) {
      $description[] = $this->view->translate($description1);
    }
    if (!empty(trim($description2))) {
      $description[] = $this->view->translate($description2);
    }

    if (!empty(trim($description3))) {
      $description[] = $this->view->translate($description3);
    }
    $this->view->description = $description;

    $this->view->verticalSignupLoginButton = $coreSettings->getSetting('sitecoretheme.landing.slider.showButton', 1);
    $this->view->signupLoginPopup = $coreSettings->getSetting("sitecoretheme.landing.slider.signupLoginPopup", 1);
    $this->view->max = $coreSettings->getSetting("sitecoretheme.landing.slider.max", 4);


    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->removePadding = false;

    //GET CONTENT ID
    $content_id = $this->view->identity;
    $content_page_id = Engine_Api::_()->sitecoretheme()->getContentPageId(array('content_id' => $content_id));
    $layoutValue = Engine_Api::_()->sitecoretheme()->getWidgetizedPageLayoutValue(array('page_id' => $content_page_id));

    if ($layoutValue == 'default-simple') {
      $this->view->removePadding = true;
    }
    $this->view->showCloseIcon = $coreSettings->getSetting('sitecoretheme.signin.popup.close', 1);
    $popupVisibility = $coreSettings->getSetting('sitecoretheme.signin.popup.visibility', 0);

    if ($popupVisibility != 0 || $this->view->showCloseIcon != 1) {
      $this->view->popupClosable = 'false';
    }

    $themes = Engine_Api::_()->getDbtable('themes', 'core')->fetchAll();
    $activeTheme = $themes->getRowMatching('active', 1);

    $this->view->isVerticaltheme = false;
    if ($activeTheme->name == 'sitecoretheme') {
      $this->view->isVerticaltheme = true;
    }
  }

}