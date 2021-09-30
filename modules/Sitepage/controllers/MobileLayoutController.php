<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LayoutController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_MobileLayoutController extends Core_Controller_Action_Standard {

  //SET THE VALUE FOR ALL ACTION DEFAULT
  public function init() {

    //SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMEPAGES
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) { 
      Engine_API::_()->sitemobile()->setupRequestError();
		}

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();

    $id = $this->_getParam('page_id', $this->_getParam('id', null));
    if ($id) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $id);
      if ($sitepage) {
        Engine_Api::_()->core()->setSubject($sitepage);
        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if (empty($isManageAdmin)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
      }
    }
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

  public function setUserDrivenLayoutAction() {
    $this->view->page_id = $this->_getParam('page_id', null);
		$this->_helper->layout->setLayout('default-simple');
  }

  public function saveUserDrivenLayoutAction() {
    $page_id = $this->_getParam('page_id', null);
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW PAGE.
		$pageAdminTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
		$pageAdminTableName = $pageAdminTable->info('name');
		$selectPageAdmin = $pageAdminTable->select()
						->setIntegrityCheck(false)
						->from($pageAdminTableName)
						->where('name = ?', 'sitepage_index_view');
		$pageAdminresult = $pageAdminTable->fetchRow($selectPageAdmin);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
		//NOW INSERTING THE ROW IN PAGE TABLE
		$pageTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
     
		//CREATE NEW PAGE
		$pageObject = $pageTable->createRow();
		$pageObject->displayname = $sitepage->title;
		$pageObject->title = $sitepage->title;
		$pageObject->description = $sitepage->body;
		$pageObject->name = "sitepage_index_view";
		$pageObject->url = $pageAdminresult->url;
		$pageObject->custom = $pageAdminresult->custom;
		$pageObject->fragment = $pageAdminresult->fragment;
		$pageObject->keywords = $pageAdminresult->keywords;
		$pageObject->layout = $pageAdminresult->layout;
		$pageObject->view_count = $pageAdminresult->view_count;
		$pageObject->user_id = $viewer_id;
		$pageObject->page_id = $page_id;
		$contentPageId = $pageObject->save();

		//NOW FETCHING PAGE CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS PAGE.
		//NOW INSERTING DEFAULT PAGE CONTENT SETTINGS IN OUR CONTENT TABLE
		$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
		$sitepage_layout_cover_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layout.cover.photo', 1);
		if (!$layout) {
			Engine_Api::_()->getDbtable('mobileContent', 'sitepage')->setContentDefault($contentPageId, $sitepage_layout_cover_photo);
		} else {
			Engine_Api::_()->getApi('mobilelayoutcore', 'sitepage')->setContentDefaultLayout($contentPageId, $sitepage_layout_cover_photo);
		}

		$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefreshTime' => '60',
				'parentRefresh' => 'true',
				'format' => 'smoothbox',
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Layout for this page has been changed successfully.'))
		));

	}

  public function layoutAction() {

//     $cont = Engine_Content::getInstance();
//     $storage = Engine_Api::_()->getDbtable('pages', 'sitemobile');
//     $cont->setStorage($storage);

    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return false;
    }

		$edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    if (empty($edit_layout_setting)) {
      $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');  
		
    //FINDING THE LAYOUT ID OF THIS PAGE
    $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
    $contentTableName = $contentTable->info('name');
    $contentPageTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
    $contentPageTableName = $contentPageTable->info('name');
    $mobilecontentpage_id = $contentPageTable->select()
            ->from($contentPageTableName, array('mobilecontentpage_id'))
            ->where('page_id =?', $sitepage->page_id)
            ->query()
            ->fetchColumn();
    //GET PAGE PARAM
    $page = $mobilecontentpage_id;
    $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
    $this->view->adminDriven = 0;
    if(empty($page)) {
      $corepageinfo = Engine_Api::_()->sitepage()->getMobileWidgetizedPage();
      $corePagesTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
      $corePagesTableName = $corePagesTable->info('name');
      $contentCoreTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
      $contentCoreTableName = $contentCoreTable->info('name');
			$adminContentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitepage');
			$adminContentTableName = $adminContentTable->info('name');
			$contentpage_id = $adminContentTable->select()
							->from($adminContentTableName, array('page_id'))
							->where('page_id =?', $corepageinfo->page_id)
							->query()
							->fetchColumn();
      //GET CURRENT PAGE
			$this->view->pageObject = $pageObject = $corePagesTable->fetchRow($corePagesTable->select()->where('name = ?', 'sitepage_index_view')->orWhere('page_id = ?', $mobilecontentpage_id));
			if (null === $pageObject) {
				$page = 'core_index_index';
				$pageObject = $corePagesTable->fetchRow($corePagesTable->select()->where('name = ?', 'core_index_index'));
			}

			//GET REGISTERED CONTENT AREAS
			if (!empty($pageObject)) {
				$contentRowset = $adminContentTable->fetchAll($adminContentTable->select()->where('page_id = ?', $pageObject->page_id)->order('order ASC'));
				$contentStructure = $adminContentTable->prepareContentArea($contentRowset);
			}
      $this->view->adminDriven = 1;
    } else {
			//GET CURRENT PAGE
			$this->view->pageObject = $pageObject = $contentPageTable->fetchRow($contentPageTable->select()->where('mobilecontentpage_id = ?', $page));
			//GET REGISTERED CONTENT AREAS
			if (!empty($pageObject)) {
				$contentRowset = $contentTable->fetchAll($contentTable->select()->where('mobilecontentpage_id = ?', $pageObject->mobilecontentpage_id)->order('order ASC'));
				$contentStructure = $contentPageTable->prepareContentArea($contentRowset);

			}
    }
		$this->view->page = $page;
		$this->view->pageObject = $pageObject;

    //GET AVAILABLE CONTENT BLOCKS
    $this->view->contentAreas = $contentAreas = $this->buildCategorizedContentAreas($this->getContentAreas());
  
    $rows = Engine_Api::_()->getDbtable('hideprofilewidgets', 'sitepage')->hideWidgets();
    $hideWidgets = array();
    foreach ($rows as $value)
      $hideWidgets[] = $value->widgetname;
    $this->view->hideWidgets = $hideWidgets;
    $contentByName = array();
    foreach ($contentAreas as $category => $categoryAreas) {
      foreach ($categoryAreas as $info) {
        $contentByName[$info['name']] = $info;
      }
    }
    $this->view->contentByName = $contentByName;

    //MAKE PAGE FORM
    $this->view->pageForm = $pageForm = new Sitepage_Form_Layout_Content_MobilePage();
    if (!empty($pageObject)) {
      $pageForm->populate($pageObject->toArray());
    } else {
      //return;
    }

    //VALIDATE STRUCTURE
    //NOTE: DO NOT VALIDATE FOR HEADER OR FOOTER
    $error = false;
    if ($pageObject->name !== 'header' && $pageObject->name !== 'footer') {
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
      $error_msg = Zend_Registry::get('Zend_Translate')->_('page failed validation check');
      throw new Exception($error_msg);
    }

    $this->view->showeditinwidget = array('seaocore.feed', 'activity.feed', 'sitepage.info-sitepage', 'sitepage.overview-sitepage', 'sitepage.location-sitepage', 'core.profile-links', 'sitepage.discussion-sitepage', 'sitepagepoll.profile-sitepagepolls', 'sitepageevent.profile-sitepageevents', 'sitepageoffer.profile-sitepageoffers', 'sitepagedocument.profile-sitepagedocuments', 'sitepageform.sitepage-viewform', 'sitepagereview.profile-sitepagereviews', 'sitepagenote.profile-sitepagenotes', 'sitepagevideo.profile-sitepagevideos', 'sitepage.photos-sitepage', 'sitepagemusic.profile-sitepagemusic', 'sitepageintegration.profile-items', 'sitepagetwitter.feeds-sitepagetwitter', 'advancedactivity.home-feeds', 'sitepagemember.profile-sitepagemembers', 'siteevent-contenttype-events', 'document-contenttype-documents');

    //ASSIGN STRUCTURE
    $this->view->contentRowset = $contentRowset;
    $this->view->contentStructure = $contentStructure;

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
      $this->renderScript('mobile-layout/layout.tpl');
    } else {
      $this->renderScript('mobile-layout/layout_default.tpl');
    }
  }

  public function getContentAreas() {

    $contentAreas = array();
    // From modules
    $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();
    foreach ($modules as $module => $path) {
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

    foreach ($modules as $module => $path) {
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
    return $contentAreas;
  }

  public function updateAction() {

    $pageTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
    $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
    $db = $pageTable->getAdapter();
    $db->beginTransaction();

    try {
      //GET PAGE
      $page = $this->_getParam('page');
      $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('mobilecontentpage_id = ?', $page));
      if (null === $pageObject) {
        $message1 = Zend_Registry::get('Zend_Translate')->_('Page is missing');
        throw new Engine_Exception($message1);
      }

      //UPDATE LAYOUT
      if (null !== ($newLayout = $this->_getParam('layout'))) {
        $pageObject->layout = $newLayout;
        $pageObject->save();
      }

      //GET REGISTERED CONTENT AREAS
      $contentRowset = $contentTable->fetchAll($contentTable->select()->where('mobilecontentpage_id = ?', $pageObject->mobilecontentpage_id));

      //GET STRUCTURE
      $trimStructure = $this->_getParam('structure');
      $structure = !empty($trimStructure) ? Zend_Json::decode($trimStructure) : array();

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
          $message2 = Zend_Registry::get('Zend_Translate')->_('content id and tmp content id both empty');
          throw new Exception($message2);
        }

        //GET EXISTING CONTENT ROW (IF ANY)
        $contentRow = null;
        if (!empty($content_id)) {
          $contentRow = $contentRowset->getRowMatching('mobilecontent_id', $content_id);
          if (null === $contentRow) {
            $message3 = Zend_Registry::get('Zend_Translate')->_('content row missing');
            throw new Exception($message3);
          }
        }

        //GET EXISTING PARENT ROW (IF ANY)
        $parentContentRow = null;
        if (!empty($parent_id)) {
          $parentContentRow = $contentRowset->getRowMatching('mobilecontent_id', $parent_id);
        } else if (!empty($tmp_parent_id)) {
          $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
        }

        //EXISTING ROW
        if (!empty($contentRow) && is_object($contentRow)) {
          $existingRowsByContentId[$content_id] = $contentRow;

          //UPDATE ROW
          if (!empty($parentContentRow)) {
            $contentRow->parent_content_id = $parentContentRow->mobilecontent_id;
          }
          if (empty($contentRow->parent_content_id)) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }
					$session = new Zend_Session_Namespace();

          //SET PARAMS
          if (isset($session->setSomething) && in_array($element['name'], $session->setSomething)) {
            $contentRow->params = json_encode($element['params']);
            $contentRow->widget_admin = 0;
					}
          if ($contentRow->type == 'container') {
            $newOrder = array_search($contentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow->order = $newOrder;
          $contentRow->save();
        }
        //NEW ROW
        else {
          if (empty($element['type']) || empty($element['name'])) {
            $message4 = Zend_Registry::get('Zend_Translate')->_('missing name and/or type info');
            throw new Exception($message4);
          }

          if ($element['type'] == 'container') {
            $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow = $contentTable->createRow();
          $contentRow->mobilecontentpage_id = $pageObject->mobilecontentpage_id;
          $contentRow->order = $newOrder;
          $contentRow->type = $element['type'];
          $contentRow->name = $element['name'];
          $contentRow->widget_admin = 0;

          //SET PARENT CONTENT
          if (!empty($parentContentRow)) {
            $contentRow->parent_content_id = $parentContentRow->mobilecontent_id;
          }
          if (empty($contentRow->parent_content_id)) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }

					$contentRow->params = json_encode($element['params']);
					$contentRow->save();
					$newRowsByTmpId[$tmp_content_id] = $contentRow;
				}
			}
      //DELETE ROWS THAT WERE NOT PRESENT IN DATA SENT BACK
      $deletedRowIds = array();
      foreach ($contentRowset as $contentRow) {
        if (empty($existingRowsByContentId[$contentRow->mobilecontent_id])) {
          $deletedRowIds[] = $contentRow->mobilecontent_id;
          $contentRow->delete();
        }
      }
      $this->view->deleted = $deletedRowIds;

      //SEND BACK NEW CONTENT INFO
      $newData = array();
      foreach ($newRowsByTmpId as $tmp_id => $newRow) {
        $newData[$tmp_id] = $pageTable->createElementParams($newRow);
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
    if (isset($session->setSomething))
      unset($session->setSomething);
  }

  public function widgetAction() {

    $page_id = $this->_getParam('page_id');
    $mod = $this->_getParam('mod');
    //RENDER BY WIDGET NAME    
    $name = $this->_getParam('name');
    if (null === $name) {
      $message5 = Zend_Registry::get('Zend_Translate')->_('no widget found with name:');
      throw new Exception($message5 . $name);
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
      //ADD SUBMIT BUTTON
      if (!$form->getElement('submit') && !$form->getElement('execute')) {
        $form->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
      }

      //ADD NAME
      $form->addElement('Hidden', 'name', array(
          'value' => $name
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
        ));
      }

      if (!$form->getDisplayGroup('buttons')) {
        $submitName = ( $form->getElement('execute') ? 'execute' : 'submit' );
        $form->addDisplayGroup(array(
            $submitName,
            'cancel',
                ), 'buttons', array(
        ));
      }

      //FORCE METHOD AND ACTION
      $form->setMethod('post')
              ->setAction($_SERVER['REQUEST_URI']);

      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
        $this->view->values = $form->getValues();
        $this->view->form = null;
				$session = new Zend_Session_Namespace();
				if (isset($session->setSomething))
					unset($session->setSomething);

        $session = new Zend_Session_Namespace();
				$session->setSomething[] = $name;

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
          'description' => 'Number of items to show.',
          'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
          ),
          'order' => 1000000 - 1,
      ));
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->view->values = $form->getValues();
      $this->view->form = null;
      $session = new Zend_Session_Namespace();
      if (isset($session->setSomething))
        unset($session->setSomething);
      $session->setSomething[] = $name;
    } else {
      $form->populate($this->_getAllParams());
    }
  }

  public function saveAction() {

    $form = new Sitepage_Form_Layout_Content_MobilePage();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $page_id = $values['mobilecontentpage_id'];
      unset($values['mobilecontentpage_id']);

      if (empty($values['url'])) {
        $values['url'] = new Zend_Db_Expr('NULL');
      }

      $pageTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
      $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page_id)->orWhere('mobilecontentpage_id = ?', $page_id));
      $pageObject->setFromArray($values)->save();
      $form->addNotice($this->view->translate('Your changes have been saved.'));
    }

    $this->getResponse()->setBody($form->render($this->view));
    $this->_helper->layout->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }

  public function deleteAction() {

    $page_id = $this->_getParam('page');
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    $page = Engine_Api::_()->getDbtable('mobilecontentpages', 'sitepage')->find($page_id)->current();
    if (null === $page) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Page not found');
      return;
    }

    if (!$page->custom) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Cannot delete non-custom pages');
      return;
    }

    $pageTable->deletePage($page);
    $this->view->status = true;
  }
}

?>