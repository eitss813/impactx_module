<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Api_Core extends Core_Api_Abstract {

    
    public function displayPhoto($id, $type = 'thumb.profile') {
        if (empty($id)) {
            return null;
        }
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($id, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    } 
    
    public function isModulesSupport() {
        $isVerticalActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.isActivate', 0);
        if (empty($isVerticalActivate))
            return array();

        $modArray = array(
            'siteevent' => '4.8.8p3',
            'siteeventticket' => '4.8.8p3',
            'sitecontentcoverphoto' => '4.8.8p5',
            'siteusercoverphoto' => '4.8.8p4',
            'sitereview' => '4.8.8p1',
            'sitereviewlistingtype' => '4.8.8p1',
            'sitealbum' => '4.8.8p1',
            'sitemenu' => '4.8.8p3'
        );
        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $isModEnabled = Engine_Api::_()->hasModuleBootstrap($key);
            if (!empty($isModEnabled)) {
                $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
                $isModSupport = $this->checkVersion($getModVersion->version, $value);
                if (!$isModSupport) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }
        return $finalModules;
    }

    public function setImageOrder($imageArray, $order) {
      if( !empty($order) && $order == 1 ) {
        $imageArray = @array_reverse($imageArray);
      } else if( !empty($order) && $order == 2 ) {
        @shuffle($imageArray);
      }
      return $imageArray;
    }

    function checkVersion($databaseVersion, $checkDependancyVersion) {
        if (strcasecmp($databaseVersion, $checkDependancyVersion) == 0)
            return -1;
        $databaseVersionArr = explode(".", $databaseVersion);
        $checkDependancyVersionArr = explode('.', $checkDependancyVersion);
        $fValueCount = $count = count($databaseVersionArr);
        $sValueCount = count($checkDependancyVersionArr);
        if ($fValueCount > $sValueCount)
            $count = $sValueCount;
        for ($i = 0; $i < $count; $i++) {
            $fValue = $databaseVersionArr[$i];
            $sValue = $checkDependancyVersionArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                $result = $this->compareValues($fValue, $sValue);
                if ($result == -1) {
                    if (($i + 1) == $count) {
                        return $this->compareValues($fValueCount, $sValueCount);
                    } else
                        continue;
                }
                return $result;
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);
                $result = $this->compareValues($fsArr[0], $sValue);
                return $result == -1 ? 1 : $result;
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);
                $result = $this->compareValues($fValue, $ssArr[0]);
                return $result == -1 ? 0 : $result;
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                $result = $this->compareValues($fsArr[0], $ssArr[0]);
                if ($result != -1)
                    return $result;
                $result = $this->compareValues($fsArr[1], $ssArr[1]);
                return $result;
            }
        }
    }

    public function compareValues($firstVal, $secondVal) {
        $num = $firstVal - $secondVal;
        return ($num > 0) ? 1 : ($num < 0 ? 0 : -1);
    } 
    
    public function getWidgetizedPageLayoutValue($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
        $select = $tableNamePages->select()
                ->from($tableNamePages->info('name'), 'layout');

        if (isset($params['name'])) {
            $select->where('name =?', $params['name']);
        }
        if (isset($params['page_id'])) {
            $select->where('page_id =?', $params['page_id']);
        }
        $layout = $select->query()
                ->fetchColumn();
        return $layout;
    }

    
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

    public function getContentPageId($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
        $page_id = $tableNameContent->select()
                ->from($tableNameContent->info('name'), 'page_id')
                ->where('content_id =?', $params['content_id'])
                ->query()
                ->fetchColumn();
        return $page_id;
    }
    public function getRecentUserCount($params = array()) {
        //GET CORE SEARCH TABLE
        $searchTable = Engine_Api::_()->getDbtable('search', 'core');
        $searchTableName = $searchTable->info('name');
        if( strpos($params['itemType'], 'sitereview_listing_') !== false ) {
            $listingTypeId = str_replace('sitereview_listing_', '', $params['itemType']);
            $itemType = 'sitereview_listing';
        }
        else {
            $itemType = $params['itemType'];
        }
        $limit = $params['limit'];
        $sortBy = $params['sortBy'];
        $table = Engine_Api::_()->getItemTable($itemType);
        $keys = $table->info(Zend_Db_Table::PRIMARY);
        $primaryKey = array_shift($keys);
        $tableName = $table->info('name');
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sort_column_exist = $db->query("SHOW COLUMNS FROM " . $tableName . " LIKE '" . $sortBy . "'")->fetch();
        if( !$sort_column_exist ) {
          $sortBy = 'creation_date';
        }
        $select = $table->select()->from($tableName)
            ->join($searchTableName, "$searchTableName.id = $tableName.$primaryKey", null)
            ->where("$searchTableName.type = ?", $itemType)
            ->order("$sortBy DESC")->limit($limit);
        if( $listingTypeId && $itemType === 'sitereview_listing' ) {
          $select->where("$tableName.listingtype_id = ?", $listingTypeId);
        }
        $results = $table->fetchAll($select);
        if( !count($results) ) {
          return false;
        }
        else {
            return true;
        }
    } 

}