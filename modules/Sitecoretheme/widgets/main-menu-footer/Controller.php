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
class Sitecoretheme_Widget_MainMenuFooterController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $languagePath = APPLICATION_PATH . '/application/languages';
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_footer');

    $this->view->socialMenusNavigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_social_sites');
    // Languages
    $translate = Zend_Registry::get('Zend_Translate');
    $languageList = $translate->getList();

    //$currentLocale = Zend_Registry::get('Locale')->__toString();
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
    $this->view->logoParams = array('logo' => $this->_getParam('logoPath'));
  }

  public function getCacheKey() {
    //return true;
  }

  public function setLanguage() {
    
  }

}