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
class Sitecoretheme_Widget_MenuFooterController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $islanguage = $this->view->translate()->getLocale();
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    if (!strstr($islanguage, '_')) {
      $islanguage = $islanguage . '_default';
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewerEmail = '';
    if (isset($viewer->email)) {
      $this->view->viewerEmail = $viewer->email;
    }
    $this->view->templates = $coreSettings->getSetting('sitecoretheme.footer.templates', 2);
    //No need of tip now, we'll give default color and image option, we can remove the code to show tip
    if (empty($coreSettings->getSetting('sitecoretheme.footer.background', 2))) {

      if (isset($viewer->level_id) && $viewer->level_id == 1) {
        $this->view->showFooterTip = 1;
      } else {
        return $this->setNoRender();
      }
    }

    $keyForSettings = str_replace('_', '.', $islanguage);
    $verticalfooterLendingBlockValue = $coreSettings->getSetting('sitecoretheme.footer.lending.block.languages.' . $keyForSettings, null);

    $verticalfooterLendingBlockTitleValue = $coreSettings->getSetting('sitecoretheme.footer.lending.block.title.languages.' . $keyForSettings, null);
    if (empty($verticalfooterLendingBlockValue)) {
      $verticalfooterLendingBlockValue = $coreSettings->getSetting('sitecoretheme.footer.lending.block', null);
    }

    if (!empty($verticalfooterLendingBlockValue))
      $this->view->verticalfooterLendingBlockValue = @base64_decode($verticalfooterLendingBlockValue);
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitecoretheme_footer");
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->selectFooterBackground = $coreSettings->getSetting('sitecoretheme.footer.background', 2);

    $this->view->showFooterBackgroundImage = $coreSettings->getSetting('sitecoretheme.footer.backgroundimage', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application/modules/Sitecoretheme/externals/images/default_footer_bg.png');
    $this->view->showFooterLogo = $coreSettings->getSetting('sitecoretheme.footer.show.logo', 1);
    $this->view->selectFooterLogo = $coreSettings->getSetting('sitecoretheme.footer.select.logo');

    $this->view->verticalTwitterFeed = $coreSettings->getSetting('sitecoretheme.twitter.feed', 0);
    $this->view->mobile = $coreSettings->getSetting('sitecoretheme.mobile', '+1-777-777-7777');
    $this->view->mail = $coreSettings->getSetting('sitecoretheme.mail', 'info@test.com');
    $this->view->website = $coreSettings->getSetting('sitecoretheme.website', 'www.example.com');
    $this->view->twitterCode = $coreSettings->getSetting('sitecoretheme.twitterCode', '');

    $this->view->social_links_array = $social_link_array = $coreSettings->getSetting('sitecoretheme.social.links', array());
    if (!empty($social_link_array)) {
      if (in_array('facebooklink', $social_link_array)) {
        $this->view->facebook_url = $coreSettings->getSetting('sitecoretheme.facebook.url', 'http://www.facebook.com/');
        $this->view->facebook_title = $coreSettings->getSetting('sitecoretheme.facebook.title', 'Like us on Facebook');
      }

      if (in_array('pininterestlink', $social_link_array)) {
        $this->view->pinterest_url = $coreSettings->getSetting('sitecoretheme.pinterest.url', 'https://www.pinterest.com/');
        $this->view->pinterest_title = $coreSettings->getSetting('sitecoretheme.pinterest.title', 'Pinterest');
      }

      if (in_array('twitterlink', $social_link_array)) {
        $this->view->twitter_url = $coreSettings->getSetting('sitecoretheme.twitter.url', 'https://www.twitter.com/');
        $this->view->twitter_title = $coreSettings->getSetting('sitecoretheme.twitter.title', 'Follow us on Twitter');
      }

      if (in_array('youtubelink', $social_link_array)) {
        $this->view->youtube_url = $coreSettings->getSetting('sitecoretheme.youtube.url', 'http://www.youtube.com/');
        $this->view->youtube_title = $coreSettings->getSetting('sitecoretheme.youtube.title', 'Youtube');
      }

      if (in_array('linkedinlink', $social_link_array)) {
        $this->view->linkedin_url = $coreSettings->getSetting('sitecoretheme.linkedin.url', 'https://www.linkedin.com/');
        $this->view->linkedin_title = $coreSettings->getSetting('sitecoretheme.linkedin.title', 'LinkedIn');
      }
    }

    $languagePath = APPLICATION_PATH . '/application/languages';
    $this->view->navigation_menus = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_footer');

    // Languages
    $translate = Zend_Registry::get('Zend_Translate');
    $languageList = $translate->getList();

    // Prepare default langauge
    $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
    if ($defaultLanguage == 'auto') {
      $defaultLanguage = 'en';
    }

    // Init default locale
    $localeObject = Zend_Registry::get('Locale');
    $languages = Zend_Locale::getTranslationList('language', $localeObject);
    $territories = Zend_Locale::getTranslationList('territory', $localeObject);

    $localeMultiOptions = array();
    foreach ($languageList as $key) {
      $dir = $languagePath . '/' . $key;
      if (!is_dir($dir)) {
        continue;
      }

      $languageName = null;
      if (!empty($languages[$key])) {
        $languageName = $languages[$key];
      } else {
        $tmpLocale = new Zend_Locale($key);
        $region = $tmpLocale->getRegion();
        $language = $tmpLocale->getLanguage();
        if (!empty($languages[$language]) && !empty($territories[$region])) {
          $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
        }
      }

      if ($languageName) {
        $localeMultiOptions[$key] = $languageName . '';
      }
    }

    if (!isset($localeMultiOptions[$this->view->defaultLanguage])) {
      $defaultLanguage = 'en';
    }

    $this->view->defaultLanguage = $defaultLanguage;
    $this->view->languageNameList = $localeMultiOptions;

    // Get affiliate code
    $this->view->affiliateCode = Engine_Api::_()->getDbtable('settings', 'core')->core_affiliate_code;
  }

}