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
class Sitepage_LayoutController extends Core_Controller_Action_Standard {

    //SET THE VALUE FOR ALL ACTION DEFAULT
    public function init() {

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

    public function layoutAction() {

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
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitepage');
        $contentTableName = $contentTable->info('name');
        $contentPageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
        $contentPageTableName = $contentPageTable->info('name');
        $contentpage_id = $contentPageTable->select()
                ->from($contentPageTableName, array('contentpage_id'))
                ->where('page_id =?', $sitepage->page_id)
                ->query()
                ->fetchColumn();
        //GET PAGE PARAM
        $page = $contentpage_id;
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitepage');
        $this->view->adminDriven = 0;
        if (empty($page)) {
            $corepageinfo = Engine_Api::_()->sitepage()->getWidgetizedPage();
            $corePagesTable = Engine_Api::_()->getDbtable('pages', 'core');
            $corePagesTableName = $corePagesTable->info('name');
            $contentCoreTable = Engine_Api::_()->getDbtable('content', 'core');
            $contentCoreTableName = $contentCoreTable->info('name');
            $adminContentTable = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
            $adminContentTableName = $adminContentTable->info('name');
            $contentpage_id = $adminContentTable->select()
                    ->from($adminContentTableName, array('page_id'))
                    ->where('page_id =?', $corepageinfo->page_id)
                    ->query()
                    ->fetchColumn();
            //GET CURRENT PAGE
            $this->view->pageObject = $pageObject = $corePagesTable->fetchRow($corePagesTable->select()->where('name = ?', 'sitepage_index_view')->orWhere('page_id = ?', $contentpage_id));
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
            $this->view->pageObject = $pageObject = $contentPageTable->fetchRow($contentPageTable->select()->where('user_id = ?', $viewer_id)->where('name = ?', $page)->orWhere('contentpage_id = ?', $page));
            if (null === $pageObject) {
                $page = 'core_index_index';
                $pageObject = $pageTable->fetchRow($contentPageTable->select()->where('name = ?', $page)->where('user_id = ?', $viewer_id));
            }
            //GET REGISTERED CONTENT AREAS
            if (!empty($pageObject)) {
                $contentRowset = $contentTable->fetchAll($contentTable->select()->where('contentpage_id = ?', $pageObject->contentpage_id)->order('order ASC'));
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
        $this->view->pageForm = $pageForm = new Sitepage_Form_Layout_Content_Page();
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

        $this->view->showeditinwidget = array('seaocore.feed', 'activity.feed', 'sitepage.info-sitepage', 'sitepage.overview-sitepage', 'sitepage.location-sitepage', 'core.profile-links', 'sitepage.discussion-sitepage', 'sitepagepoll.profile-sitepagepolls', 'sitepageevent.profile-sitepageevents', 'sitepageoffer.profile-sitepageoffers', 'sitepagedocument.profile-sitepagedocuments', 'sitepageform.sitepage-viewform', 'sitepagereview.profile-sitepagereviews', 'sitepagenote.profile-sitepagenotes', 'sitepagevideo.profile-sitepagevideos', 'sitepage.photos-sitepage', 'sitepagemusic.profile-sitepagemusic', 'sitepageintegration.profile-items', 'sitepagetwitter.feeds-sitepagetwitter', 'advancedactivity.home-feeds', 'sitepagemember.profile-sitepagemembers', 'siteevent.contenttype-events', 'sitevideo.contenttype-videos', 'sitecrowdfunding.contenttype-projects','sitemusic.contenttype-music');

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
        if (!empty($coreVersion)) {
            $coreVersion = $coreVersion->version;
            if (!empty(Engine_Api::_()->seaocore()->checkVersion($coreVersion, '4.2.1'))) {
                $isSupport = 1;
            }
        }
        if (!empty($isSupport)) {
            $this->renderScript('layout/layout.tpl');
        } else {
            $this->renderScript('layout/layout_default.tpl');
        }
    }

    public function updateAction() {

        $pageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitepage');
        $db = $pageTable->getAdapter();
        $db->beginTransaction();

        try {
            //GET PAGE
            $page = $this->_getParam('page');
            $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('contentpage_id = ?', $page));
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
            $contentRowset = $contentTable->fetchAll($contentTable->select()->where('contentpage_id = ?', $pageObject->contentpage_id));

            //GET STRUCTURE
            $paramStructure = $this->_getParam('structure');
            $structure = !empty($paramStructure) ? Zend_Json::decode($paramStructure) : array();

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
                    $contentRow = $contentRowset->getRowMatching('content_id', $content_id);
                    if (null === $contentRow) {
                        $message3 = Zend_Registry::get('Zend_Translate')->_('content row missing');
                        throw new Exception($message3);
                    }
                }

                //GET EXISTING PARENT ROW (IF ANY)
                $parentContentRow = null;
                if (!empty($parent_id)) {
                    $parentContentRow = $contentRowset->getRowMatching('content_id', $parent_id);
                } else if (!empty($tmp_parent_id)) {
                    $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
                }

                //EXISTING ROW
                if (!empty($contentRow) && is_object($contentRow)) {
                    $existingRowsByContentId[$content_id] = $contentRow;

                    //UPDATE ROW
                    if (!empty($parentContentRow)) {
                        $contentRow->parent_content_id = $parentContentRow->content_id;
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
                    $contentRow->contentpage_id = $pageObject->contentpage_id;
                    $contentRow->order = $newOrder;
                    $contentRow->type = $element['type'];
                    $contentRow->name = $element['name'];
                    $contentRow->widget_admin = 0;

                    //SET PARENT CONTENT
                    if (!empty($parentContentRow)) {
                        $contentRow->parent_content_id = $parentContentRow->content_id;
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
                if (empty($existingRowsByContentId[$contentRow->content_id])) {
                    $deletedRowIds[] = $contentRow->content_id;
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

    public function createAction() {

        //GET PAGE PARAM
        $page = $this->_getParam('page');
        $pageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitepage');

        //MAKE NEW PAGE
        if (($page == 'new' || $page === null) && $this->getRequest()->isPost()) {
            $pageObject = $pageTable->createRow();
            $pageObject->displayname = ( null !== ($name = $this->_getParam('name')) ? $name : 'Untitled' );
            $pageObject->save();

            //CREATE A CONTENT ROW FOR THIS PAGE
            $contentRow = $contentTable->createRow();
            $contentRow->type = 'container';
            $contentRow->name = 'main';
            $contentRow->contentpage_id = $pageObject->contentpage_id;
            $contentRow->save();

            $contentRow2 = $contentTable->createRow();
            $contentRow2->type = 'container';
            $contentRow2->name = 'middle';
            $contentRow2->contentpage_id = $pageObject->contentpage_id;
            $contentRow2->parent_content_id = $contentRow->content_id;
            $contentRow2->save();
        }

        if ($pageObject) {
            return $this->_redirectCustom($this->view->url(array('action' => 'index')) . '?page=' . $pageObject->contentpage_id);
        } else {
            return $this->_redirectCustom($this->view->url(array('action' => 'index')));
        }
    }

    public function saveAction() {

        $form = new Sitepage_Form_Layout_Content_Page();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $page_id = $values['contentpage_id'];
            unset($values['contentpage_id']);

            if (empty($values['url'])) {
                $values['url'] = new Zend_Db_Expr('NULL');
            }

            $pageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
            $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page_id)->orWhere('contentpage_id = ?', $page_id));
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

        $page = Engine_Api::_()->getDbtable('contentpages', 'sitepage')->find($page_id)->current();
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
                'value' => $name,
                'order' => $orderIndex++,
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

    public function getContentAreas() {

        $contentAreas = array();
        $levelModules = array("offer" => "sitepageoffer", "form" => "sitepageform", "invite" => "sitepageinvite", "sdcreate" => "sitepagedocument", "sncreate" => "sitepagenote", "splcreate" => "sitepagepoll", "secreate" => "sitepageevent", "svcreate" => "sitepagevideo", "spcreate" => "sitepagealbum", "sdicreate" => "sitepagediscussion", "smcreate" => "sitepagemusic", "smecreate" => "sitepagemusic", "twitter" => "sitepagetwitter");
        //$sitepageintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration');
        //FROM MODULES
        $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();

        $flag = 0;
        $integrated_module = '';
        foreach ($modules as $module => $path) {
            if ($module == 'sitepage' || $module == 'core' || $module == 'sitepagenote' || $module == 'activity' || $module == 'sitepagedocument' || $module == 'sitepageevent' || $module == 'sitepagereview' || $module == 'sitepagepoll' || $module == 'sitepagevideo' || $module == 'sitepageform' || $module == 'sitepagediscussion' || $module == 'sitepagealbum' || $module == 'sitepageoffer' || $module == 'sitepagebadge' || $module == 'facebookse' || $module == 'sitelike' || $module == 'suggestion' || $module == 'sitepagemusic' || $flag || $module == 'sitepagetwitter' || $module == 'sitepagemember' || $module == 'sitecontentcoverphoto' || $module == 'siteusercoverphoto' || $module == 'siteevent'  || $module == 'sitevideo' || $module == 'sitecrowdfunding'|| $module == 'sitemusic' || $module == 'seaocore' || $module == 'advancedactivity') {
                if ($module == 'activity' || $module == 'core' || $module == 'facebookse' || $module == 'sitelike' || $module == 'suggestion' || $module == 'sitecontentcoverphoto' || $module == 'siteusercoverphoto' || $module == 'seaocore' || $module == 'advancedactivity') {
                    $contentManifestFile = dirname($path) . '/settings/content.php';
                } else {
                    $addFile = true;
                    $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

                    if ($flag == 0) {
                        if ($module != 'sitepage' && $module != 'sitepagereview' && $module != 'sitepagebadge' && $module != 'sitepagetwitter') {

                            if ($module == 'siteevent') {
                                $module = 'sitepageevent';
                            } else if($module == 'sitevideo') {
                                $module = 'sitepagevideo';
                            } else if($module == 'document') {
                                $module = 'sitepagedocument';
                            }
                            //PACKAGE BASE PRIYACY START
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", $module)) {
                                    $addFile = false;
                                }
                            } else {
                                //non sub modules
                                $search_Key = array_search($module, $levelModules);
                                if (!empty($search_Key))
                                    $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, $search_Key);
                                if (empty($isPageOwnerAllow)) {
                                    $addFile = false;
                                }
                            }
                            //PACKAGE BASE PRIYACY END
                        }
                    } elseif ($module == 'sitepagetwitter') {
                        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'twitter');
                        if (empty($isManageAdmin)) {
                            $addFile = false;
                        }
                    }

                    $contentManifestFile = '';
                    if ($addFile)
                        $contentManifestFile = dirname($path) . '/settings/content_user.php';
                }
                if (!file_exists($contentManifestFile))
                    continue;
                $ret = include $contentManifestFile;
                $contentAreas = array_merge($contentAreas, (array) $ret);
            }
        }
        $pagelayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layout.setting', 1);
        //$layoutBlockTable = Engine_Api::_()->getDbtable('layoutblocks', 'sitepage');
        foreach ($contentAreas as $key => $item) {
            if ($pagelayout) {
                if ($item['name'] == 'core.content' || $item['name'] == 'core.theme-choose' || $item['name'] == 'core.menu-footer' || $item['name'] == 'core.menu-generic' || $item['name'] == 'core.menu-main' || $item['name'] == 'core.menu-mini' || $item['name'] == 'core.menu-logo' || $item['name'] == 'core.statistics' || $item['name'] == 'activity.list-requests' || $item['name'] == 'sitepageevent.profile-photo' || $item['name'] == 'sitepageevent.profile-options' || $item['name'] == 'sitepageevent.profile-info' || $item['name'] == 'sitepageevent.profile-rsvp' || $item['name'] == 'sitepageevent.profile-members' || $item['name'] == 'sitepageevent.profile-status' || $item['name'] == 'Facebookse.facebookse-recommendation' || $item['name'] == 'Facebookse.facebookse-activity' || $item['name'] == 'Facebookse.facebookse-facepile' || $item['name'] == 'Facebookse.facebookse-likebox' || $item['name'] == 'Facebookse.facebookse-websitelike' || $item['name'] == 'Facebookse.facebookse-groupprofilelike' || $item['name'] == '
Facebookse.facebookse-eventprofilelike' || $item['name'] == 'Facebookse.facebookse-userprofilelike' || $item['name'] == 'Facebookse.facebookse-listprofilelike' || $item['name'] == 'Facebookse.facebookse-sitepageeventprofilelike' || $item['name'] == 'Suggestion.suggestion-classified' || $item['name'] == 'Suggestion.explore-friend' || $item['name'] == 'Suggestion.suggestion-album' || $item['name'] == 'Suggestion.suggestion-blog' || $item['name'] == 'Suggestion.suggestion-document' || $item['name'] == 'Suggestion.suggestion-event' || $item['name'] == 'Suggestion.suggestion-forum' || $item['name'] == 'Suggestion.suggestion-friend' || $item['name'] == 'Suggestion.suggestion-mix' || $item['name'] == 'Suggestion.suggestion-list' || $item['name'] == 'Suggestion.suggestion-music' || $item['name'] == 'Suggestion.suggestion-poll' || $item['name'] == 'Suggestion.suggestion-video' || $item['name'] == 'Suggestion.suggestion-group' || $item['name'] == 'sitelike.event-friend-like' || $item['name'] == 'sitelike.event-like' || $item['name'] == 'sitelike.event-like-button' || $item['name'] == 'sitelike.group-friend-like' || $item['name'] == 'sitelike.group-like' || $item['name'] == 'sitelike.group-like-button' || $item['name'] == '
sitelike.list-browse-mixlikes' || $item['name'] == 'sitelike.list-friend-like' || $item['name'] == 'sitelike.list-like' || $item['name'] == 'sitelike.list-like-album' || $item['name'] == 'sitelike.list-like-albumphoto' || $item['name'] == 'sitelike.list-like-blogs' || $item['name'] == 'sitelike.list-like-eventphotos' || $item['name'] == 'sitelike.list-like-events' || $item['name'] == 'sitelike.list-like-forum' || $item['name'] == 'sitelike.list-like-groupphotos' || $item['name'] == 'sitelike.list-like-groups' || $item['name'] == 'sitelike.list-like-listings' || $item['name'] == 'sitelike.list-like-members' || $item['name'] == 'sitelike.list-like-musics' || $item['name'] == 'sitelike.list-like-pages' || $item['name'] == 'sitelike.list-like-button' || $item['name'] == 'sitelike.list-like-classifieds' || $item['name'] == 'sitelike.list-like-document' || $item['name'] == 'sitelike.list-like-videos' || $item['name'] == 'sitelike.member-friend-like' || $item['name'] == 'sitelike.member-like' || $item['name'] == '
sitelike.mix-like' || $item['name'] == 'sitelike.navigation-like' || $item['name'] == 'sitelike.page-like' || $item['name'] == 'sitelike.profile-like-button' || $item['name'] == 'sitelike.profile-user-likes' || $item['name'] == 'sitelike.list-like-pagealbumphotos' || $item['name'] == 'sitelike.list-like-pagealbums' || $item['name'] == 'sitelike.list-like-pagedocuments' || $item['name'] == 'sitelike.list-like-pageevent' || $item['name'] == 'sitelike.list-like-pagenotes' || $item['name'] == 'sitelike.list-like-pagepolls' || $item['name'] == 'sitelike.list-like-pagereviews' || $item['name'] == 'sitelike.list-like-pagevideos' || $item['name'] == 'sitelike.list-like-recipe' || $item['name'] == 'sitelike.pageevent-friend-like' || $item['name'] == 'sitelike.pageevent-like' || $item['name'] == 'sitelike.pageevent-like-button' || $item['name'] == 'sitelike.recipe-friend-like' || $item['name'] == 'sitelike.recipe-like' || $item['name'] == 'sitelike.recipe-like-button' || $item['name'] == 'sitelike.sitepageevent-like-
button' || $item['name'] == 'sitelike.list-like-polls' || $item['name'] == 'Facebookse.facebookse-comments'
                ) {
                    unset($contentAreas[$key]);
                }
            } else {
                if ($item['name'] == 'Suggestion.common-suggestion' || $item['name'] == 'core.content' || $item['name'] == 'core.theme-choose' || $item['name'] == 'core.menu-footer' || $item['name'] == 'core.menu-generic' || $item['name'] == 'core.menu-main' || $item['name'] == 'core.menu-mini' || $item['name'] == 'core.menu-logo' || $item['name'] == 'core.statistics' || $item['name'] == 'activity.list-requests' || $item['name'] == 'sitepageevent.profile-photo' || $item['name'] == 'sitepageevent.profile-options' || $item['name'] == 'sitepageevent.profile-info' || $item['name'] == 'sitepageevent.profile-rsvp' || $item['name'] == 'sitepageevent.profile-members' || $item['name'] == 'sitepageevent.profile-status' || $item['name'] == 'core.container-tabs' || $item['name'] == 'Facebookse.facebookse-recommendation' || $item['name'] == 'Facebookse.facebookse-activity' || $item['name'] == 'Facebookse.facebookse-facepile' || $item['name'] == 'Facebookse.facebookse-likebox' || $item['name'] == 'Facebookse.facebookse-
websitelike' || $item['name'] == 'Facebookse.facebookse-groupprofilelike' || $item['name'] == 'Facebookse.facebookse-eventprofilelike' || $item['name'] == 'Facebookse.facebookse-userprofilelike' || $item['name'] == 'Facebookse.facebookse-listprofilelike' || $item['name'] == 'Facebookse.facebookse-sitepageeventprofilelike' || $item['name'] == 'Suggestion.suggestion-classified' || $item['name'] == 'Suggestion.explore-friend' || $item['name'] == 'Suggestion.suggestion-album' || $item['name'] == 'Suggestion.suggestion-blog' || $item['name'] == 'Suggestion.suggestion-document' || $item['name'] == 'Suggestion.suggestion-event' || $item['name'] == 'Suggestion.suggestion-forum' || $item['name'] == 'Suggestion.suggestion-friend' || $item['name'] == 'Suggestion.suggestion-mix' || $item['name'] == 'Suggestion.suggestion-list' || $item['name'] == 'Suggestion.suggestion-music' || $item['name'] == 'Suggestion.suggestion-poll' || $item['name'] == 'Suggestion.suggestion-video' || $item['name'] == 'Suggestion.suggestion-
group' || $item['name'] == 'sitelike.event-friend-like' || $item['name'] == 'sitelike.event-like' || $item['name'] == 'sitelike.event-like-button' || $item['name'] == 'sitelike.group-friend-like' || $item['name'] == 'sitelike.group-like' || $item['name'] == 'sitelike.group-like-button' || $item['name'] == 'sitelike.list-browse-mixlikes' || $item['name'] == 'sitelike.list-friend-like' || $item['name'] == 'sitelike.list-like' || $item['name'] == 'sitelike.list-like-album' || $item['name'] == 'sitelike.list-like-albumphoto' || $item['name'] == 'sitelike.list-like-blogs' || $item['name'] == 'sitelike.list-like-eventphotos' || $item['name'] == 'sitelike.list-like-events' || $item['name'] == 'sitelike.list-like-forum' || $item['name'] == 'sitelike.list-like-groupphotos' || $item['name'] == 'sitelike.list-like-groups' || $item['name'] == 'sitelike.list-like-listings' || $item['name'] == 'sitelike.list-like-members' || $item['name'] == 'sitelike.list-like-musics' || $item['name'] == 'sitelike.list-like-pages' ||
                        $item['name'] == 'sitelike.list-like-button' || $item['name'] == 'sitelike.list-like-classifieds' || $item['name'] == 'sitelike.list-like-document' || $item['name'] == 'sitelike.list-like-videos' || $item['name'] == 'sitelike.member-friend-like' || $item['name'] == 'sitelike.member-like' || $item['name'] == 'sitelike.mix-like' || $item['name'] == 'sitelike.navigation-like' || $item['name'] == 'sitelike.page-like' || $item['name'] == 'sitelike.profile-like-button' || $item['name'] == 'sitelike.profile-user-likes' || $item['name'] == 'sitelike.list-like-pagealbumphotos' || $item['name'] == 'sitelike.list-like-pagealbums' || $item['name'] == 'sitelike.list-like-pagedocuments' || $item['name'] == 'sitelike.list-like-pageevent' || $item['name'] == 'sitelike.list-like-pagenotes' || $item['name'] == 'sitelike.list-like-pagepolls' || $item['name'] == 'sitelike.list-like-pagereviews' || $item['name'] == 'sitelike.list-like-pagevideos' || $item['name'] == 'sitelike.list-like-recipe' || $item['name'] == 'sitelike.
pageevent-friend-like' || $item['name'] == 'sitelike.pageevent-like' || $item['name'] == 'sitelike.pageevent-like-button' || $item['name'] == 'sitelike.recipe-friend-like' || $item['name'] == 'sitelike.recipe-like' || $item['name'] == 'sitelike.recipe-like-button' || $item['name'] == 'sitelike.sitepageevent-like-button' || $item['name'] == 'sitelike.list-like-polls'
                ) {
                    unset($contentAreas[$key]);
                }
            }
        }

        // From widgets
        $it = new DirectoryIterator(APPLICATION_PATH . '/application/widgets');
        foreach ($it as $dir) {
            if (!$dir->isDir() || $dir->isDot())
                continue;
            $path = $dir->getPathname();
            $contentManifestFile = $path . '/' . 'manifest.php';
            if (!file_exists($contentManifestFile))
                continue;
            $ret = include $contentManifestFile;
            if (!is_array($ret))
                continue;
            foreach ($ret as $key => $value) {
                if ((isset($value['name']) && $value['name'] == 'rss')) {
                    array_push($contentAreas, $ret);
                }
            }
        }

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
        $pageAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageAdminTableName = $pageAdminTable->info('name');
        $selectPageAdmin = $pageAdminTable->select()
                ->setIntegrityCheck(false)
                ->from($pageAdminTableName)
                ->where('name = ?', 'sitepage_index_view');
        $pageAdminresult = $pageAdminTable->fetchRow($selectPageAdmin);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        //NOW INSERTING THE ROW IN PAGE TABLE
        $pageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');

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
            Engine_Api::_()->getDbtable('content', 'sitepage')->setContentDefault($contentPageId, $sitepage_layout_cover_photo);
        } else {
            Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultLayout($contentPageId, $sitepage_layout_cover_photo);
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefreshTime' => '60',
            'parentRefresh' => 'true',
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Layout for this page has been changed successfully.'))
        ));
    }

}

?>