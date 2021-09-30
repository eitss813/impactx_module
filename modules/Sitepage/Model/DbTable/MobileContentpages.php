<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contentpages.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_MobileContentpages extends Engine_Db_Table implements Engine_Content_Storage_Interface {

  protected $_rowClass = "Sitepage_Model_MobileContentpage";

  public function loadMetaData(Engine_Content $contentAdapter, $name) {
  	
    $select = $this->select()->where('name = ?', $name)->orWhere('mobilecontentpage_id = ?', $name);
    $page = $this->fetchRow($select);

    if (!is_object($page)) {
      //throw?
      return null;
    }

    return $page->toArray();
  }

  public function loadContent(Engine_Content $contentAdapter, $name) {
  	
    $sitepage_id = Engine_Api::_()->core()->getSubject()->getIdentity();

    if (is_array($name)) {
      $name = join('_', $name);
    }
    if (!is_string($name) && !is_numeric($name)) {
      throw new Exception('not string');
    }
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()->where('page_id = ?', $sitepage_id)->where('name = ?', $name)->orWhere('mobilecontentpage_id = ?', $name);
    $page = $this->fetchRow($select);

    if (is_object($page)) {
			$contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
			$select = $contentTable->select()
							->where('mobilecontentpage_id = ?', $page->mobilecontentpage_id)
							->order('order ASC');
			$content = $contentTable->fetchAll($select);

			$structure = $this->prepareContentArea($content);
			$element = new Engine_Content_Element_Container(array(
									'class' => 'layout_page_' . $page->name,
									'elements' => $structure
							));
    } else {
      $page_id = Engine_Api::_()->sitepage()->getMobileWidgetizedPage()->page_id;
			$contentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitepage');
			$select = $contentTable->select()
							->where('page_id = ?', $page_id)
							->order('order ASC');
			$content = $contentTable->fetchAll($select);
			$structure = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitepage')->prepareContentArea($content);
			$element = new Engine_Content_Element_Container(array(
									'class' => 'layout_page_sitepage_index_view',
									'elements' => $structure
							));
    }
    


    return $element;
  }

  public function prepareContentArea($content, $current = null) {

    $parent_content_id = null;
    if (null !== $current) {
      $parent_content_id = $current->mobilecontent_id;
    }

    $children = $content->getRowsMatching('parent_content_id', $parent_content_id);
    if (empty($children) && null === $parent_content_id) {
      $children = $content->getRowsMatching('parent_content_id', 0);
    }

    $struct = array();
    foreach ($children as $child) {
      $elStruct = $this->createElementParams($child);
      $elStruct['elements'] = $this->prepareContentArea($content, $child);
      $struct[] = $elStruct;
    }
    $Modules = array("offer" => "sitepageoffer", "form" => "sitepageform", "invite" => "sitepageinvite", "sdcreate" => "sitepagedocument", "sncreate" => "sitepagenote", "splcreate" => "sitepagepoll", "secreate" => "sitepageevent", "svcreate" => "sitepagevideo", "spcreate" => "sitepagealbum", "sdicreate" => "sitepagediscussion", "smcreate" => "sitepagemusic");
    $subject = Engine_Api::_()->core()->getSubject('sitepage_page');
    foreach ($struct as $keys => $valuess) {
      $unsetFlage = false;
      $explode_modulename_array = explode('.', $valuess['name']);
      $explode_modulename = $explode_modulename_array[0];
      $search_Key = "";
      $search_Key = array_search($explode_modulename, $Modules);
      if ($explode_modulename == 'sitepage') {
        if ($valuess['name'] == 'sitepage.photos-sitepage' || $valuess['name'] == 'sitepage.photorecent-sitepage' || $valuess['name'] == 'sitepage.albums-sitepage') {
          $explode_modulename = 'sitepagealbum';
          $search_Key = 'spcreate';
        }
        if ($valuess['name'] == 'sitepage.discussion-sitepage') {
          $explode_modulename = 'sitepagediscussion';
          $search_Key = 'sdicreate';
        }
        if ($valuess['name'] == 'sitepage.overview-sitepage') {
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'overview');
          if (empty($isManageAdmin)) {
            $unsetFlage = true;
          }
        }
        if ($valuess['name'] == 'sitepage.location-sitepage') {
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'map');
          if (empty($isManageAdmin)) {
            $unsetFlage = true;
          }
        }
      }
      if (!empty($search_Key)) {
        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", $explode_modulename)) {
            $unsetFlage = true;
          }
        } else {
          $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, $search_Key);
          if (empty($isPageOwnerAllow)) {
            $unsetFlage = true;
          }
        }
      }
      if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
        if ($valuess['name'] == 'sitepage.thumbphoto-sitepage') {
          unset($struct[$keys]);
        }
      }

      if ($unsetFlage)
        unset($struct[$keys]);
    }
    return $struct;
  }

  public function createElementParams($row) {
  	
    $data = array(
        'identity' => $row->mobilecontent_id,
        'type' => $row->type,
        'name' => $row->name,
        'order' => $row->order,
        'widget_admin' => $row->widget_admin,
    );
    $params = (array) $row->params;
    if (isset($params['title']))
      $data['title'] = $params['title'];
    $data['params'] = $params;

    return $data;
  }

  public function deletePage(Sitepage_Model_Page $page) {

    Engine_Api::_()->getDbtable('mobileContent', 'sitepage')->delete(array('mobilecontentpage_id = ?' => $page->mobilecontentpage_id));    
    $page->delete();
    return $this;
  }
  
  /**
   * Gets contentpage_id,description,keywords
   *
   * @param int $page_id
   * @return contentpage_id,description,keywords
   */     
  public function getContentPageId($page_id) {
  	
  	$selectPageAdmin = $this->select()            
            ->from($this->info('name'), array('mobilecontentpage_id', 'description', 'keywords'))  
            ->where('name = ?', 'sitepage_index_view')           
            ->where('page_id =?', $page_id)
            ->limit(1);
    return $this->fetchRow($selectPageAdmin);
  }

}

?>