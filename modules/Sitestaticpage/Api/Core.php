<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Api_Core extends Core_Api_Abstract {

  /**
   * Return merge_array
   *
   * @return $merge_array
   */
  public function getBannedUrls() {

    $merge_array = array();
    $businessUrlFinalArray = array();
    $groupUrlFinalArray = array();
    $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    $urlArray = $bannedPageurlsTable->select()->from($bannedPageurlsTable, 'word')
                    ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    // CHECKING IN SITEBUSINESS PLUGIN
    $enableSitebusiness = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');

    if ($enableSitebusiness) {
      $businessTable = Engine_Api::_()->getDbtable('business', 'sitebusiness');
      $businessUrlArray = $businessTable->select()->from($businessTable, ('business_url'))
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach ($businessUrlArray as $url) {
        $businessUrlFinalArray[] = strtolower($url);
      }
      if (!empty($businessUrlFinalArray)) {
        $merge_array = array_merge($urlArray, $businessUrlFinalArray);
      } else {
        $merge_array = $urlArray;
      }
    } else {
      $merge_array = $urlArray;
    }

    // CHECKING IN SITEGROUP PLUGIN
    $enableSitegroup = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');

    if ($enableSitegroup) {
      $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
      $groupUrlArray = $groupTable->select()->from($groupTable, 'group_url')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach ($groupUrlArray as $url) {
        $groupUrlFinalArray[] = strtolower($url);
      }
      if (!empty($groupUrlFinalArray)) {
        $merge_array = array_merge($merge_array, $groupUrlFinalArray);
      } else {
        $merge_array = $merge_array;
      }
    }

    // CHECKING IN SITEPAGE PLUGIN
    $enableSitepage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');

    if ($enableSitepage) {
      $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
      $pageUrlArray = $pageTable->select()->from($pageTable, 'page_url')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach ($pageUrlArray as $url) {
        $pageUrlFinalArray[] = strtolower($url);
      }
      if (!empty($pageUrlFinalArray)) {
        $merge_array = array_merge($merge_array, $pageUrlFinalArray);
      } else {
        $merge_array = $merge_array;
      }
    }

    // CHECKING IN SITESTORE PLUGIN
    $enableSitestore = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');

    if ($enableSitestore) {
      $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
      $storeUrlArray = $storeTable->select()->from($storeTable, 'store_url')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach ($storeUrlArray as $url) {
        $storeUrlFinalArray[] = strtolower($url);
      }
      if (!empty($storeUrlFinalArray)) {
        $merge_array = array_merge($merge_array, $storeUrlFinalArray);
      }
    } else {
      $merge_array = $merge_array;
    }
    return $merge_array;
  }

  /**
   * Get page id
   *
   * @param string $page_url
   * @return int $pageID
   */
  public function getPageId($page_url, $pageId = null, $subjectPageId = null) {

    $pageID = 0;
    $db = Engine_Db_Table::getDefaultAdapter();
    if (!empty($page_url)) {
      $sitestaticpage_table = Engine_Api::_()->getItemTable('sitestaticpage_page');
      if (!empty($pageId)) {
        $pageID = $db->select('page_id')
                ->from('engine4_sitestaticpage_pages')
                ->where('page_url = ?', $page_url)
                ->where('page_id != ?', $pageId)
                ->limit(1)
                ->query()
                ->fetchColumn();
      } else {
        $pageID = $db->select('page_id')
                ->from('engine4_sitestaticpage_pages')
                ->where('page_url = ?', $page_url)
                ->limit(1)
                ->query()
                ->fetchColumn();
      }
    } elseif (!empty($subjectPageId)) {
      $pageID = $db->select('page_id')
              ->from('engine4_sitestaticpage_pages')
              ->order('page_id ASC')
              ->limit(1)
              ->query()
              ->fetchColumn();
    }

    return $pageID;
  }

  /**
   * Get language array
   *
   * @param string $page_url
   * @return array $localeMultiOptions
   */
  public function getLanguageArray() {

    //PREPARE LANGUAGE LIST
    $languageList = Zend_Registry::get('Zend_Translate')->getList();

    //PREPARE DEFAULT LANGUAGE
    $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
    if (!in_array($defaultLanguage, $languageList)) {
      if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
        $defaultLanguage = 'en';
      } else {
        $defaultLanguage = null;
      }
    }
    //INIT DEFAULT LOCAL
    $localeObject = Zend_Registry::get('Locale');
    $languages = Zend_Locale::getTranslationList('language', $localeObject);
    $territories = Zend_Locale::getTranslationList('territory', $localeObject);

    $localeMultiOptions = array();
    foreach ($languageList as $key) {
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
        $localeMultiOptions[$key] = $languageName;
      } else {
        $localeMultiOptions[$key] = Zend_Registry::get('Zend_Translate')->_('Unknown');
      }
    }
    $localeMultiOptions = array_merge(array(
        $defaultLanguage => $defaultLanguage
            ), $localeMultiOptions);
    return $localeMultiOptions;
  }

  /**
   * Get language column
   *
   * @param string $column_type
   * @return string $column_value
   */
  public function getLanguageColumn($column_type) {

    //RETURN IF COLUMN TYPE IS EMPTY
    if (empty($column_type)) {
      return;
    }

    //GET LANGUAGE SETTINGS
    $multilanguage_support = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.multilanguage', 0);
    $languages = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.languages');

    //GET THE CURRENT LANGUAGE
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $locale = $view->locale()->getLocale()->__toString();

    //RETURN COLUMN TYPE
    if (empty($multilanguage_support) || (!in_array($locale, $languages) && $locale == 'en')) {
      return $column_type;
    } else {
      $column_value = $column_type;
      $column_name = "$column_type" . "_$locale";

      $db = Engine_Db_Table::getDefaultAdapter();
      $column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestaticpage_pages LIKE '$column_name'")->fetch();

      if (!empty($column_exist)) {
        return $column_name;
      }
    }
    //RETURN VALUE 
    return $column_value;
  }

  /**
   * Get member Networks array
   *
   */
  public function getNetworks() {

    //GET NETWORK TABLE
    $tableNetwork = Engine_Api::_()->getDbtable('networks', 'network');

    //MAKE QUERY
    $select = $tableNetwork->select()
            ->from($tableNetwork->info('name'), array('network_id', 'title'))
            ->order('title');
    $result = $tableNetwork->fetchAll($select);

    $everyone = Zend_Registry::get('Zend_Translate')->_('Everyone');

    //MAKE DATA ARRAY
    $networksOptions = array('0' => $everyone);
    foreach ($result as $value) {
      $networksOptions[$value->network_id] = $value->title;
    }

    //RETURN
    return $networksOptions;
  }

  /**
   * Get Widgetized Page info
   */
  public function getWidgetizedPageInfo() {

    //PREPARE LANGUAGE LIST
    $languageList = Zend_Registry::get('Zend_Translate')->getList();

    //PREPARE DEFAULT LANGUAGE
    $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
    $sitestaticpageGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.global.type', null);
    $sitestaticpageManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.manage.type', null);
    $sitestaticpageInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.info.type', null);
    if (!empty($languaeWidgetType) && !in_array($defaultLanguage, $languageList)) {
      if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
        $defaultLanguage = 'en';
      } else {
        $defaultLanguage = null;
      }
    }
    //INIT DEFAULT LOCAL
    $localeObject = Zend_Registry::get('Locale');
    $languages = Zend_Locale::getTranslationList('language', $localeObject);
    $territories = Zend_Locale::getTranslationList('territory', $localeObject);
    $sitestaticpageHostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    $sitestaticpageLanSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.lsettings', null);

    $localeMultiOptions = array();
    $sitestaticpageMainManageType = $sitestaticpageMainInfoType = null;
    if (empty($languaeWidgetType)) {
      for ($languageNum = 0; $languageNum < strlen($sitestaticpageHostType); $languageNum++) {
        $sitestaticpageMainManageType += @ord($sitestaticpageHostType[$languageNum]);
      }
      for ($languageNum = 0; $languageNum < strlen($sitestaticpageLanSettings); $languageNum++) {
        $sitestaticpageMainInfoType += @ord($sitestaticpageLanSettings[$languageNum]);
      }
      if (empty($sitestaticpageGlobalType) && (($sitestaticpageManageType != $sitestaticpageMainManageType) || ($sitestaticpageInfoType != $sitestaticpageMainInfoType))) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestaticpage.viewtype.type', 0);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestaticpage.viewtype.settings', 0);
        return false;
      } else {
        return true;
      }
    } else {
      foreach ($languageList as $sitestaticpageLanSettings) {
        $languageName = null;
        if (!empty($languages[$sitestaticpageLanSettings])) {
          $languageName = $languages[$sitestaticpageLanSettings];
        } else {
          $tmpLocale = new Zend_Locale($sitestaticpageLanSettings);
          $region = $tmpLocale->getRegion();
          $language = $tmpLocale->getLanguage();
          if (!empty($languages[$language]) && !empty($territories[$region])) {
            $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
          }
        }

        if ($languageName) {
          $localeMultiOptions[$sitestaticpageLanSettings] = $languageName;
        } else {
          $localeMultiOptions[$sitestaticpageLanSettings] = Zend_Registry::get('Zend_Translate')->_('Unknown');
        }
      }
      $localeMultiOptions = array_merge(array(
          $defaultLanguage => $defaultLanguage
              ), $localeMultiOptions);
      return $localeMultiOptions;
    }
    return true;
  }

  /**
   * Get page_id
   *
   * @param int $page_id
   * @return int page_id
   */
  public function getWidetizedpageId($page_id) {

    //GET NETWORK TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    //MAKE QUERY
    $page_id = $pageTable->select()
            ->from($pageTable->info('name'), array('page_id'))
            ->where("name =?", "sitestaticpage_index_index_staticpageid_" . $page_id)
            ->limit(1)
            ->query()
            ->fetchColumn();
    if (!empty($page_id))
      return $page_id;
    else
      return 0;
  }

  public function getMobileWidetizedpageId($page_id) {

    //GET NETWORK TABLE
    if ($this->isSitemobileEnabled()) {
      $sitemobilepageTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
      $db = Engine_Db_Table::getDefaultAdapter();
      $name_widgetizedpage = "sitestaticpage_index_index_staticpageid_" . $page_id;
      $page_id = $sitemobilepageTable->select()
                ->from($sitemobilepageTable->info('name'), array('page_id'))
                ->where("name =?", "sitestaticpage_index_index_staticpageid_" . $page_id)
                ->limit(1)
                ->query()
                ->fetchColumn();
      if (!empty($page_id))
        return $page_id;
      else
        return 0;
    }
  }

  /**
   * Get string
   *
   * @param string $string
   * @param string $start
   * @param string $end
   */
  function getstring($string, $start, $end) {

    $found = array();
    $pos = 0;
    while (true) {
      $pos = strpos($string, $start, $pos);
      if ($pos === false) { // Zero is not exactly equal to false...
        return $found;
      }
      $pos += strlen($start);
      $len = strpos($string, $end, $pos) - $pos;
      $found[] = substr($string, $pos, $len);
    }
  }

  /**
   * Set Meta Titles
   *
   * @param array $params
   */
  public function setMetaTitles($params = array()) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $siteinfo = $view->layout()->siteinfo;
    $titles = $siteinfo['title'];

    if (isset($params['page_title']) && !empty($params['page_title'])) {
      if (!empty($titles))
        $titles .= ' - ';
      $titles .= $params['page_title'];
    }

    $siteinfo['title'] = $titles;
    $view->layout()->siteinfo = $siteinfo;
  }

  public function setMetaDescriptionsBrowse($params = array()) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $siteinfo = $view->layout()->siteinfo;
    $descriptions = '';
    if (isset($params['page_description'])) {
      if (!empty($descriptions))
        $descriptions .= ' - ';
      $descriptions .= $params['page_description'];
    }

    $siteinfo['description'] = $descriptions;
    $view->layout()->siteinfo = $siteinfo;
  }

  public function setMetaKeywords($params = array()) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $siteinfo = $view->layout()->siteinfo;
    $keywords = "";

    if (isset($params['keywords'])) {
      if (!empty($keywords))
        $keywords .= ', ';
      $keywords .= $params['keywords'];
    }
    $siteinfo['keywords'] = $keywords;
    $view->layout()->siteinfo = $siteinfo;
  }

  public function isSitemobileEnabled() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $enableSitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
    if($enableSitemobile){
    $enableSitestaticpage = $db->select()
            ->from('engine4_sitemobile_modules', 'integrated')
            ->where('name = ?', 'sitestaticpage')
            ->where('integrated = ?', 1)
            ->query()
            ->fetchColumn();
    }
    if ($enableSitemobile && $enableSitestaticpage)
      return 1;
    else
      return 0;
  }
  
  public function getElementParams($spec, array $additionalParams = array(), $user_id = null, $field) {
    $params = $field->toArray();

    $info = Engine_Api::_()->fields()->getFieldInfo($params['type']);

    $name = $params['field_id'];
    $field_type = $params['type'];
    $type = Engine_Api::_()->fields()->inflectFieldType($params['type']);
    $config = $params['config'];

    unset($params['field_id']);
    unset($params['type']);
    unset($params['config']);
    unset($params['parent_option_id']);
    unset($params['parent_field_id']);
    //unset($params['alias']);
    unset($params['error']);
    unset($params['display']);
    unset($params['search']);

    $params['allowEmpty'] = !@$params['required'];
    if(isset($params['field_id']))
        $params['data-field-id'] = $params['field_id'];

    if (!is_array($config))
      $config = array();
    $options = array_merge($config, $params, $additionalParams);

    // Clean out null and "NULL" values
    foreach ($options as $index => $option) {
      // Note: Don't do empty() here, there may be false values
      if (is_null($options[$index])) {
        unset($options[$index]);
      }
      if (is_string($option) && strtoupper($option) == 'NULL') {
        unset($options[$index]);
      }
    }

    // Process multi options
    if ($this->canHaveDependents($field_type)) {
      $options['multiOptions'] = array();
      if (/* empty($config['required']) && */ empty($info['multi']) && $field_type != 'radio') {
        $options['multiOptions'][''] = '';
      }

      foreach ($this->getOptions($name) as $option) {
        $options['multiOptions'][$option->option_id] = $option->label;
      }
    }

    // or just regular options
    else if (!empty($info['multiOptions'])) {
      $options['multiOptions'] = $info['multiOptions'];
      if (/* empty($config['required']) && */ empty($info['multi'])) {
        $options['multiOptions'] = array_merge(array('' => ''), $options['multiOptions']);
      }
    }

    // Process value
    if ($spec instanceof Core_Model_Item_Abstract) {
//      if(isset($user_id) && !empty($user_id))
//        $value = $this->getValue($spec, $user_id);
//      else
      $value = $this->getValue($spec, $user_id, $type, $name);
      if (is_array($value)) {
        $vals = array();
        foreach ($value as $singleValue) {
          $vals[] = $singleValue->value;
        }
        $options['value'] = $vals;
      } else if (is_object($value)) {
        $options['value'] = htmlspecialchars_decode($value->value);
      }
    }

    if (empty($user_id))
      unset($options['value']);
    return array(
        'type' => $type,
        'name' => $name,
        'options' => $options
    );
  }

  public function getValue($spec, $user_id = null, $type, $field_id) {
    // spec must be an instance of Core_Model_Item_Abstract
    if (!($spec instanceof Core_Model_Item_Abstract)) {
      throw new Fields_Model_Exception('$spec must be an instance of Core_Model_Item_Abstract');
    }

    if (!$spec->getIdentity()) {
      return null;
    }

    $values = Engine_Api::_()->fields()
            ->getFieldsValues($spec);

    if (!$values) {
      return null;
    }

    if (in_array($type, array('Multiselect', 'MultiCheckbox', 'PartnerGender', 'LookingFor'))) {
      if (isset($user_id) && !empty($user_id))
        return $values->getRowsMatching(array('field_id' => $field_id, 'member_id' => $user_id));
      else
        return $values->getRowsMatching('field_id', $field_id);
    } else {
      if (isset($user_id) && !empty($user_id))
        return $values->getRowMatching(array('field_id' => $field_id, 'member_id' => $user_id));
      else
        return $values->getRowMatching('field_id', $field_id);
    }
  }

  public function canHaveDependents($type) {
    return ( in_array($type, Engine_Api::_()->fields()->getFieldInfo('dependents')) );
  }

  public function getOptions($field_id) {
    return Engine_Api::_()->fields()
                    ->getFieldsOptions('sitestaticpage_page')
                    ->getRowsMatching('field_id', $field_id);
  }

}