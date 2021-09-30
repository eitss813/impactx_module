<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_AdminManageController extends Core_Controller_Action_Admin {

  public function indexAction() {

    // MAKE NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestaticpage_admin_main', array(), 'sitestaticpage_admin_main_manage');

    $page_id = Engine_Api::_()->sitestaticpage()->getPageId('', '', 1);
    $this->view->default_pageid = $page_id;
    $this->view->canCreate = false;
    $enble_sitemobile = Engine_Api::_()->sitestaticpage()->isSitemobileEnabled();
    if ($enble_sitemobile) {
      $this->view->mobile_enabled = 1;
    }
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $page = Engine_Api::_()->getItem('sitestaticpage_page', $value);
          if ($enble_sitemobile) {
            if ($page->menu == 0) {
              $db->query('DELETE FROM engine4_sitemobile_menuitems WHERE name = "core_main_sitestaticpage_' . $value . '"');
            } elseif ($page->menu == 1) {
              $db->query('DELETE FROM engine4_sitemobile_menuitems WHERE name = "core_mini_sitestaticpage_' . $value . '"');
            } else {
              $db->query('DELETE FROM engine4_sitemobile_menuitems WHERE name = "core_footer_sitestaticpage_' . $value . '"');
            }
            $mobilewidgetizedpage_id = Engine_Api::_()->sitestaticpage()->getMobileWidetizedpageId($value);
            if (!empty($mobilewidgetizedpage_id)) {
              $db->query('DELETE FROM engine4_sitemobile_content WHERE page_id = "' . $mobilewidgetizedpage_id . '"');
              $db->query('DELETE FROM engine4_sitemobile_pages WHERE name = "sitestaticpage_index_index_staticpageid_' . $value . '"');
            }
            if (!empty($page->search)) {
              $db->query('DELETE FROM engine4_core_search WHERE id = "' . $value . '"');
            }
          }
          // DELETE NAVIGATION MENU ENTRY
          if ($page->menu == 0) {
            $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_main_sitestaticpage_' . $value . '"');
          } elseif ($menu == 1) {
            $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_mini_sitestaticpage_' . $value . '"');
          } elseif ($menu == 2) {
            $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_footer_sitestaticpage_' . $value . '"');
          }

          // DELETE WIDGETIZED PAGE AND CONTENT TABLE ENTRY
          $widegtizedpgae_id = Engine_Api::_()->sitestaticpage()->getWidetizedpageId($value);
          if (!empty($widegtizedpgae_id)) {
            $db->query('DELETE FROM engine4_core_content WHERE page_id = "' . $widegtizedpgae_id . '"');
            $db->query('DELETE FROM engine4_core_pages WHERE name = "sitestaticpage_index_index_staticpageid_' . $value . '"');
          }
          if (!empty($page->search)) {
            $db->query('DELETE FROM engine4_core_search WHERE id = "' . $value . '"');
          }
          $page->delete();
        }
      }
    }
    $page = $this->_getParam('page', 1);
    include APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/license/license2.php';
  }

  //CREATE STATIC PAGE ACTION
  public function createAction() {

    // MAKE NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestaticpage_admin_main', array(), 'sitestaticpage_admin_main_manage');

    // GET SETTINGS API
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //FORM GENERATION
    $this->view->form = $form = new Sitestaticpage_Form_Admin_Manage_Create();

    //MULTI LANGUAGE IS ALLOWED OR NOT
    $this->view->multiLanguage = $settings->getSetting('sitestaticpage.multilanguage', 0);

    //DEFAULT LANGUAGE
    $this->view->defaultLanguage = $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');

    // MULTI LANGUAGE WORK
    $this->view->languageCount = 0;
    $this->view->languageData = array();
    $default_body_link = $this->view->add_show_hide_link = 'body';
    $default_title_link = 'title';
    if ($this->view->multiLanguage) {
      //GET LANGUAGE ARRAY
      $localeMultiOptions = Engine_Api::_()->sitestaticpage()->getLanguageArray();
      $languages = $settings->getSetting('sitestaticpage.languages');

      if ($this->view->multiLanguage) {
        Engine_Api::_()->getDbtable('pages', 'sitestaticpage')->createColumns($languages);
      }
      $this->view->languageCount = $langugaeCount = Count($languages);
      $this->view->languageData = array();
      foreach ($languages as $label) {
        $this->view->languageData[] = $label;

        if ($this->view->languageCount >= 2 && $defaultLanguage == $label && $label != 'en') {
          $default_body_link = $this->view->add_show_hide_link = "body_$label";
          $default_title_link = "title_$label";
        }
      }
      if (!in_array($defaultLanguage, $this->view->languageData)) {
        $this->view->defaultLanguage = 'en';
      }
    }
    $this->view->default_url = $settings->getSetting('sitestaticpage.manifestUrl', 'static');

    // FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // PROCESS
    $values = $form->getValues();
    if ($this->view->multiLanguage && !empty($languages)) {
      foreach ($languages as $language) {
        if (count($language) == 1) {
          if ($language == 'en') {
            $output = Engine_Api::_()->sitestaticpage()->getstring($values['body'], '[static_form', ']');
            $values['params'] = serialize($output);
          } else {
            $output = Engine_Api::_()->sitestaticpage()->getstring($values['body'], '[static_form', ']');
            $values['params'] = serialize($output);
            $values['params' . "_$language"] = serialize($output);
          }
        } else {
          if ($language == 'en') {
            $output = Engine_Api::_()->sitestaticpage()->getstring($values['body'], '[static_form', ']');
            $values['params'] = serialize($output);
          } else {
            $column_name = "body" . "_$language";
            $output = Engine_Api::_()->sitestaticpage()->getstring($values[$column_name], '[static_form', ']');
            $values['params' . "_$language"] = serialize($output);
          }
        }
      }
    } else {
      $output = Engine_Api::_()->sitestaticpage()->getstring($values['body'], '[static_form', ']');
      $values['params'] = serialize($output);
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $table = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');

      // CHECK URL AVAILABILITY ON SUBMIT IF CONTENT IS STATIC PAGE 
      if ($values['type'] == 0) {
        $urlArray = Engine_Api::_()->sitestaticpage()->getBannedUrls();
        $selectTable = $table->select()
                ->where('page_url = ?', $values['page_url']);
        $resultSitestaticpageTable = $table->fetchAll($selectTable);
        if (empty($values['page_url'])) {
          $form->addError('Please Enter a valid page url.');
          return;
        }
        if (count($resultSitestaticpageTable) || (in_array(strtolower($values['page_url']), $urlArray))) {
          $form->addError('This URL has been restricted by our automated system. Please choose another URL.');
          return;
        }
      }

      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();
      $page = $table->createRow();
      if ($values['type'] == 1) {
        $values['menu'] = 3;
      }
      unset($values['link_title']);
      if ($values['type'] == 0) {
        if (empty($_POST['page_widget'])) {

          $meta_info = array('page_title' => $_POST['page_title'], 'page_description' => $_POST['page_description'], 'keywords' => $_POST['keywords']);
          $values['meta_info'] = serialize($meta_info);
        }
      }
      unset($values['page_title']);
      unset($values['page_description']);
      unset($values['keywords']);
      $page->setFromArray($values);
      if ($this->view->multiLanguage && !empty($languages)) {
        foreach ($languages as $language) {
          if (count($languages) == 1) {
            if ($language == 'en') {
              $page->body = $_POST['body'];
            } else {
              $column_name = "body" . "_$language";

              $page->$column_name = $_POST['body'];
            }
          } else {
            if ($language == 'en') {
              $page->body = $_POST['body'];
            } else {
              $column_name = "body" . "_$language";

              $page->$column_name = $_POST[$column_name];
            }
          }
        }
      } else {
        $page->body = $_POST['body'];
      }

      // STORING LEVELS 
      if (!isset($_POST['level_id'])) {

        $page->level_id = '["0"]';
      } else {
        $page->level_id = Zend_Json_Encoder::encode($_POST['level_id']);
        if (strstr($page->level_id, '"0"')) {
          $page->level_id = '["0"]';
        }
      }

      // STORING NETWORKS
      if (!isset($_POST['networks'])) {
        $page->networks = '["0"]';
      } else {
        $page->networks = Zend_Json_Encoder::encode($_POST['networks']);
        if (strstr($page->networks, '"0"'))
          $page->networks = '["0"]';
      }
      $staticpage_id = $page->page_id;

      include APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/license/license2.php';
      $db->commit();

      //REDIRECT
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //EDIT STATIC OR HTML BLOCK PAGE
  public function editAction() {

    $this->view->staticpage_id = $staticpage_id = $this->_getParam('staticpage_id');

    $page = Engine_Api::_()->getItem('sitestaticpage_page', $this->_getParam('staticpage_id'));

    // NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestaticpage_admin_main', array(), 'sitestaticpage_admin_main_manage');

    // GET SETTINGS API
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $enble_sitemobile = Engine_Api::_()->sitestaticpage()->isSitemobileEnabled();

    $db = Engine_Db_Table::getDefaultAdapter();

    // Prepare form
    $this->view->form = $form = new Sitestaticpage_Form_Admin_Manage_Edit();

    // DISABLE WIDGET CHECK BOX
    $widgetizedpage_id = Engine_Api::_()->sitestaticpage()->getWidetizedpageId($staticpage_id);
    if (!empty($widgetizedpage_id)) {
      $form->getElement('page_widget')
              ->setIgnore(true)
              ->setAttrib('disable', true);
    }

    //MULTI LANGUAGE IS ALLOWED OR NOT
    $this->view->multiLanguage = $settings->getSetting('sitestaticpage.multilanguage', 0);

    //DEFAULT LANGUAGE
    $this->view->defaultLanguage = $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');

    //MULTI LANGUAGE WORK
    $this->view->languageCount = 0;
    $this->view->languageData = array();

    $default_body_link = $this->view->add_show_hide_link = 'body';
    $default_title_link = 'title';
    if ($this->view->multiLanguage) {
      //GET LANGUAGE ARRAY
      $localeMultiOptions = Engine_Api::_()->sitestaticpage()->getLanguageArray();
      $languages = $settings->getSetting('sitestaticpage.languages');
      if ($this->view->multiLanguage) {
        Engine_Api::_()->getDbtable('pages', 'sitestaticpage')->createColumns($languages);
      }
      $this->view->languageCount = Count($languages);
      $this->view->languageData = array();
      foreach ($languages as $label) {
        $this->view->languageData[] = $label;
        if ($this->view->languageCount >= 2 && $defaultLanguage == $label && $label != 'en') {
          $default_body_link = $this->view->add_show_hide_link = "body_$label";
          $default_title_link = "title_$label";
        }
      }
    }

    $this->view->default_url = $settings->getSetting('sitestaticpage.manifestUrl', 'static');

    if (!in_array($defaultLanguage, $this->view->languageData)) {
      $this->view->defaultLanguage = 'en';
    }

    // POPULATE VALUES IN FORM
    $values = $page->toArray();
    $form->populate($values);
    if (!empty($page->meta_info)) {
      $meta_info = unserialize($page->meta_info);
      $form->getElement('page_title')->setValue($meta_info['page_title']);
      $form->getElement('page_description')->setValue($meta_info['page_description']);
      $form->getElement('keywords')->setValue($meta_info['keywords']);
    }
    $link_title = $db->select()
            ->from('engine4_core_menuitems', 'label')
            ->where('name = ?', "core_main_sitestaticpage_" . $staticpage_id)
            ->orwhere('name = ?', "core_mini_sitestaticpage_" . $staticpage_id)
            ->orwhere('name = ?', "core_footer_sitestaticpage_" . $staticpage_id)
            ->limit(1)
            ->query()
            ->fetchColumn();
    if ($page->menu != 3 && !empty($link_title))
      $form->getElement('link_title')->setValue($link_title);

    //SHOW PREFIELD MEMBER LEVELS
    if ($page->level_id) {
      $form->getElement('level_id')->setValue(Zend_Json_Decoder::decode($page->level_id));
    }

    //SHOW PREFIELD NETWORKS
    if ($page->networks) {
      $form->getElement('networks')->setValue(Zend_Json_Decoder::decode($page->networks));
    }

    if (!empty($page->page_url) && !empty($widgetizedpage_id)) {
      $page_data = $db->select()
              ->from('engine4_core_pages', array('title', 'description', 'keywords', 'search'))
              ->where('name = ?', "sitestaticpage_index_index_staticpageid_" . $staticpage_id)
              ->limit(1)
              ->query()
              ->fetchAll();
      $form->getElement('page_title')->setValue($page_data[0]['title']);
      $form->getElement('page_description')->setValue($page_data[0]['description']);
      $form->getElement('keywords')->setValue($page_data[0]['keywords']);
      $form->getElement('search')->setValue($page_data[0]['search']);
    }

    // Check post/form
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $db->beginTransaction();

    try {
      $oldPageUrl = !empty($page->page_url) ? $page->page_url : null;
      $values = $form->getValues();
      if ($values['type'] == 1) {
        $values['menu'] = 3;
      }
      if (empty($widgetizedpage_id)) {
        $meta_info = array('page_title' => $_POST['page_title'], 'page_description' => $_POST['page_description'], 'keywords' => $_POST['keywords']);
        $values['meta_info'] = serialize($meta_info);
      }
      if ($this->view->multiLanguage && !empty($languages)) {
        foreach ($languages as $language) {
          if (count($languages) == 1) {
            if ($language == 'en') {
              $output = Engine_Api::_()->sitestaticpage()->getstring($values['body'], '[static_form', ']');
              $values['params'] = serialize($output);
            } else {
              $column_name = "body" . "_$language";
              $output = Engine_Api::_()->sitestaticpage()->getstring($values['body'], '[static_form', ']');
              $values['params'] = serialize($output);
              $values['params' . "_$language"] = serialize($output);
            }
          } else {
            if ($language == 'en') {
              $output = Engine_Api::_()->sitestaticpage()->getstring($values['body'], '[static_form', ']');
              $values['params'] = serialize($output);
            } else {
              $column_name = "body" . "_$language";
              $output = Engine_Api::_()->sitestaticpage()->getstring($values[$column_name], '[static_form', ']');
              $values['params' . "_$language"] = serialize($output);
            }
          }
        }
      } else {
        $output = Engine_Api::_()->sitestaticpage()->getstring($values['body'], '[static_form', ']');
        $values['params'] = serialize($output);
      }
      unset($values['link_title']);
      if ($values['type'] == 0) {
        if (isset($values['menu'])) {
          if (empty($_POST['link_title']))
            $_POST['link_title'] = $_POST['title'];
          if($enble_sitemobile){
          $sitemobile_menu_id = $db->select()
                  ->from('engine4_sitemobile_menuitems')
                  ->where('name = ?', "core_main_sitestaticpage_" . $staticpage_id)
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
          }
          $menu_id = $db->select()
                  ->from('engine4_core_menuitems')
                  ->where('name = ?', "core_main_sitestaticpage_" . $staticpage_id)
                  ->orwhere('name = ?', "core_mini_sitestaticpage_" . $staticpage_id)
                  ->orwhere('name = ?', "core_footer_sitestaticpage_" . $staticpage_id)
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
          if ($enble_sitemobile) {
            if (empty($sitemobile_menu_id)) {
              if ($values['menu'] != 3) {
                // DISPALY PAGE IN NAVIGATION MENU
                $this->insertMenu($values['menu'], $staticpage_id, $_POST['link_title'], 999);
              }
            } else {
              if($values['menu'] != 3)
                    $db->query('UPDATE `engine4_sitemobile_menuitems` SET `label` = "' . $_POST['link_title'] . '"  WHERE `name` = "core_main_sitestaticpage_' . $staticpage_id . '";');
              else
                    $db->query('DELETE FROM `engine4_sitemobile_menuitems` WHERE `name` = "core_main_sitestaticpage_' . $staticpage_id . '"');
                    
            }
          }
          if (empty($menu_id)) {
            if ($values['menu'] != 3) {
              $order = $db->select()
                      ->from('engine4_core_menuitems', array('order'))
                      ->order('id DESC')
                      ->limit(1)
                      ->query()
                      ->fetchColumn();
              // DISPALY PAGE IN NAVIGATION MENU
              $this->insertMenu($values['menu'], $staticpage_id, $_POST['link_title'], $order);
            }
          } else {
            if ($page->menu == $values['menu']) {
              if ($page->menu == 0) {
                $db->query('UPDATE `engine4_core_menuitems` SET `label` = "' . $_POST['link_title'] . '"  WHERE `name` = "core_main_sitestaticpage_' . $staticpage_id . '";');
              } else if ($page->menu == 1) {
                $db->query('UPDATE `engine4_core_menuitems` SET `label` = "' . $_POST['link_title'] . '"  WHERE `name` = "core_mini_sitestaticpage_' . $staticpage_id . '";');
              } else if ($page->menu == 2) {
                $db->query('UPDATE `engine4_core_menuitems` SET `label` = "' . $_POST['link_title'] . '"  WHERE `name` = "core_footer_sitestaticpage_' . $staticpage_id . '";');
              }
            } elseif ($page->menu != $values['menu']) {
              if ($values['menu'] != 3) {
                if ($page->menu == 0) {
                  $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_main_sitestaticpage_' . $staticpage_id . '"');
                } elseif ($page->menu == 1) {
                  $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_mini_sitestaticpage_' . $staticpage_id . '"');
                } else {
                  $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_footer_sitestaticpage_' . $staticpage_id . '"');
                }
                if (empty($_POST['link_title']))
                  $_POST['link_title'] = $_POST['title'];
                $this->insertMenu($values['menu'], $staticpage_id, $_POST['link_title'], $order);
              }
              else {
                if ($page->menu == 0) {
                  $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_main_sitestaticpage_' . $staticpage_id . '"');
                } elseif ($page->menu == 1) {
                  $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_mini_sitestaticpage_' . $staticpage_id . '"');
                } else {
                  $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_footer_sitestaticpage_' . $staticpage_id . '"');
                }
              }
            }
          }
        }
      }
      $page->setFromArray($values);
      if ($this->view->multiLanguage && !empty($languages)) {
        foreach ($languages as $language) {
          if (count($languages) == 1) {
            if ($language == 'en') {
              $page->body = $_POST['body'];
            } else {
              $column_name = "body" . "_$language";
              $page->$column_name = $_POST['body'];
            }
          } else {
            if ($language == 'en') {
              $page->body = $_POST['body'];
            } else {
              $column_name = "body" . "_$language";
              $page->$column_name = $_POST[$column_name];
            }
          }
        }
      } else {
        $page->body = $_POST['body'];
      }

      // CHECK URL AVAILABILITY ON SUBMIT
      if ($values['type'] == 0) {
        $urlArray = Engine_Api::_()->sitestaticpage()->getBannedUrls();
        $sitestaticpageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');
        $selectTable = $sitestaticpageTable->select()->where('page_id != ?', $staticpage_id)
                ->where('page_url = ?', $values['page_url']);
        $resultSitestaticpageTable = $sitestaticpageTable->fetchAll($selectTable);
        if (empty($values['page_url'])) {
          $form->addError('Please Enter a valid page url.');
          return;
        }
        if (count($resultSitestaticpageTable) || (in_array(strtolower($values['page_url']), $urlArray)  && $values['page_url'] != $oldPageUrl)) {
          $form->addError('This URL has been restricted by our automated system. Please choose another URL.');
          return;
        }
      }
      // END CHECK URL AVAILABILITY WORK
      // STORING MEMBER LEVELS
      if (!isset($_POST['level_id'])) {
        $page->level_id = '["0"]';
      } else {
        $page->level_id = Zend_Json_Encoder::encode($_POST['level_id']);
        if (strstr($page->level_id, '"0"')) {
          $page->level_id = '["0"]';
        }
      }

      // STORING MEMBER LEVELS
      if (!isset($_POST['networks'])) {
        $page->networks = '["0"]';
      } else {
        $page->networks = Zend_Json_Encoder::encode($_POST['networks']);
        if (strstr($page->networks, '"0"')) {
          $page->networks = '["0"]';
        }
      }
      // CREATE WIDGETIZED PAGE
      include APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/license/license2.php';
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'sitestaticpage', 'controller' => 'manage', 'action' => 'index'), 'admin_default', true);
  }

  public function deleteAction() {

    $page = Engine_Api::_()->getItem('sitestaticpage_page', $this->_getParam('staticpage_id'));
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->form = $form = new Sitestaticpage_Form_Admin_Manage_Delete();

    if (!$page) {
      $this->view->error = "Page entry doesn't exist or not authorized to delete";
      return;
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }
    $db = $page->getTable()->getAdapter();
    $db->beginTransaction();

    $staticpage_id = $this->_getParam('staticpage_id');
    $widgetizedpage_id = Engine_Api::_()->sitestaticpage()->getWidetizedpageId($staticpage_id);
    $mobilewidgetizedpage_id = Engine_Api::_()->sitestaticpage()->getMobileWidetizedpageId($staticpage_id);
    try {
      $enble_sitemobile = Engine_Api::_()->sitestaticpage()->isSitemobileEnabled();
      if ($enble_sitemobile) {
        if ($page->menu == 0) {
          $db->query('DELETE FROM engine4_sitemobile_menuitems WHERE name = "core_main_sitestaticpage_' . $staticpage_id . '"');
        } elseif ($page->menu == 1) {
          $db->query('DELETE FROM engine4_sitemobile_menuitems WHERE name = "core_mini_sitestaticpage_' . $staticpage_id . '"');
        } else {
          $db->query('DELETE FROM engine4_sitemobile_menuitems WHERE name = "core_footer_sitestaticpage_' . $staticpage_id . '"');
        }
        // DELETE WIDGETIZED PAGE AND CONTENT TABLE ENTRY
        if (!empty($mobilewidgetizedpage_id)) {
          $db->query('DELETE FROM engine4_sitemobile_content WHERE page_id = "' . $mobilewidgetizedpage_id . '"');
          $db->query('DELETE FROM engine4_sitemobile_pages WHERE name = "sitestaticpage_index_index_staticpageid_' . $staticpage_id . '"');
        }
        if (!empty($page->search)) {
          $db->query('DELETE FROM engine4_core_search WHERE id = "' . $staticpage_id . '"');
        }
      }
      // DELETE NAVIGATION MENU ENTRY
      if ($page->menu == 0) {
        $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_main_sitestaticpage_' . $staticpage_id . '"');
      } elseif ($page->menu == 1) {
        $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_mini_sitestaticpage_' . $staticpage_id . '"');
      } else {
        $db->query('DELETE FROM engine4_core_menuitems WHERE name = "core_footer_sitestaticpage_' . $staticpage_id . '"');
      }
      // DELETE WIDGETIZED PAGE AND CONTENT TABLE ENTRY
      if (!empty($widgetizedpage_id)) {
        $db->query('DELETE FROM engine4_core_content WHERE page_id = "' . $widgetizedpage_id . '"');
        $db->query('DELETE FROM engine4_core_pages WHERE name = "sitestaticpage_index_index_staticpageid_' . $staticpage_id . '"');
      }
      if (!empty($page->search)) {
        $db->query('DELETE FROM engine4_core_search WHERE id = "' . $staticpage_id . '"');
      }
      $page->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('Your page entry has been deleted.')
    ));
  }

  //COPY STATIC PAGE URL ACTION
  public function copyUrlAction() {

    $page = Engine_Api::_()->getItem('sitestaticpage_page', $this->_getParam('staticpage_id'));

    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->page_url = $page->page_url;
    $this->view->short_url = $page->short_url;
    $default_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.manifestUrl', 'static');
    $this->view->default_url = $default_url;
  }

  //ACTION FOR PAGE URL VALIDATION AT PAGE CREATION TIME
  public function pageurlvalidationAction() {

    $page_url = $this->_getParam('page_url');
    $urlArray = Engine_Api::_()->sitestaticpage()->getBannedUrls();
    if (empty($page_url)) {
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="./application/modules/Sitestaticpage/externals/images/cross.png"/>URL not valid.</span>'));
      exit();
    }

    $url_lenght = strlen($page_url);
    if ($url_lenght < 3) {
      $error_msg1 = "URL component should be atleast 3 characters long.";
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='./application/modules/Sitestaticpage/externals/images/cross.png'/>$error_msg1</span>"));
      exit();
    } elseif ($url_lenght > 255) {
      $error_msg2 = "URL component should be maximum 255 characters long.";
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='./application/modules/Sitestaticpage/externals/images/cross.png'/>$error_msg2</span>"));
      exit();
    }
    $check_url = $this->_getParam('check_url');
    if (!empty($check_url)) {
      $pageId = $this->_getParam('page_id');
      $page_id = Engine_Api::_()->sitestaticpage()->getPageId($page_url, $pageId);
    } else {
      $page_id = Engine_Api::_()->sitestaticpage()->getPageId($page_url, '');
    }

    if (!empty($page_id) || (in_array(strtolower($page_url), $urlArray))) {
      $error_msg3 = "URL not available.";
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='./application/modules/Sitestaticpage/externals/images/cross.png'/>$error_msg3</span>"));
      exit();
    }

    if (!preg_match("/^[a-zA-Z0-9-_]+$/", $page_url)) {
      $error_msg4 = "URL component can contain alphabets, numbers, underscores & dashes only.";
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='./application/modules/Sitestaticpage/externals/images/cross.png'/>$error_msg4</span>"));
      exit();
    } else {
      $error_msg5 = "URL Available!";
      echo Zend_Json::encode(array('success' => 1, 'success_msg' => "<span style='color:green;'><img src='./application/modules/Sitestaticpage/externals/images/tick.png'/>$error_msg5</span>"));
      exit();
    }
  }

  //ACTION FOR DISPLAY ALL FORMS
  public function formListAction() {

    $db = Engine_Db_Table::getDefaultAdapter();
    $information = $db->select()
            ->from('engine4_sitestaticpage_page_fields_options', array('option_id', 'label'))
            ->where('field_id = ?', 1)
            ->query()
            ->fetchAll();
    $optionids = $db->select()
            ->from('engine4_sitestaticpage_page_fields_maps', 'option_id')
            ->group(array('option_id'))
            ->query()
            ->fetchAll();

    $multioptions = array();
    foreach ($information as $info) {
      foreach ($optionids as $option_id) {
        if (in_array($info['option_id'], $option_id)) {
          $multioptions[$info['option_id']] = $info['label'];
        }
      }
    }
    $this->view->multioptions = $multioptions;
    $option_id = $this->_getParam('option_id');
    if (!empty($option_id)) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $this->view->form_label = $db->select()
              ->from('engine4_sitestaticpage_page_fields_options', array('label'))
              ->where('option_id = ?', $option_id)
              ->limit(1)
              ->query()
              ->fetchColumn();
      $this->view->form_id = $option_id;
    }
  }

  //ACTION FOR DISPLAY KEY FOR A PARTICULAR FORM
  public function formDataAction() {

    $db = Engine_Db_Table::getDefaultAdapter();
    $option_id = $this->_getParam('option_id');
    $page_id = Engine_Api::_()->sitestaticpage()->getPageId('', '', 1);
    $this->view->form_id = $option_id = $this->_getParam('option_id');
  }

  //ACTION FOR UPLOADING THE IMAGES
  public function uploadPhotoAction() {

    $this->_helper->layout->setLayout('default-simple');

    //GET THE DIRECTORY WHERE WE ARE UPLOADING THE PHOTOS
    $adminContactFile = APPLICATION_PATH . '/public/adminstaticpage';

    if (!is_dir($adminContactFile) && mkdir($adminContactFile, 0777, true)) {
      chmod($adminContactFile, 0777);
    }

    //GET THE PATH
    $path = realpath($adminContactFile);

    //PREPARE
    if (empty($_FILES['userfile'])) {
      $this->view->error = 'File failed to upload. Check your server settings (such as php.ini max_upload_filesize).';
      return;
    }

    //GET PHOTO INFORMATION
    $info = $_FILES['userfile'];

    //SET TARGET FILE
    $targetFile = $path . '/' . $info['name'];

    //TRY TO MOVE UPLOADED FILE
    if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
      $this->view->error = "Unable to move file to upload directory.";
      return;
    }

    //SEND THE STATUS TO THE TPl
    $this->view->status = true;

    //SEND THE PHOTO URL TO THE TPl
    $httpScheme = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) ? 'https://' : 'http://';
    $this->view->photo_url = $httpScheme . $_SERVER['HTTP_HOST'] . '/' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/adminstaticpage/' . $info['name'];
  }

  //FUNCTION FOR INSERITNG NAVIGATION MENU
  public function insertMenu($menu, $staticpage_id, $link_title, $order) {

    $db = Engine_Db_Table::getDefaultAdapter();
    $enble_sitemobile = Engine_Api::_()->sitestaticpage()->isSitemobileEnabled();
    if ($enble_sitemobile) {
      if ($menu == 0 || $menu == 1 || $menu == 2) {
        $menu_order = $db->select()
                  ->from('engine4_sitemobile_menuitems', 'order')
                  ->where('name = ?', "core_main_separator_settings")
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
        $menu_order -= 1;
        $db->query('INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES ("core_main_sitestaticpage_' . $staticpage_id . '", "sitestaticpage", "' . $link_title . '", \'Sitestaticpage_Plugin_Menus::mainMenu\', \'{"route":"sitestaticpage_index_index_staticpageid_' . $staticpage_id . ' ", "action":"index", "staticpage_id":"' . $staticpage_id . '"}\', "core_main", "" , "' . $menu_order . '", 1, 1)');
      }
    }
    if ($menu == 0) {
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `submenu`, `order`) VALUES ("core_main_sitestaticpage_' . $staticpage_id . '", "sitestaticpage", "' . $link_title . '", \'Sitestaticpage_Plugin_Menus::mainMenu\', \'{"route":"sitestaticpage_index_index_staticpageid_' . $staticpage_id . ' ", "action":"index", "staticpage_id":"' . $staticpage_id . '"}\', "core_main", "" , "' . ++$order . '" )');
    } elseif ($menu == 1) {
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `submenu`, `order`) VALUES ("core_mini_sitestaticpage_' . $staticpage_id . '", "sitestaticpage", "' . $link_title . '", \'Sitestaticpage_Plugin_Menus::miniMenu\', \'{"route":"sitestaticpage_index_index_staticpageid_' . $staticpage_id . ' ", "action":"index", "staticpage_id":"' . $staticpage_id . '"}\', "core_mini", "" , "' . ++$order . '" )');
    } else if ($menu == 2) {
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `submenu`, `order`) VALUES ("core_footer_sitestaticpage_' . $staticpage_id . '", "sitestaticpage", "' . $link_title . '", \'Sitestaticpage_Plugin_Menus::footerMenu\', \'{"route":"sitestaticpage_index_index_staticpageid_' . $staticpage_id . ' ", "action":"index", "staticpage_id":"' . $staticpage_id . '"}\', "core_footer", "" , "' . ++$order . '" )');
    }
  }

  public function createWidgetizePage($staticpage_id, $title, $pageTitle, $pageDescription, $keywords, $searchValue) {

    $db = Engine_Db_Table::getDefaultAdapter();
    $enble_sitemobile = Engine_Api::_()->sitestaticpage()->isSitemobileEnabled();
    if ($enble_sitemobile) {
      $db->insert('engine4_sitemobile_pages', array(
          'name' => "sitestaticpage_index_index_staticpageid_" . $staticpage_id,
          'displayname' => 'Static Page: ' . $title,
          'title' => $pageTitle,
          'description' => $pageDescription,
          'keywords' => $keywords,
          'search' => $searchValue,
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      //MAIN CONTAINER
      $db->insert('engine4_sitemobile_content', array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_container_id = $db->lastInsertId();

      //MAIN-MIDDLE CONTAINER
      $db->insert('engine4_sitemobile_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => 1,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert('engine4_sitemobile_content', array(
          'type' => 'widget',
          'name' => 'sitestaticpage.page-content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
    }

    $db->insert('engine4_core_pages', array(
        'name' => "sitestaticpage_index_index_staticpageid_" . $staticpage_id,
        'displayname' => 'Static Page: ' . $title,
        'title' => $pageTitle,
        'description' => $pageDescription,
        'keywords' => $keywords,
        'search' => $searchValue,
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();
    //MAIN CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
    ));
    $main_container_id = $db->lastInsertId();

    //MAIN-MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => 1,
    ));
    $main_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitestaticpage.page-content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
    ));
  }

}