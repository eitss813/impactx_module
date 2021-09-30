<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Plugin_Menus {

  public function mainMenu($row) {
    $staticpage_id = $row->params['staticpage_id'];
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (!empty($staticpage_id)) {
      $page = Engine_Api::_()->getItem('sitestaticpage_page', $staticpage_id);
      if ($page) {
        $page->level_id = Zend_Json_Decoder::decode($page->level_id);
        $page->networks = Zend_Json_Decoder::decode($page->networks);

        $return_array = '';
        if (in_array(0, $page->level_id)) {
          $return_array = array(
              'label' => $row->label,
              'route' => 'sitestaticpage_index_index_staticpageid_' . $staticpage_id . '',
              'action' => 'index',
              'params' => array(
                  'staticpage_id' => $staticpage_id,
              ),
          );
        } else if ((empty($viewer_id) && in_array(5, $page->level_id)) || (in_array(Engine_Api::_()->user()->getViewer()->level_id, $page->level_id))) {

          $return_array = array(
              'label' => $row->label,
              'route' => 'sitestaticpage_index_index_staticpageid_' . $staticpage_id . '',
              'action' => 'index',
              'params' => array(
                  'staticpage_id' => $staticpage_id,
              ),
          );
        } else {
          $return_array = false;
        }
        if (!in_array(0, $page->networks)) {
          $flag = $page->isViewableByNetwork();
          if (empty($flag))
            $return_array = false;
        }
        return $return_array;
      }
      else
        return;
    }
  }

  public function miniMenu($row) {

    $staticpage_id = $row->params['staticpage_id'];
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (!empty($staticpage_id)) {
      $page = Engine_Api::_()->getItem('sitestaticpage_page', $staticpage_id);
      if ($page) {
        $page->level_id = Zend_Json_Decoder::decode($page->level_id);
        $page->networks = Zend_Json_Decoder::decode($page->networks);

        $return_array = '';
        if (in_array(0, $page->level_id)) {
          $return_array = array(
              'label' => $row->label,
              'route' => 'sitestaticpage_index_index_staticpageid_' . $staticpage_id . '',
              'action' => 'index',
              'params' => array(
                  'staticpage_id' => $staticpage_id,
              ),
          );
        } else if ((empty($viewer_id) && in_array(5, $page->level_id)) || (in_array(Engine_Api::_()->user()->getViewer()->level_id, $page->level_id))) {

          $return_array = array(
              'label' => $row->label,
              'route' => 'sitestaticpage_index_index_staticpageid_' . $staticpage_id . '',
              'action' => 'index',
              'params' => array(
                  'staticpage_id' => $staticpage_id,
              ),
          );
        } else {
          $return_array = false;
        }
        if (!in_array(0, $page->networks)) {
          $flag = $page->isViewableByNetwork();
          if (empty($flag))
            $return_array = false;
        }
        return $return_array;
      }
      else
        return;
    }
  }

  public function footerMenu($row) {

    $staticpage_id = $row->params['staticpage_id'];
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (!empty($staticpage_id)) {
      $page = Engine_Api::_()->getItem('sitestaticpage_page', $staticpage_id);
      if ($page) {
        $page->level_id = Zend_Json_Decoder::decode($page->level_id);
        $page->networks = Zend_Json_Decoder::decode($page->networks);

        $return_array = '';
        if (in_array(0, $page->level_id)) {
          $return_array = array(
              'label' => $row->label,
              'route' => 'sitestaticpage_index_index_staticpageid_' . $staticpage_id . '',
              'action' => 'index',
              'params' => array(
                  'staticpage_id' => $staticpage_id,
              ),
          );
        } else if ((empty($viewer_id) && in_array(5, $page->level_id)) || (in_array(Engine_Api::_()->user()->getViewer()->level_id, $page->level_id))) {

          $return_array = array(
              'label' => $row->label,
              'route' => 'sitestaticpage_index_index_staticpageid_' . $staticpage_id . '',
              'action' => 'index',
              'params' => array(
                  'staticpage_id' => $staticpage_id,
              ),
          );
        } else {
          $return_array = false;
        }
        if (!in_array(0, $page->networks)) {
          $flag = $page->isViewableByNetwork();
          if (empty($flag))
            $return_array = false;
        }
        return $return_array;
      }
      else
        return;
    }
  }

}