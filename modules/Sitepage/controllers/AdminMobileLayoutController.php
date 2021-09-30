<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLayoutController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminMobileLayoutController extends Core_Controller_Action_Admin {

  public function saveAction() {

    $pageTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
    $pageObject = $pageTable->fetchAll($pageTable->select());
    $form = new Sitepage_Form_AdminLayout_Content_Page();
    $pagecoreTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
    $pagecoreObject = $pagecoreTable->fetchAll($pagecoreTable->select()->where('name =?', 'sitepage_index_view'));
    $form->populate($pageObject->toArray());
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $pageTable->update(array('description' => $values['description'], 'keywords' => $values['keywords']), array('name = ?' => 'sitepage_index_view'));
      $pagecoreTable->update(array('description' => $values['description'], 'keywords' => $values['keywords']), array('name = ?' => 'sitepage_index_view'));
      $form->addNotice($this->view->translate('Your changes have been saved.'));
    }

    $this->getResponse()->setBody($form->render($this->view));
    $this->_helper->layout->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }

  public function getContentAreas() {
    $contentAreas = array();
    // From modules
    $modulesDir = Zend_Controller_Front::getInstance()->getControllerDirectory();
    foreach ($modulesDir as $module => $path) {
      $contentManifestFile = dirname($path) . '/settings/sitemobile/content.php';
      if (!file_exists($contentManifestFile)) {
        $contentManifestFile = dirname($path) . '/settings/sitemobile_content.php';
        if (!file_exists($contentManifestFile))
          continue;
      }
      $ret = include $contentManifestFile;
      if (!is_array($ret))
        continue;
      for ($i = 0; $i < count($ret); $i++) {
        if (!isset($ret[$i]['module']))
          $ret[$i]['module'] = $module;
      }
      $contentAreas = array_merge($contentAreas, (array) $ret);
    }
    
    foreach ($modulesDir as $module => $path) {
      $explodePath = explode(DS, dirname($path));
      $moduleName = '/' . end($explodePath);
      $path = str_replace($moduleName, '/Sitemobile/modules' . $moduleName, implode('/', $explodePath));
      $contentManifestFile = $path . '/settings/sitemobile_content.php';

      if (!file_exists($contentManifestFile))
        continue;
      $ret = include $contentManifestFile;
      if (!is_array($ret))
        continue;
      for ($i = 0; $i < count($ret); $i++) {
        if (!isset($ret[$i]['module']))
          $ret[$i]['module'] = $module;
      }
      $contentAreas = array_merge($contentAreas, (array) $ret);
    }
    // From widgets
//    $it = new DirectoryIterator(APPLICATION_PATH . '/application/widgets');
//    foreach( $it as $dir ) {
//      if( !$dir->isDir() || $dir->isDot() ) continue;
//      $path = $dir->getPathname();
//      $contentManifestFile = $path . '/' . 'manifest.php';
//      if( !file_exists($contentManifestFile) ) continue;
//      $ret = include $contentManifestFile;
//      if( !is_array($ret) ) continue;
//      array_push($contentAreas, $ret);
//    }
    return $contentAreas;
  }

  public function buildCategorizedContentAreas($contentAreas) {

    $categorized = array();
    foreach ($contentAreas as $config) {
      //CHECK SOME STUFF
      if (!empty($config['requireItemType'])) {
        if (is_string($config['requireItemType']) && !Engine_Api::_()->hasItemType($config['requireItemType'])) {
          $config['disabled'] = true;
        } else if (is_array($config['requireItemType'])) {
          $tmp = array_map(array(Engine_Api::_(), 'hasItemType'), $config['requireItemType']);
          $config['disabled'] = !(array_sum($tmp) == count($config['requireItemType']));
        }
      }

      //ADD TO CATEGORY
      $category = ( isset($config['category']) ? $config['category'] : 'Uncategorized' );
      $categorized[$category][] = $config;
    }

    //SORT CATEGORIES
    uksort($categorized, array($this, '_sortCategories'));

    //SORT ITEMS IN CATEGORIES
    foreach ($categorized as $category => &$items) {
      usort($items, array($this, '_sortCategoryItems'));
    }

    return $categorized;
  }

  public function filterContentAreasByRequirements($contentAreas, $provides) {

    //PROCESS PROVIDES
    if (is_string($provides)) {
      $providedFeatures = explode(';', $provides);
      $provides = array();
      foreach ($providedFeatures as $providedFeature) {
        if (false === strpos($providedFeature, '=')) {
          $provides[$providedFeature] = true;
        } else {
          list($feature, $value) = explode('=', $providedFeature);
          if (false === strpos($value, ',')) {
            $provides[$feature] = $value;
          } else {
            $provides[$feature] = explode(',', $value);
          }
        }
      }
    } else if (!is_array($provides)) {
      $provides = array();
    }

    //PROCESS CONTENT AREAS
    $filteredContentAreas = array();
    foreach ($contentAreas as $category => $categoryWidgets) {
      foreach ($categoryWidgets as $widget) {
        $pass = true;
        //REQUIREMENTS
        if (!empty($widget['requirements']) && is_array($widget['requirements'])) {
          foreach ($widget['requirements'] as $k => $v) {
            if (is_numeric($k)) {
              $req = $v;
              $value = null;
            } else {
              $req = $k;
              $value = $v;
            }
            //NOTE: WILL CONTINUE IF MISSING ANY OF THE REQUIREMENTS
            switch ($req) {
              case 'viewer':
                if (isset($provides['no-viewer'])) {
                  $pass = false;
                }
                break;
              case 'no-viewer':
                if (isset($provides['viewer'])) {
                  $pass = false;
                }
                break;
              case 'subject':
                if (!isset($provides['subject']) /* ||
                  isset($provides['no-subject']) */) {
                  $pass = false;
                } else if (is_string($value)) {
                  if (is_string($provides['subject']) &&
                          $provides['subject'] == $value) {
                    
                  } else if (is_array($provides['subject']) &&
                          in_array($value, $provides['subject'])) {
                    
                  } else {
                    $pass = false;
                  }
                } else if (is_array($value)) {
                  if (count(array_intersect($value, (array) $provides['subject'])) <= 0) {
                    $pass = false;
                  }
                }
                break;
              case 'no-subject':
                if (isset($provides['subject']) /* ||
                  !isset($provides['no-subject']) */) {
                  $pass = false;
                }
                //@todo subject blacklist?
                break;
              case 'header-footer';
                if (!isset($provides['header-footer'])) {
                  $pass = false;
                }
                break;
            }
          }
        }
        //ADD TO AREAS
        if ($pass) {
          $filteredContentAreas[$category][] = $widget;
        }
      }
    }

    return $filteredContentAreas;
  }

  protected function _sortCategories($a, $b) {

    if ($a == 'Core')
      return -1;
    if ($b == 'Core')
      return 1;
    return strcmp($a, $b);
  }

  protected function _sortCategoryItems($a, $b) {

    if (!empty($a['special']))
      return -1;
    if (!empty($b['special']))
      return 1;
    return strcmp($a['title'], $b['title']);
  }

  protected function _reorderContentStructure($a, $b) {

    $sample = array('left', 'middle', 'right');
    $av = $a['name'];
    $bv = $b['name'];
    $ai = array_search($av, $sample);
    $bi = array_search($bv, $sample);
    if ($ai === false && $bi === false)
      return 0;
    if ($ai === false)
      return -1;
    if ($bi === false)
      return 1;
    $r = ( $ai == $bi ? 0 : ($ai < $bi ? -1 : 1) );
    return $r;
  }

  public function layoutAction() {
    set_time_limit(0);
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_layoutdefault');

    //GET PAGE PARAM
    $page = $this->_getParam('page', 'core_index_index');
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
    $pageTableName = $pageTable->info('name');

    $row = Engine_Api::_()->sitepage()->getMobileWidgetizedPage();
    if (!empty($row)) {
      $this->view->mobile_page_id = $row->page_id;
    }

    $row = Engine_Api::_()->sitepage()->getWidgetizedPage();
    if (!empty($row)) {
      $this->view->page_id = $row->page_id;
    }

    $contentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitepage');

    //GET CURRENT PAGE
    $this->view->pageObject = $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));

    if (null === $pageObject) {
      $page = 'core_index_index';
      $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page));
      if (null === $pageObject) {
        throw new Engine_Exception('Home page is missing');
      }
    }
    $this->view->page = $page;
    $this->view->pageObject = $pageObject;

    //MAKE PAGE FORM
    $this->view->pageForm = $pageForm = new Sitepage_Form_AdminLayout_Content_Page();
    if (!$pageObject->custom) {
      $pageForm->removeElement('levels');
    }

    $pageForm->populate($pageObject->toArray());
    $levels = $pageForm->getElement('levels');
    if ($levels && !empty($pageObject->levels)) {
      $levels->setValue(Zend_Json_Decoder::decode($pageObject->levels));
    } else if ($levels) {
      $levels->setValue(array_keys($levels->getMultiOptions()));
    }

    //GET PAGE LIST
    $this->view->pageList = $pageList = $pageTable->fetchAll();
    $userlayoutcontentpagesTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
    $this->view->totalPages = $userlayoutcontentpagesTable->select()->from($userlayoutcontentpagesTable->info('name'), array('count(*) as count'))->query()->fetchColumn();
    $this->view->limit = 500;
    //GET AVAILABLE CONTENT BLOCKS
    $contentAreas = $this->buildCategorizedContentAreas($this->getContentAreas());
    if (!$this->_getParam('show-all', true)) { //@todo change default to false when ready to roll out
      $contentAreas = $this->filterContentAreasByRequirements($contentAreas, $pageObject->provides);
    }
    $this->view->contentAreas = $contentAreas;

    //RE-INDEX BY NAME
    $contentByName = array();
    foreach ($contentAreas as $category => $categoryAreas) {
      foreach ($categoryAreas as $info) {
        $contentByName[$info['name']] = $info;
      }
    }
    $this->view->contentByName = $contentByName;

    //GET REGISTERED CONTENT AREAS
    $contentRowset = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $pageObject->page_id)->order('order ASC'));
    $contentStructure = $this->prepareContentArea($contentRowset);

    //VALIDATE STRUCTURE
    //NOTE: DO NOT VALIDATE FOR HEADER OR FOOTER
    $error = false;
    if (substr($pageObject->name, 0, 6) !== 'header' && substr($pageObject->name, 0, 6) !== 'footer') {
      foreach ($contentStructure as &$info1) {
        if (!in_array($info1['name'], array('top', 'bottom', 'main')) || $info1['type'] != 'container') {
          $error = true;
          break;
        }
        foreach ($info1['elements'] as &$info2) {
          if (!in_array($info2['name'], array('left', 'middle', 'right')) || $info1['type'] != 'container') {
            $error = true;
            break;
          }
        }
        //RE ORDER SECOND-LEVEL ELEMENTS
        usort($info1['elements'], array($this, '_reorderContentStructure'));
      }
    }

    if ($error) {
      throw new Exception('page failed validation check');
    }

    //ASSIGN STRUCTURE
    $this->view->contentRowset = $contentRowset;
    $this->view->contentStructure = $contentStructure;

    $rows = Engine_Api::_()->getDbtable('hideprofilewidgets', 'sitepage')->hideWidgets();
    $hideWidgets = array();
    foreach ($rows as $value)
      $hideWidgets[] = $value->widgetname;
    $this->view->hideWidgets = $hideWidgets;

    $session = new Zend_Session_Namespace();
    if (isset($session->structure))
      unset($session->structure);
    if (isset($session->page_object_id))
      unset($session->page_object_id);

		$isSupport = null;
		$coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
		/*
			return < 0 : when running version is lessthen 4.2.1
			return 0 : If running version is equal to 4.2.1
			return > 0 : when running version is greaterthen 4.2.1
		*/
		if( !empty($coreVersion) ) {
			$coreVersion = $coreVersion->version;
			if( !empty(Engine_Api::_()->seaocore()->checkVersion($coreVersion, '4.2.1')) ) {
				$isSupport = 1;
			}
		}
    if (!empty($isSupport)) {
      $this->renderScript('admin-mobile-layout/layout.tpl');
    } else {
      $this->renderScript('admin-mobile-layout/layout_default.tpl');
    }
  }

	public function updateAction() {
    //GET NAVIGATION   
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
    $contentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitepage');
    $userlayoutcontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
    $userlayoutcontentTablename = $userlayoutcontentTable->info('name');
    set_time_limit(0);
    $userlayoutcontentpagesTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
    $totalPages = $userlayoutcontentpagesTable->select()->from($userlayoutcontentpagesTable->info('name'), array('count(*) as count'))->query()->fetchColumn();
    $limit = 150;
    $page_reload = $this->_getParam('page_reload', 1);
    $offset = ($page_reload - 1) * $limit;

    $select = $userlayoutcontentTable->select()->from($userlayoutcontentTablename, array('mobilecontentpage_id'))->limit($limit, $offset)->group('mobilecontentpage_id');

    $content_pages_id = $userlayoutcontentTable->fetchAll($select);
    $session = new Zend_Session_Namespace();

    $db = $pageTable->getAdapter();
    $db->beginTransaction();
		//CLOSE THE SMOOTHBOX
		$reload_count = round($totalPages / $limit);
    try {
      //GET PAGE
      $page = $this->_getParam('page');

      $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));
      if (null === $pageObject) {
        throw new Engine_Exception('Page is missing');
      }

      //UPDATE LAYOUT
      if (null !== ($newLayout = $this->_getParam('admin_sitepage_layout'))) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.show.hide.header.footer', $newLayout);
      }

      //GET REGISTERED CONTENT AREAS
      if (isset($session->page_object_id)) {
        $page_object_id = $session->page_object_id;
        $contentRowset = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $page_object_id)->where('default_admin_layout = ?', 0));
      } else {
        $session->page_object_id = $pageObject->page_id;
        $contentRowset = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $pageObject->page_id));
      }
      //GET STRUCTURE
      if (isset($session->structure)) {
        $structure = $session->structure;
      } else {
        $structure = $this->_getParam('structure');
        $trimStructure = trim($structure, '()');
         //session gets destroyed due to large structure, and site gets logged out after save changes
        // $session->structure = !empty($trimStructure) ? Zend_Json::decode($trimStructure) : array();

        if (is_string($structure)) {
          $structure = !empty($trimStructure) ? Zend_Json::decode($trimStructure) : array();
        }

        if (!is_array($structure)) {
          throw new Engine_Exception('Structure is not an array or valid json structure');
        }
      }

      //DIFF
      $orderIndex = 1;
      $newRowsByTmpId = array();
      $existingRowsByContentId = array();

      foreach ($structure as $element) {
        //GET INFO
        $content_id = @$element['identity'];
        $tmp_content_id = @$element['tmp_identity'];
        $parent_id = @$element['parent_identity'];
        $tmp_parent_id = @$element['parent_tmp_identity'];

        $newOrder = $orderIndex++;

        //SANITY
        if (empty($content_id) && empty($tmp_content_id)) {
          throw new Exception('content id and tmp content id both empty');
        }

        //GET EXISTING CONTENT ROW (IF ANY)
        $contentRow = null;
        if (!empty($content_id)) {
          $contentRow = $contentRowset->getRowMatching('mobileadmincontent_id', $content_id);
          if (null === $contentRow) {
            throw new Exception('content row missing');
          }
        }

        //GET EXISTING PARENT ROW (IF ANY)
        $parentContentRow = null;
        if (!empty($parent_id)) {
          $parentContentRow = $contentRowset->getRowMatching('mobileadmincontent_id', $parent_id);
        } else if (!empty($tmp_parent_id)) {
          $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
        }

        //EXISTING ROW
        if (!empty($contentRow) && is_object($contentRow)) {
          $existingRowsByContentId[$content_id] = $contentRow;

          //UPDATE ROW
          if (!empty($parentContentRow)) {
            $contentRow->parent_content_id = $parentContentRow->mobileadmincontent_id;
          }
          if (empty($contentRow->parent_content_id)) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }

          //SET PARAMS
          $oldContentRow = null;
          $oldContentRowParams = json_encode($contentRow->params);
          if (isset($element['params']) && is_array($element['params'])) {
            $contentRow->params = $element['params'];
          }

          if ($contentRow->type == 'container') {
            $newOrder = array_search($contentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow->order = $newOrder;
          $contentRow->save();

          foreach ($content_pages_id as $value) {

            $content_page_id = $value->mobilecontentpage_id;

            if ($contentRow->type == 'container' && $contentRow->name == 'middle') {
              $select_parent = $userlayoutcontentTable->select()->where('type =?', 'container')->where('name =?', $parentContentRow->name)->where('mobilecontentpage_id =?', $content_page_id);
              $content_parent = $userlayoutcontentTable->fetchRow($select_parent);
              $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('mobilecontentpage_id =?', $content_page_id)->where('parent_content_id = ?', $content_parent->mobilecontent_id);
            } else {
              $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('mobilecontentpage_id =?', $content_page_id);
            }
            $usercontentRow = $userlayoutcontentTable->fetchRow($select);
            if (empty($usercontentRow))
              continue;

            if ($usercontentRow->type == 'container') {
              $newOrder = array_search($usercontentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
              $userlayoutcontentTable->update(array('order' => $newOrder), array('type = ?' => $contentRow->type, 'mobilecontentpage_id = ?' => $content_page_id, 'name = ?' => $contentRow->name, 'widget_admin = ?' => 1));
            }

            if (isset($session->edittitles) && in_array($element['name'], $session->edittitles)) {
							$userlayoutcontentTable->update(array('params' => $element['params']), array('type = ?' => $contentRow->type, 'mobilecontentpage_id = ?' => $content_page_id, 'name = ?' => $contentRow->name, 'widget_admin = ?' => 1, 'parent_content_id = ?' => $usercontentRow->parent_content_id, 'params =?' => $oldContentRowParams));
            }

            $parent_id = 0;
            if (!empty($parentContentRow)) {
              $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('mobilecontentpage_id =?', $content_page_id);
              $content_pages = $userlayoutcontentTable->fetchRow($select);
              if (!empty($parentContentRow->parent_content_id)) {
                $parentparentContentRow = $contentRowset->getRowMatching('mobileadmincontent_id', $parentContentRow->parent_content_id);
                if (!empty($parentparentContentRow)) {
                  $select = $userlayoutcontentTable->select()->where('type =?', $parentparentContentRow->type)->where('name =?', $parentparentContentRow->name)->where('mobilecontentpage_id =?', $content_page_id);
                  $contentParent_pages = $userlayoutcontentTable->fetchRow($select);
                  if ($contentParent_pages->name != $parentparentContentRow->name) {
                    $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('mobilecontentpage_id =?', $content_page_id)->where('parent_content_id <> ?', $content_pages->parent_content_id);
                    $content_pages_2 = $userlayoutcontentTable->fetchRow($select);
                    if (!empty($content_pages_2))
                      $content_pages = $content_pages_2;
                  }
                }
              }
              $parent_id = $content_pages->mobilecontent_id;
            } else {
              $userlayoutcontentTable->update(array('parent_content_id' => new Zend_Db_Expr('NULL'), 'order' => $newOrder), array('type = ?' => $contentRow->type, 'mobilecontentpage_id = ?' => $content_page_id, 'name = ?' => $contentRow->name, 'widget_admin = ?' => 1));
            }

            if (!empty($parent_id)) {
              //if you place any widget two times please mantion in this condition
              if ($usercontentRow->name != 'core.html-block' && $usercontentRow->name != 'core.ad-campaign' && $usercontentRow->name != 'sitepageintegration.profile-items' && $usercontentRow->name != 'sitepagemember.profile-sitepagemembers') {
                $userlayoutcontentTable->update(array('parent_content_id' => $parent_id, 'order' => $newOrder), array('mobilecontent_id = ?' => $usercontentRow->mobilecontent_id, 'widget_admin = ?' => 1));
              }
              if (empty($usercontentRow->parent_content_id)) {
                $userlayoutcontentTable->update(array('parent_content_id' => new Zend_Db_Expr('NULL'), 'order' => $newOrder), array('mobilecontent_id = ?' => $usercontentRow->mobilecontent_id, 'widget_admin = ?' => 1));
              }
            }
          }

          //SET PARENT CONTENT
          if (!empty($parentContentRow)) {
            $contentRow->parent_content_id = $parentContentRow->mobileadmincontent_id;
          }
          if (empty($contentRow->parent_content_id)) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }
          $contentRow->save();
        }

        //NEW ROW
        else {
          if ($page_reload == 1) {
            if (empty($element['type']) || empty($element['name'])) {
              throw new Exception('missing name and/or type info');
            }

            if ($element['type'] == 'container') {
              $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
            }

            $contentRow = $contentTable->createRow();
            $contentRow->page_id = $pageObject->page_id;
            $contentRow->order = $newOrder;
            $contentRow->type = $element['type'];
            $contentRow->name = $element['name'];
            $contentRow->default_admin_layout = 1;

            //SET PARENT CONTENT
            if (!empty($parentContentRow)) {
              $contentRow->parent_content_id = $parentContentRow->mobileadmincontent_id;
            }
            if (empty($contentRow->parent_content_id)) {
              $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
            }
            $contentRow->save();
          }
          foreach ($content_pages_id as $value) {
            $content_page_id = $value->mobilecontentpage_id;
            $userlayoutpages = null;
            if ($element['type'] == 'container') {
              $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
            } else {
              if ($element['name'] != 'core.html-block' && $element['name'] != 'core.ad-campaign' && $element['name'] != 'sitepageintegration.profile-items' && $element['name'] != 'sitepagemember.profile-sitepagemembers') {
                $select = $userlayoutcontentTable->select()->where('name =?', $element['name'])->where('mobilecontentpage_id =?', $content_page_id);
                $userlayoutpages = $userlayoutcontentTable->fetchRow($select);
              } else {
                $select = $userlayoutcontentTable->select()->where('name =?', $element['name'])->where('mobilecontentpage_id =?', $content_page_id)->where('params =?', json_encode($element['params']));
                $userlayoutpages = $userlayoutcontentTable->fetchRow($select);
              }
            }

            if (empty($userlayoutpages) && isset($element['type']) && isset($element['name'])) {
              $usercontentRow = $userlayoutcontentTable->createRow();
              $usercontentRow->mobilecontentpage_id = $content_page_id;
              $usercontentRow->order = $newOrder;
              $usercontentRow->type = $element['type'];
              $usercontentRow->name = $element['name'];
              if (!empty($parentContentRow)) {
                $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('mobilecontentpage_id =?', $content_page_id)->order('mobilecontent_id DESC')->order('mobilecontent_id DESC');
                $content_pages = $userlayoutcontentTable->fetchRow($select);
                if (!empty($content_pages)) {
                  $usercontentRow->parent_content_id = $content_pages->mobilecontent_id;
                }
                if (empty($usercontentRow->parent_content_id)) {
                  $usercontentRow->parent_content_id = new Zend_Db_Expr('NULL');
                }
              }

              //SET PARAMS
              if (isset($element['params']) && is_array($element['params'])) {
                $usercontentRow->params = $element['params'];
              }
              $usercontentRow->save();
            }
          }

          if ($page_reload == 1) {
            //SET PARAMS
            if (isset($element['params']) && is_array($element['params'])) {
              $contentRow->params = json_encode($element['params']);
            }
            $contentRow->save();
            $newRowsByTmpId[$tmp_content_id] = $contentRow;
          }
        }
      }

      //DELETE ROWS THAT WERE NOT PRESENT IN DATA SENT BACK
      $deletedRowIds = array();
      foreach ($contentRowset as $contentRow) {
        if (empty($existingRowsByContentId[$contentRow->mobileadmincontent_id])) {
          $parentContentRow = "";
          if (!empty($contentRow->parent_content_id))
            $parentContentRow = $contentRowset->getRowMatching('mobileadmincontent_id', $contentRow->parent_content_id);
          $oldContentRow = json_encode($contentRow->params);
          foreach ($content_pages_id as $value) {
            $parent_id = 0;
            $content_page_id = $value->mobilecontentpage_id;
            if (!empty($parentContentRow)) {
              $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('mobilecontentpage_id =?', $content_page_id);
              $content_pages = $userlayoutcontentTable->fetchRow($select);
              $parentparentContentRow = $contentRowset->getRowMatching('mobileadmincontent_id', $parentContentRow->mobileadmincontent_id);
              if (!empty($parentparentContentRow)) {
                $select = $userlayoutcontentTable->select()->where('type =?', $parentparentContentRow->type)->where('name =?', $parentparentContentRow->name)->where('mobilecontentpage_id =?', $content_page_id);
                $contentParent_pages = $userlayoutcontentTable->fetchRow($select);
                if ($contentParent_pages->name != $parentparentContentRow->name) {
                  $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('mobilecontentpage_id =?', $content_page_id)->where('parent_content_id <> ?', $contentParent_pages->mobilecontent_id);
                  $content_pages = $userlayoutcontentTable->fetchRow($select);
                }
              }
              $parent_id = $content_pages->mobilecontent_id;
            }
            if (!empty($parent_id)) {
              if ( $contentRow->name != 'core.ad-campaign' && $contentRow->name != 'sitepageintegration.profile-items') {
                $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('mobilecontentpage_id =?', $content_page_id)->where('parent_content_id =?', $parent_id);
              } else {
                $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('mobilecontentpage_id =?', $content_page_id)->where('parent_content_id =?', $parent_id)->where('params =?', $oldContentRow);
              }
              $content_pages = $userlayoutcontentTable->fetchRow($select);
              if (!empty($content_pages)) {
                $userlayoutcontentTable->delete(array(
                    'name =?' => $contentRow->name, 'mobilecontentpage_id =?' => $content_page_id, 'parent_content_id =?' => $parent_id, 'mobilecontent_id =?' => $content_pages->mobilecontent_id, 'widget_admin = ?' => 1
                ));
              }
            } else {
              if (($contentRow->name == 'top' || $contentRow->name == 'bottom') && $contentRow->type == 'container') {
                $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('mobilecontentpage_id =?', $content_page_id);
                $content_pages = $userlayoutcontentTable->fetchRow($select);
                $userlayoutcontentTable->delete(array('type =?' => 'container',
                    'name =?' => 'middle', 'mobilecontentpage_id =?' => $content_page_id, 'parent_content_id = ?' => $content_pages->mobilecontent_id, 'widget_admin = ?' => 1
                ));
                $userlayoutcontentTable->delete(array(
                    'name =?' => $contentRow->name, 'mobilecontentpage_id =?' => $content_page_id, 'mobilecontent_id =?' => $content_pages->mobilecontent_id, 'widget_admin = ?' => 1
                ));
              }
            }
          }
          if ($page_reload >= $reload_count) {
            $contentRow->delete();
          }
        }
      }
      $this->view->deleted = $deletedRowIds;
      $select = $contentTable->select()->where('page_id =?', $page)->where('name =?', 'top');
      $top = $contentTable->fetchRow($select);
      if (!empty($top)) {
        $select = $contentTable->select()->where('page_id =?', $page)->where('name =?', 'middle')->where('parent_content_id =?', $top->mobileadmincontent_id);
        $middle = $contentTable->fetchRow($select);
        $select = $contentTable->select()->where('page_id =?', $page)->where('parent_content_id =?', $middle->mobileadmincontent_id);
        $middle_block = $contentTable->fetchAll($select);
        $name = array();
        foreach ($middle_block as $block) {
          $name[] = $block->name;
        }
        foreach ($content_pages_id as $value) {
          $content_page_id = $value->mobilecontentpage_id;
          $select = $userlayoutcontentTable->select()->where('name =?', 'top')->where('mobilecontentpage_id =?', $content_page_id);
          $user_top = $userlayoutcontentTable->fetchRow($select);
          if (!empty($user_top)) {
            $select = $userlayoutcontentTable->select()->where('name =?', 'middle')->where('mobilecontentpage_id =?', $content_page_id)->where('parent_content_id =?', $user_top->mobilecontent_id);
            $user_middile = $userlayoutcontentTable->fetchRow($select);
            if ($user_middile) {
              foreach ($name as $name_value) {
                $userlayoutcontentTable->update(array('parent_content_id' => $user_middile->mobilecontent_id), array('mobilecontentpage_id = ?' => $content_page_id, 'name = ?' => $name_value, 'widget_admin = ?' => 1));
              }
            }
          }
        }
      } else {
        $select = $contentTable->select()->where('page_id =?', $page)->where('name =?', 'bottom');
        $bottom = $contentTable->fetchRow($select);
        if (!empty($bottom)) {
          $select = $contentTable->select()->where('page_id =?', $page)->where('name =?', 'middle')->where('parent_content_id =?', $bottom->mobileadmincontent_id);
          $middle = $contentTable->fetchRow($select);
          $select = $contentTable->select()->where('page_id =?', $page)->where('parent_content_id =?', $middle->mobileadmincontent_id);
          $middle_block = $contentTable->fetchAll($select);
          $name = array();
          foreach ($middle_block as $block) {
            $name[] = $block->name;
          }

          foreach ($content_pages_id as $value) {
            $content_page_id = $value->mobilecontentpage_id;
            $select = $userlayoutcontentTable->select()->where('name =?', 'bottom')->where('mobilecontentpage_id =?', $content_page_id);
            $user_bottom = $userlayoutcontentTable->fetchRow($select);
            if (!empty($user_bottom)) {
              $select = $userlayoutcontentTable->select()->where('name =?', 'middle')->where('mobilecontentpage_id =?', $content_page_id)->where('parent_content_id =?', $user_bottom->mobilecontent_id);
              $user_middile = $userlayoutcontentTable->fetchRow($select);
              if ($user_middile) {
                foreach ($name as $name_value) {
                  $userlayoutcontentTable->update(array('parent_content_id' => $user_middile->mobilecontent_id), array('mobilecontentpage_id = ?' => $content_page_id, 'name = ?' => $name_value, 'widget_admin = ?' => 1));
                }
              }
            }
          }
        }
      }

      $edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layout.setting', 0);
      if (empty($edit_layout_setting)) {
        $select = $contentTable->select()->where('name =?', 'sitepage.widgetlinks-sitepage');
        $user_widgetlink = $contentTable->fetchRow($select);
        if (empty($user_widgetlink)) {
          foreach ($content_pages_id as $value) {
            $content_page_id = $value->mobilecontentpage_id;
            $userlayoutcontentTable->delete(array(
                'name =?' => 'sitepage.widgetlinks-sitepage', 'mobilecontentpage_id =?' => $content_page_id));
          }
        }
      }

      //SEND BACK NEW CONTENT INFO
      $newData = array();
      foreach ($newRowsByTmpId as $tmp_id => $newRow) {
        $newData[$tmp_id] = $this->createElementParams($newRow);
      }
      $this->view->newIds = $newData;

      $this->view->status = true;
      $this->view->error = false;
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }

    if ($page_reload <= $reload_count) {
      $page_reload++;

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => false,
          'redirect' => $this->view->url(array('module' => 'sitepage', 'controller' => 'mobile-layout', 'action' => 'update', 'page' => $page, 'page_reload' => $page_reload, 'admin_sitepage_layout' => $newLayout), 'admin_default', true),
          // 'parentRedirectTime' => '10',
          //  format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('<div class="tip" style="margin:10px auto;width:750px;"><span>Please do not close this page or navigate to another page till you see a layout changes completion or error message.</span></div><div>
					<center><img src="application/modules/Sitepage/externals/images/layout/uploading.gif" alt="" /></center>
      	</div>'))
      ));
    } else {

      $contentTable->update(array('default_admin_layout' => 0), array('default_admin_layout = ?' => 1));
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => false,
          'redirect' => $this->view->url(array('module' => 'sitepage', 'controller' => 'mobile-layout', 'action' => 'layout', 'page' => $page), 'admin_default', true),
          'parentRedirectTime' => '10',
          //'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('<ul class="form-notices" style="margin:10px auto;width:500px;float:none;"><li style="float:none;">Your changes have been saved successfully.</li></ul>'))
      ));
    }

    if (isset($session->edittitles))
      unset($session->edittitles);
  }

  public function widgetAction() {

    //RENDER BY WIDGET NAME
    $name = $this->_getParam('name');
    $mod = $this->_getParam('mod');
    if (null === $name) {
      throw new Exception('no widget found with name: ' . $name);
    }
    if (null !== $mod) {
      $name = $mod . '.' . $name;
    }

    $contentInfoRaw = $this->getContentAreas();
    $contentInfo = array();
    foreach ($contentInfoRaw as $info) {
      $contentInfo[$info['name']] = $info;
    }

    //IT HAS A FORM SPECIFIED IN CONTENT MANIFEST
    if (!empty($contentInfo[$name]['adminForm'])) {
      if (is_string($contentInfo[$name]['adminForm'])) {
        $formClass = $contentInfo[$name]['adminForm'];
        Engine_Loader::loadClass($formClass);
        $this->view->form = $form = new $formClass();
      } else if (is_array($contentInfo[$name]['adminForm'])) {
        $this->view->form = $form = new Engine_Form($contentInfo[$name]['adminForm']);
      } else {
        throw new Core_Model_Exception('Unable to load admin form class');
      }

      //TRY TO SET TITLE IF MISSING
      if (!$form->getTitle()) {
        $form->setTitle('Editing: ' . $contentInfo[$name]['title']);
      }

      //TRY TO SET DESCRIPTION IF MISSING
      if (!$form->getDescription()) {
        $form->setDescription('placeholder');
      }

      $form->setAttrib('class', 'global_form_popup ' . $form->getAttrib('class'));

      //ADD TITLE ELEMENT
      if (!$form->getElement('title')) {
        $form->addElement('Text', 'title', array(
            'label' => 'Title',
            'order' => -100,
        ));
      }

      if (!empty($contentInfo[$name]['isPaginated']) && !$form->getElement('itemCountPerPage')) {
        $form->addElement('Text', 'itemCountPerPage', array(
            'label' => 'Count',
            'description' => '(number of items to show)',
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
            'order' => 1000000 - 1,
        ));
      }

      //ADD SUBMIT BUTTON
      if (!$form->getElement('submit') && !$form->getElement('execute')) {
        $form->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
            'order' => 1000000,
        ));
      }

      //ADD NAME
      $form->addElement('Hidden', 'name', array(
          'value' => $name,
          'order' => 1000010,
      ));

      if (!$form->getElement('cancel')) {
        $form->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
            'order' => 1000001,
        ));
      }

      if (!$form->getDisplayGroup('buttons')) {
        $submitName = ( $form->getElement('execute') ? 'execute' : 'submit' );
        $form->addDisplayGroup(array(
            $submitName,
            'cancel',
                ), 'buttons', array(
            'order' => 1000002,
        ));
      }

      //FORCE METHOD AND ACTION
      $form->setMethod('post')
              ->setAction($_SERVER['REQUEST_URI']);

      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

			$session = new Zend_Session_Namespace();
      if (isset($session->edittitles))
        unset($session->edittitles);
      $session->edittitles[] = $name;
        $this->view->values = $form->getValues();
        $this->view->form = null;

      }

      return;
    }

    //TRY TO RENDER ADMIN PAGE
    if (!empty($contentInfo[$name])) {
      try {
        $structure = array(
            'type' => 'widget',
            'name' => $name,
            'request' => $this->getRequest(),
            'action' => 'admin',
            'throwExceptions' => true,
        );

        //CREATE ELEMENT (WITH STRUCTURE)
        $element = new Engine_Content_Element_Container(array(
                    'elements' => array($structure),
                    'decorators' => array(
                        'Children'
                    )
                ));

        $content = $element->render();
        $this->getResponse()->setBody($content);
        $this->_helper->viewRenderer->setNoRender(true);
        return;
      } catch (Exception $e) {
        
      }
    }

    //JUST RENDER DEFAULT EDITING FORM
    $this->view->form = $form = new Engine_Form(array(
                'title' => $contentInfo[$name]['title'],
                'description' => 'placeholder',
                'method' => 'post',
                'action' => $_SERVER['REQUEST_URI'],
                'class' => 'global_form_popup',
                'elements' => array(
                    array(
                        'Text',
                        'title',
                        array(
                            'label' => 'Title',
                        )
                    ),
                    array(
                        'Button',
                        'submit',
                        array(
                            'label' => 'Save',
                            'type' => 'submit',
                            'decorators' => array('ViewHelper'),
                            'ignore' => true,
                            'order' => 1501,
                        )
                    ),
                    array(
                        'Hidden',
                        'name',
                        array(
                            'value' => $name,
                        )
                    ),
                    array(
                        'Cancel',
                        'cancel',
                        array(
                            'label' => 'cancel',
                            'link' => true,
                            'prependText' => ' or ',
                            'onclick' => 'parent.Smoothbox.close();',
                            'ignore' => true,
                            'decorators' => array('ViewHelper'),
                            'order' => 1502,
                        )
                    )
                ),
                'displaygroups' => array(
                    'buttons' => array(
                        'name' => 'buttons',
                        'elements' => array(
                            'submit',
                            'cancel',
                        ),
                        'options' => array(
                            'order' => 1500,
                        )
                    )
                )
            ));

    if (!empty($contentInfo[$name]['isPaginated'])) {
      $form->addElement('Text', 'itemCountPerPage', array(
          'label' => 'Count',
          'description' => '(number of items to show)',
          'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
          )
      ));
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $session = new Zend_Session_Namespace();
      if (isset($session->edittitles))
        unset($session->edittitles);
      $session->edittitles[] = $name;
      $this->view->values = $form->getValues();
      $this->view->form = null;
    } else {
      $form->populate($this->_getAllParams());
    }
  }

  public function prepareContentArea($content, $current = null) {

    //GET PARENT CONTENT ID
    $parent_content_id = null;
    if (null !== $current) {
      $parent_content_id = $current->mobileadmincontent_id;
    }

    //GET CHILDREN
    $children = $content->getRowsMatching('parent_content_id', $parent_content_id);
    if (empty($children) && null === $parent_content_id) {
      $children = $content->getRowsMatching('parent_content_id', 0);
    }

    //GET STRUCT
    $struct = array();
    foreach ($children as $child) {
      $elStruct = $this->createElementParams($child);
      $elStruct['elements'] = $this->prepareContentArea($content, $child);
      $struct[] = $elStruct;
    }

    return $struct;
  }

  public function createElementParams($row) {

    $data = array(
        'identity' => $row->mobileadmincontent_id,
        'type' => $row->type,
        'name' => $row->name,
        'order' => $row->order,
    );
    $params = (array) $row->params;
    if (isset($params['title']))
      $data['title'] = $params['title'];
    $data['params'] = $params;
    return $data;
  }

  public function showHideWidgetAction() {

    //GET WIDGET NAME
    $widgetname = $this->_getParam('widgetname', null);

    //GET OPTION
    $option = $this->_getParam('option', null);
    $hideprofilewidget_table = Engine_Api::_()->getDbTable('hideprofilewidgets', 'sitepage');
    $row = $hideprofilewidget_table->fetchRow($hideprofilewidget_table->select()->where('widgetname =?', $widgetname));
    if ($option == 1) {
      $contentpagestable = Engine_Api::_()->getDbTable('mobileContentpages', 'sitepage');
      $contentpagesrow = $contentpagestable->fetchAll($contentpagestable->select());
      $admincontenttable = Engine_Api::_()->getDbTable('admincontent', 'sitepage');
      $admincontentrow = $admincontenttable->fetchRow($admincontenttable->select()->where('name =?', $widgetname));
      if (empty($admincontentrow)) {
        foreach ($contentpagesrow as $value) {
          Engine_Api::_()->getDbTable('mobileContent', 'sitepage')->delete(array('name =?' => $widgetname));
        }
      }
      if (empty($row)) {
        $hideprofilewidget_table->insert(array('widgetname' => $widgetname));
      }
    } else {
      if (!empty($row)) {
        $hideprofilewidget_table->delete(array('widgetname =?' => $widgetname));
      }
    }
  }
}

?>