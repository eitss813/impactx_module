<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Api_Language extends Core_Api_Abstract {

  protected $_languagePath;
  protected $_defaultLanguagePath;
  protected $_hasUnlinkFlag = true;

  public function __construct() {
    $this->_languagePath = APPLICATION_PATH . '/application/languages';
    $this->_defaultLanguagePath = APPLICATION_PATH . '/application/languages/en/';
  }

  public function getDataWithoutKeyPhase($flag = null, $page = null, $pages = null) {
  
		$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		
	  $plural	= $pages; //$coreSettings->getSetting( "language.phrases.pages" ,'pages');
		$singular = $page; //$coreSettings->getSetting( "language.phrases.page" ,'page');
		if (!empty($flag)) {
		return array('text_pages' => 'pages', 'text_page' => 'page');
		} else {
    return array('text_pages' => $plural, 'text_page' => $singular);
    }
  }
  
  public function hasDirectoryPermissions() {
    $flage = false;
    $test = new Engine_Sanity(array(
						'basePath' => APPLICATION_PATH,
							'tests' => array(
								array(
									'type' => 'FilePermission',
									'name' => 'Language Directory Permissions',
									'path' => 'application/languages',
									'value' => 7,
									'recursive' => true,
									'messages' => array(
										'insufficientPermissions' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory',
									),
								),
							),
						));
    $test->run();
    foreach ($test->getTests() as $stest) {
      $errorLevel = $stest->getMaxErrorLevel();
      if (empty($errorLevel))
        $flage = true;
    }
    return $flage;
  }
  
  public function setUnlinkFlag($flage=true) {
    $this->_hasUnlinkFlag = $flage;
  }

  public function checkLocal($locale='en') {
    // Check Locale
    $locale = Zend_Locale::findLocale();
    // Make Sure Language Folder Exist
    $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
    if ($languageFolder === false) {
      $locale = substr($locale, 0, 2);
      $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
    }
    return $languageFolder;
  }

  public function addLanguageFile($fileName, $locale ='en', $replaceDatas=array(), $replaceDataWithoutKey=array(), $oldFileName=null) {
    if (empty($fileName) || !$this->checkLocal($locale)) {
      return;
    }
    $output = array();
    $dataLocale = array();

    $output = $dataEn = $this->loadTranslationData('en', $fileName);

    if (empty($output))
      return;
    $output = $this->convertData($output, $replaceDatas, $replaceDataWithoutKey);
    $language_file = $this->_languagePath . '/' . $locale . '/' . $fileName;

    if ($this->_hasUnlinkFlag && file_exists($language_file)) {
      @unlink($language_file);
    }

    touch($language_file);
    chmod($language_file, 0777);

    $export = new Engine_Translate_Writer_Csv($language_file);
    $export->setTranslations($output);
    $export->write();
  }

  public function addLanguageFiles($fileName, $replaceDatas=array(), $replaceDataWithoutKey=array(), $oldFileName=null) {
    $translate = Zend_Registry::get('Zend_Translate');

    // Prepare language list
    $languageList = $translate->getList();
    foreach ($languageList as $key) { 
      $this->addLanguageFile($fileName, $key, $replaceDatas, $replaceDataWithoutKey, $oldFileName);
    }
  }

  protected function loadTranslationData($locale='en',$filename = null,  array $options = array()) {
    $file_data = array();
    $options['delimiter'] = ";";
    $options['length'] = 0;
    $options['enclosure'] = '"';
    $filename = APPLICATION_PATH . '/application/languages/en/'. $filename;
    $tmp = Engine_Translate_Parser_Csv::parse($filename, 'null', $options);
    if (!empty($tmp['null']) && is_array($tmp['null'])) {
      $file_data = $tmp['null'];
    } else {
      $file_data = array();
    }
    return $file_data;
  }

  public function getReplaceDataWithoutKey($listType, $flag = null, $page = null, $pages = null) {
    $replaceWithOutKeyDatas = array();
    $replaceWithOutKeyDatasDefault = $listType;
    if(empty ($replaceWithOutKeyDatasDefault))
      return;
    $defaultPhase = $this->getDataWithoutKeyPhase($flag, $page, $pages);
    foreach ($replaceWithOutKeyDatasDefault as $arraykey => $data) {
      if (!isset($defaultPhase[$arraykey]))
        continue;
        
			if (!empty($flag)) { 
				$key = $defaultPhase[$arraykey];
			} else {
				$key = $defaultPhase[$arraykey];
			}
     
      $replaceWithOutKeyDatas[strtolower($key)] = strtolower($data);
      $replaceWithOutKeyDatas[ucfirst($key)] = ucfirst($data);
      $replaceWithOutKeyDatas[strtoupper($key)] = strtoupper($data);
      $replaceWithOutKeyDatas[ucwords($key)] = ucwords($data);
    }
    return $replaceWithOutKeyDatas;
  }

  public function setTranslateForListType($listType, $flag= null, $page = null, $pages = null) {

		$coreModulesTable = Engine_Api::_()->getDbtable('modules', 'core');
		$coreModulesTableName = $coreModulesTable->info('name');
		$select = $coreModulesTable->select()
													->from($coreModulesTableName)
													->where($coreModulesTableName . '.name LIKE ?', '%' . 'sitepage' . '%');
		$datas = $coreModulesTable->fetchAll($select);
		foreach ($datas as $data) {
			$fileName =   $data['name'] . '.csv';
		  $oldFileName = null;
			$replaceDatas = array();
			
			$replaceDataWithoutKey = $this->getReplaceDataWithoutKey($listType, $flag, $page, $pages);
			$this->addLanguageFiles($fileName, $replaceDatas, $replaceDataWithoutKey, $oldFileName);
    }
  }

  public function convertData($datas, $replaceDatas, $replaceDataWithoutKey) {
    $data = array();
    foreach ($datas as $data_key => $data) {
      foreach ($replaceDataWithoutKey as $search => $replace) { 
      
        $data = str_replace($search, $replace, $data); 
				if (strstr($data, $replace . "_title")) {
					$data = str_replace($replace . "_title", 'page_title', $data);
				}
				if (strstr($data, $replace . "_description")) {
					$data = str_replace($replace . "_description", 'page_description', $data);
				}
				if (strstr($data, $replace . "_title_with_link")) {
					$data = str_replace($replace . "_title_with_link", 'page_title_with_link', $data);
				}
				if (strstr($data, $replace . "_url")) {
					$data = str_replace($replace . "_url", 'page_url', $data);
				}
      }
      $datas[$data_key] = $data;
    }
    return $datas;
  }
  
  public function languageChanges() {
                  return ;
  		//START LANGUAGE WORK
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_settings')
			->where('name = ?', 'language.phrases.page');
		$language_page = $select->query()->fetch();

		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_settings')
			->where('name = ?', 'language.phrases.pages');
		$language_pages = $select->query()->fetch();
		if (isset($language_pages['value']) && $language_pages['value'] != 'pages' && isset($language_page['value'])  && $language_page['value'] != 'page') {

            $language_pharse = array('text_pages' => '$plural$' , 'text_page' => '$singular$'); 

            $this->setTranslateForListType($language_pharse, '', 'page', 'pages');

            $language_pharse = array('text_pages' => $language_pages['value'], 'text_page' => $language_page['value']); 

            $this->setTranslateForListType($language_pharse, '', '$singular$', '$plural$'); 
		}
		//END LANGUAGE WORK
  }
  
}