<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_AdminManageController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_manage');

    //CREATE FORM FOR SEARCH
    $this->view->formFilter = $formFilter = new Sitemember_Form_Admin_Filter();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array('order' => 'user_id', 'order_direction' => 'DESC'), $values);

    $this->view->assign($values);

    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');
    
    $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
    $tableUserInfoName = $tableUserInfo->info('name');

    $select = $userTable->select()
            ->setIntegrityCheck(false)
            ->from($userTableName, array('*'))
            ->joinLeft($tableUserInfoName, "$userTableName.user_id = $tableUserInfoName.user_id", array('featured', 'sponsored', 'rating_avg', 'review_count'));

   $select->order((!empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if (!empty($values['displayname'])) {
      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
    }
    
    if (!empty($values['username'])) {
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }
    
    if (!empty($values['email'])) {
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }
    
    if (!empty($values['level_id'])) {
      $select->where('level_id = ?', $values['level_id']);
    }
    
    if (isset($values['enabled']) && $values['enabled'] != -1) {
      $select->where('enabled = ?', $values['enabled']);
    }
    
    if (isset($values['featured']) && $values['featured'] != -1) {
      $select->where($tableUserInfoName . '.featured = ?', $values['featured']);
    }
    
    if (isset($values['sponsored']) && $values['sponsored'] != -1) {
      $select->where($tableUserInfoName . '.sponsored = ?', $values['sponsored']);
    }

    $valuesCopy = array_filter($values);

    // RESET ENABLE BIT
    if (isset($values['enabled']) && $values['enabled'] == 0) {
      $valuesCopy['enabled'] = 0;
    }
    
    if (isset($values['featured']) && $values['featured'] == 0) {
      $valuesCopy['featured'] = 0;
    }
    
    if (isset($values['sponsored']) && $values['sponsored'] == 0) {
      $valuesCopy['sponsored'] = 0;
    }

    include APPLICATION_PATH . '/application/modules/Sitemember/controllers/license/license2.php';
  }

  //TO VIEW STATS OF USER
  public function statsAction() {

    $this->view->user = $user = Engine_Api::_()->getItem('user', $this->_getParam('id', null));
    $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
    $this->view->featured = $tableUserInfo->getColumnValue($this->_getParam('id', null), 'featured');
    $this->view->sponsored = $tableUserInfo->getColumnValue($this->_getParam('id', null), 'sponsored');

    $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);

    //FOR MEMBER TYPE OF USER
    if (!empty($fieldsByAlias['profile_type'])) {
      $optionId = $fieldsByAlias['profile_type']->getValue($user);
      if ($optionId) {
        $optionObj = Engine_Api::_()->fields()
                ->getFieldsOptions($user)
                ->getRowMatching('option_id', $optionId->value);
        if ($optionObj) {
          $this->view->memberType = $optionObj->label;
        }
      }
    }

    // NETWORKS
    $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($user)->where('hide = ?', 0);
    $this->view->networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);

    // FRIEND COUNT
    $this->view->friendCount = $user->membership()->getMemberCount($user);
    $this->view->likeCount = Engine_Api::_()->getApi('like', 'seaocore')->likeCount('user', $this->_getParam('id', null));
  }

  // MAKE FEATURED / UNFEATURED OF MEMBER ENTRY BY ADMIN
  public function featuredAction() {

    $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
    $user_id = $this->_getParam('id');
    $user_id_result = $tableUserInfo->getColumnValue($user_id, '*');
    $featured = $tableUserInfo->getColumnValue($user_id, 'featured');

    try {
      if (empty($user_id_result)) {
        $userinfo = $tableUserInfo->createRow();
        $userinfo->user_id = $user_id;
        $userinfo->featured = 1;
        $userinfo->save();
      } else {
        if (empty($featured)) {
          $tableUserInfo->update(array('featured' => 1), array('user_id = ?' => $user_id));
        } else {
          $tableUserInfo->update(array('featured' => 0), array('user_id = ?' => $user_id));
        }
      }
    } catch (Exception $e) {
      throw $e;
    }
    $this->_redirect('admin/sitemember/manage');
  }

  // MAKE SPONSERED / UNSPONSERED OF MEMBER ENTRY BY ADMIN
  public function sponsoredAction() {

    $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
    $user_id = $this->_getParam('id');
    $user_id_result = $tableUserInfo->getColumnValue($user_id, '*');
    $sponsored = $tableUserInfo->getColumnValue($user_id, 'sponsored');
    
    try {
      if (empty($user_id_result)) {
        $userinfo = $tableUserInfo->createRow();
        $userinfo->user_id = $user_id;
        $userinfo->sponsored = 1;
        $userinfo->save();
      } else {
        if (empty($sponsored)) {
          $tableUserInfo->update(array('sponsored' => 1), array('user_id = ?' => $user_id));
        } else {
          $tableUserInfo->update(array('sponsored' => 0), array('user_id = ?' => $user_id));
        }
      }
    } catch (Exception $e) {
      throw $e;
    }
    $this->_redirect('admin/sitemember/manage');
  }

}