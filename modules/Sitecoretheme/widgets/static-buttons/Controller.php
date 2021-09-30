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
class Sitecoretheme_Widget_StaticButtonsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->title1 = $coreSettings->getSetting('sitecoretheme.landing.cta.title1', 'Create Events to Socialise');
    $this->view->title2 = $coreSettings->getSetting('sitecoretheme.landing.cta.title2', 'Preserve Memorable Moments');
    $this->view->title3 = $coreSettings->getSetting('sitecoretheme.landing.cta.title3', 'Improve your Credibility with Blogs');
    $this->view->icon1Url = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.cta.icon1', ''), 1);
    $this->view->icon2Url = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.cta.icon2', ''), 2);
    $this->view->icon3Url = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.cta.icon3', ''), 3);

    $this->view->icon1HoverUrl = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.cta.hover.icon1', ''), 1, true);
    $this->view->icon2HoverUrl = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.cta.hover.icon2', ''), 2, true);
    $this->view->icon3HoverUrl = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.cta.hover.icon3', ''), 3, true);

    $this->view->url1 = $coreSettings->getSetting('sitecoretheme.landing.cta.url1', '');
    $this->view->url2 = $coreSettings->getSetting('sitecoretheme.landing.cta.url2', '');
    $this->view->url3 = $coreSettings->getSetting('sitecoretheme.landing.cta.url3', '');

    $this->view->body1 = $coreSettings->getSetting('sitecoretheme.landing.cta.body1', 'Virtual events are highly interactive and involve interacting people sharing a common virtual environment on the web.');
    $this->view->body2 = $coreSettings->getSetting('sitecoretheme.landing.cta.body2', 'Photos capture the moment in time and preserve it for generations to come. They make the important events of our lives memorable.');
    $this->view->body3 = $coreSettings->getSetting('sitecoretheme.landing.cta.body3', 'Blog gives you the opportunity to create relevant content for your customers and gives them a reason to click through to your website.');
    $this->view->sidePhoto = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.side-banner')) ?: $this->view->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/cta/sidePhoto.png';
    $this->view->new_tab = $coreSettings->getSetting('sitecoretheme.landing.cta.newtab', 0);
    $this->view->style = $coreSettings->getSetting('sitecoretheme.landing.cta.style', 'style1');
  }

  public function getIconUrl($file_id, $iconNumber = 0, $hover = false) {
    $iconUrl = '';
    if ($iconNumber) {
      if ($hover) {
        $iconUrl = $defaultIcon = $this->view->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/cta/static_button_' . $iconNumber . '_hover.png';
      } else {
        $iconUrl = $defaultIcon = $this->view->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/cta/static_button_' . $iconNumber . '.png';
      }
    }
    if ($file_id) {
      $icon = Engine_Api::_()->storage()->get($file_id);
      $iconUrl = ( $icon ) ? $icon->getPhotoUrl() : $defaultIcon;
    }
    return $iconUrl;
  }

}