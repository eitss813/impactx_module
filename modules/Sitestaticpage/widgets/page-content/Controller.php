<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Widget_PageContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $page_id = $this->_getParam('static_pages');
    if (!empty($page_id)) {
      $staticpage_id = $this->_getParam('static_pages');
    } else {
      $staticpage_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('staticpage_id', null);
    }
    
    if(empty($staticpage_id)){
        return $this->setNoRender();
    }
    
    $body_column = '';
    $sitestaticpage_page_content = Zend_Registry::isRegistered('sitestaticpage_page_content') ? Zend_Registry::get('sitestaticpage_page_content') : null;

    $getWidgetizedPageInfo = Engine_Api::_()->sitestaticpage()->getWidgetizedPageInfo();
    $multilanguage_support = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.multilanguage', 0);

    $page = Engine_Api::_()->getItem('sitestaticpage_page', $staticpage_id);
    if (empty($multilanguage_support)) {
      $embed_froms = unserialize($page->params);
    } else {
      $params_column = Engine_Api::_()->sitestaticpage()->getLanguageColumn('params');
      $body_column = Engine_Api::_()->sitestaticpage()->getLanguageColumn('body');
      //$params_column = 'params';
      $params_col = unserialize($page->$params_column);
      if (empty($params_col) && empty($page->$body_column)) {
        $embed_froms = unserialize($page->params);
      } else {
        $embed_froms = unserialize($page->$params_column);
      }
    }
    // GET CUSTOM FIELDS FORMS
    $form_ids = array();
    foreach ($embed_froms as $form) {
      $form = explode('_', $form);
      $form_ids[] = $form[1];
    }

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $front = Zend_Controller_Front::getInstance();
    $form_viewer_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.formsetting', 1);

    $this->view->form_ids = $form_ids;

    if (!empty($getWidgetizedPageInfo)) {
      if (count($form_ids) > 0) {
        foreach ($form_ids as $form_id) {
          $db = Engine_Db_Table::getDefaultAdapter();
          //$page_id = Engine_Api::_()->sitestaticpage()->getPageId('', '', 1);
          $subject = Engine_Api::_()->getItem('sitestaticpage_page', $staticpage_id);
          $this->view->topLevelId = 1;
          $this->view->topLevelValue = $form_id;
          $form = new Sitestaticpage_Form_Standard(array(
              'item' => $subject,
              'topLevelId' => 1,
              'topLevelValue' => $form_id,
              'userId' => $viewer_id,
          ));
          $form_label = $db->select()
                  ->from('engine4_sitestaticpage_page_fields_options', array('label'))
                  ->where('option_id = ?', $form_id)
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
          //$html_code = htmlspecialchars_decode(htmlentities($form));
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

    $count_forms = count($form_ids);
    if ($front->getRequest()->isPost() && !empty($count_forms)) {
      $this->view->profile_id = $_POST['profile_id'];
      if (in_array($_POST['profile_id'], $form_ids))
        include APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/CustomField.php';
    }
  }

}