<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_IndexController extends Seaocore_Controller_Action_Standard {

  public function indexAction() {

    $staticpage_id = $this->_getParam('staticpage_id');
    $page = Engine_Api::_()->getItem('sitestaticpage_page', $staticpage_id);
    Engine_Api::_()->core()->setSubject($page);
    $widgetizedpage_id = Engine_Api::_()->sitestaticpage()->getWidetizedpageId($staticpage_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $body_column = '';
    $form_ids = array();
    $page->level_id = Zend_Json_Decoder::decode($page->level_id);
    $page->networks = Zend_Json_Decoder::decode($page->networks);
    $multilanguage_support = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.multilanguage', 0);
    $form_viewer_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.formsetting', 1);
    
    if (!in_array(0, $page->level_id)) {
      if (!empty($viewer_id)) {
        if (!in_array($viewer->level_id, $page->level_id)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      } else {
        if (!in_array(5, $page->level_id)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      }
    }

    if (!in_array(0, $page->networks)) {
      $flag = $page->isViewableByNetwork();
      if (empty($flag))
        return $this->_forward('requireauth', 'error', 'core');
    }

    $sitestaticpage_page_widgets = Zend_Registry::isRegistered('sitestaticpage_page_widgets') ? Zend_Registry::get('sitestaticpage_page_widgets') : null;
    $default_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.manifestUrl', 'static');

    if (empty($sitestaticpage_page_widgets))
      return $this->_forward('requireauth', 'error', 'core');
    if (!empty($widgetizedpage_id)) {
      $this->_helper->content->setContentName("sitestaticpage_index_index_staticpageid_$staticpage_id")->setNoRender()->setEnabled();
    } else {
      $getWidgetizedPageInfo = Engine_Api::_()->sitestaticpage()->getWidgetizedPageInfo();
      $page->meta_info = unserialize($page->meta_info);
      Engine_Api::_()->sitestaticpage()->setMetaTitles($page->meta_info);
      Engine_Api::_()->sitestaticpage()->setMetaDescriptionsBrowse($page->meta_info);
      Engine_Api::_()->sitestaticpage()->setMetaKeywords($page->meta_info);
      $page = Engine_Api::_()->getItem('sitestaticpage_page', $staticpage_id);
      if (empty($multilanguage_support)) {
        $embed_froms = unserialize($page->params);
      } else {
        $params_column = Engine_Api::_()->sitestaticpage()->getLanguageColumn('params');
        $body_column = Engine_Api::_()->sitestaticpage()->getLanguageColumn('body');
        $params_col = unserialize($page->$params_column);
        if (empty($params_col) && empty($page->$body_column)) 
          $embed_froms = unserialize($page->params);
        else
          $embed_froms = unserialize($page->$params_column);
      }
      foreach ($embed_froms as $form) {
        $form = explode('_', $form);
        $form_ids[] = $form[1];
      }
      $this->view->form_ids = $form_ids;
      
      if (!empty($getWidgetizedPageInfo)) {
        if (count($form_ids) > 0) {
          foreach ($form_ids as $form_id) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $subject = Engine_Api::_()->getItem('sitestaticpage_page', $staticpage_id);
            $this->view->topLevelId = 1;
            $this->view->topLevelValue = $form_id;
            $form = new Sitestaticpage_Form_Standard(array(
                'item' => $subject,
                'topLevelId' => 1,
                'topLevelValue' => $form_id,
                'userId' => $viewer_id,
            ));
//            $html_code = htmlspecialchars_decode(htmlentities($form));
            $html_code = $form;
            if (empty($multilanguage_support)) {
              if (!($viewer_id) && empty($form_viewer_setting))
                $page->body = str_replace('[static_form_' . $form_id . ']', '', $page->body);
              else
                $page->body = str_replace('[static_form_' . $form_id . ']', $html_code, $page->body);
            } else {
              $body_column = Engine_Api::_()->sitestaticpage()->getLanguageColumn('body');
              //$body_column = 'body';
              if (empty($page->$body_column)) {
                if (!($viewer_id) && empty($form_viewer_setting))
                  $page->body = str_replace('[static_form_' . $form_id . ']', '', $page->body);
                else
                  $page->body = str_replace('[static_form_' . $form_id . ']', $html_code, $page->body);
              } else {
                if (!($viewer_id) && empty($form_viewer_setting))
                  $page->$body_column = str_replace('[static_form_' . $form_id . ']', '', $page->$body_column);
                else
                  $page->$body_column = str_replace('[static_form_' . $form_id . ']', $html_code, $page->$body_column);
              }
            }
          }
        } else {
          if (empty($multilanguage_support)) {
            $page->body = $page->body;
          } else {
            $body_column = Engine_Api::_()->sitestaticpage()->getLanguageColumn('body');
            //$body_column = 'body';
            if (empty($page->$body_column)) {
              $page->body = $page->body;
            } else {
              $page->$body_column = $page->$body_column;
            }
          }
        }
      }
      if (!empty($page->$body_column))
        echo '<div class="stpage_cont_body">' . $page->$body_column . '</div>';
      else
        echo '<div class="stpage_cont_body">' . $page->body . '</div>';
    }

    //INCREMENT Page VIEWS IF VIEWER IS NOT OWNER
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');
    if (($page->owner_id != $viewer_id)) {
      $pageTable->update(array(
          'view_count' => new Zend_Db_Expr('view_count + 1'),
              ), array(
          'page_id = ?' => $staticpage_id,
      ));
    }
    
    $count_forms = count($form_ids);
    if (empty($widgetizedpage_id) && $this->getRequest()->isPost() && !empty($count_forms)) {
      $this->view->profile_id = $_POST['profile_id'];
      if (in_array($_POST['profile_id'], $form_ids)) {
        include_once APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/CustomField.php';
      }
    }
  }
  
  public function editAction() {

    $staticpage_id = $this->_getParam('item_id');
    $member_id = $this->_getParam('member_id');
    $form_id = $this->_getParam('form_id');

    if (empty($staticpage_id) || empty($member_id) || empty($form_id))
      return;

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $subject = Engine_Api::_()->getItem('sitestaticpage_page', $staticpage_id);
    $this->view->topLevelId = 1;
    $this->view->topLevelValue = $form_id;
    $this->view->form = $form = new Sitestaticpage_Form_Standard(array(
        'item' => $subject,
        'topLevelId' => 1,
        'topLevelValue' => $form_id,
        'userId' => $viewer_id,
    ));
    if ($this->getRequest()->isPost()) {
      include_once APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/CustomField.php';
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Your form data has been saved successfully.')
      ));
    }
  }
  
  public function deleteAction() {

    $this->_helper->layout->setLayout('default-simple');
    $staticpage_id = $this->_getParam('item_id');
    $member_id = $this->_getParam('member_id');
    $form_id = $this->_getParam('form_id');

    if (empty($staticpage_id) || empty($member_id) || empty($form_id))
      return;

    if ($this->getRequest()->isPost()) {
      $table_values = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'values');

      $table_values->delete(array('item_id=?' => $staticpage_id, 'member_id=?' => $member_id, 'form_id=?' => $form_id));

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Your form data has been deleted.')
      ));
    }
  }

}