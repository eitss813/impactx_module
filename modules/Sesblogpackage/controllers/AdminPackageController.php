<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: AdminPackageController.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblogpackage_AdminPackageController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_packagesetting');

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_packagesetting', array(), 'sesblog_admin_package');

    // Test curl support
    if (!function_exists('curl_version') ||
            !($info = curl_version())) {
      $this->view->error = $this->view->translate('The PHP extension cURL ' .
              'does not appear to be installed, which is required ' .
              'for interaction with payment gateways. Please contact your ' .
              'hosting provider.');
    }
    // Test curl ssl support
    else if (!($info['features'] & CURL_VERSION_SSL) ||
            !in_array('https', $info['protocols'])) {
      $this->view->error = $this->view->translate('The installed version of ' .
              'the cURL PHP extension does not support HTTPS, which is required ' .
              'for interaction with payment gateways. Please contact your ' .
              'hosting provider.');
    }
    // Check for enabled payment gateways
    else if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
      $this->view->error = $this->view->translate('There are currently no ' .
              'enabled payment gateways. You must %1$sadd one%2$s before this ' .
              'page is available.', '<a href="' .
              $this->view->escape($this->view->url(array('controller' => 'gateway', 'module' => 'payment'))) .
              '">', '</a>');
    }

    // Make form
    $this->view->formFilter = $formFilter = new Sesblogpackage_Form_Admin_Package_Filter();

    // Process form
    if ($formFilter->isValid($this->_getAllParams())) {
      if (null === $this->_getParam('enabled')) {
        
      }
      $filterValues = $formFilter->getValues();
    }
    if (empty($filterValues['order'])) {
      $filterValues['order'] = 'package_id';
    }
    if (empty($filterValues['direction'])) {
      $filterValues['order'] = 'order';
      $filterValues['direction'] = 'DESC';
    }
    $this->view->filterValues = $filterValues;
    $this->view->order = $filterValues['order'];
    $this->view->direction = $filterValues['direction'];

    // Initialize select
    $table = Engine_Api::_()->getDbtable('packages', 'sesblogpackage');
    $select = $table->select();

    // Add filter values
    if (!empty($filterValues['query'])) {
      $select->where('title LIKE ?', '%' . $filterValues['query'] . '%');
    }

    if (isset($filterValues['enabled']) && '' != $filterValues['enabled']) {
      $select->where('enabled = ?', $filterValues['enabled']);
    }

    if (!empty($filterValues['order'])) {
      if (empty($filterValues['direction'])) {
        $filterValues['direction'] = 'DESC';
      }
      $select->order($filterValues['order'] . ' ' . $filterValues['direction']);
    }

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Get blog totals for each plan
    $blogCounts = array();
    foreach ($paginator as $item) {
      $blogCounts[$item->package_id] = Engine_Api::_()->getDbtable('blogs', 'sesblog')
              ->select()
              ->from('engine4_sesblog_blogs', new Zend_Db_Expr('COUNT(*)'))
              ->where('package_id = ?', $item->package_id)
              ->where('is_approved = ?', true)
              ->query()
              ->fetchColumn();
    }
    $this->view->blogCounts = $blogCounts;
  }

  public function settingsAction() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_packagesetting');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_packagesetting', array(), 'sesblog_admin_subpackagesetting');

    // Make form
    $this->view->form = $form = new Sesblogpackage_Form_Admin_Package_Settings();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      unset($values['commision']);
      include_once APPLICATION_PATH . "/application/modules/Sesblogpackage/controllers/License.php";
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.pluginactivated')) {
        foreach ($values as $key => $value) {
          if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key))
            Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
        if ($error)
          $this->_helper->redirector->gotoRoute(array());
      }
    }
  }

  public function createAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_packagesetting');

    $customFields = array();
    if (count($_POST) && isset($_POST['custom_fields']) && $_POST['custom_fields'] == 2) {
      foreach ($_POST as $key => $customValues) {
        $val = explode('_', $key);
        if (count($val) < 3 || !is_numeric($val[0]) || !is_numeric($val[1]) || !is_numeric($val[2])) {
          continue;
        }
        $customFields[] = $key;
      }
    }
    $this->view->customFields = $customFields;

    // Make form
    $this->view->form = $form = new Sesblogpackage_Form_Admin_Package_Create(array('customFields' => $customFields));

    // Get supported billing cycles
    $gateways = array();
    $supportedBillingCycles = array();
    $partiallySupportedBillingCycles = array();
    $fullySupportedBillingCycles = null;
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    foreach ($gatewaysTable->fetchAll() as $gateway) {
      $gateways[$gateway->gateway_id] = $gateway;
      $supportedBillingCycles[$gateway->gateway_id] = $gateway->getGateway()->getSupportedBillingCycles();
      $partiallySupportedBillingCycles = array_merge($partiallySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
      if (null === $fullySupportedBillingCycles) {
        $fullySupportedBillingCycles = $supportedBillingCycles[$gateway->gateway_id];
      } else {
        $fullySupportedBillingCycles = array_intersect($fullySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
      }
    }
    $partiallySupportedBillingCycles = array_diff($partiallySupportedBillingCycles, $fullySupportedBillingCycles);

    $multiOptions = array_combine(array_map('strtolower', $fullySupportedBillingCycles), $fullySupportedBillingCycles);
    // $multiOptions = array_merge(array('day' => "Day"), $multiOptions);
    $form->getElement('recurrence')
            ->setMultiOptions($multiOptions)
    ;
    $form->getElement('recurrence')->options['forever'] = 'One-time';

    $form->getElement('duration')
            ->setMultiOptions($multiOptions)
    ;
    $form->getElement('duration')->options['forever'] = 'Forever';

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $values = $form->getValues();
    // Process


    if ($values['price'] > 0) {
      //check gateway enable
      if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
        $form->getDecorator('errors')->setOption('escape', false);
        $gatewayError = $this->view->translate('You have not enabled any payment gateway yet. Please %1$senable payment gateways%2$s  before creating a paid package.', '<a href="' . $this->view->baseUrl() . '/admin/payment/gateway" ' . " target='_blank'" . '">', '</a>');
        return $form->addError($gatewayError);
      }
    }

    $tmp = $values['recurrence'];
    unset($values['recurrence']);
    if (empty($tmp) || !is_array($tmp)) {
      $tmp = array(null, null);
    }
    $values['recurrence'] = (int) $tmp[0];
    $values['recurrence_type'] = $tmp[1];
    $tmp = $values['duration'];
    unset($values['duration']);
    if (empty($tmp) || !is_array($tmp)) {
      $tmp = array(null, null);
    }
    $values['duration'] = (int) $tmp[0];
    $values['duration_type'] = $tmp[1];
    $packageTable = Engine_Api::_()->getDbtable('packages', 'sesblogpackage');
    $db = $packageTable->getAdapter();
    $db->beginTransaction();

    try {
      // Create package
      $values['custom_fields_params'] = json_encode($customFields);
      $package = $packageTable->createRow();

      $params['upload_mainphoto'] = $values['upload_mainphoto'];
//       $params['blog_choose_style'] = $values['blog_choose_style'];
//       if ($values['blog_choose_style'] && isset($values['blog_chooselayout']))
//         $params['blog_chooselayout'] = $values['blog_chooselayout'];
//       else
//         $params['blog_style_type'] = $values['blog_style_type'];

      $params['blog_approve'] = $values['blog_approve'];
      $params['blog_featured'] = $values['blog_featured'];
      $params['blog_sponsored'] = $values['blog_sponsored'];
      $params['blog_verified'] = $values['blog_verified'];

      $params['blog_seo'] = $values['blog_seo'];
      $params['blog_contactinfo'] = $values['blog_contactinfo'];
      $params['custom_fields'] = $values['custom_fields'];

      $values['params'] = json_encode($params);
      $values['member_level'] = ',' . implode(',', $values['member_level']) . ',';
      $package->setFromArray($values);
      $package->save();
      $package->order = $package->getIdentity();
      $package->save();
      // Create package in gateways?
      if (!$package->isFree()) {
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
          $gatewayPlugin = $gateway->getGateway();
          // Check billing cycle support
          if (!$package->isOneTime()) {
            $sbc = $gateway->getGateway()->getSupportedBillingCycles();
            if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
              continue;
            }
          }
          if (method_exists($gatewayPlugin, 'createProduct')) {
            $gatewayPlugin->createProduct($package->getGatewayParams());
          }
        }
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    // Redirect
    return $this->_helper->redirector->gotoRoute(array('module' => 'sesblogpackage', 'controller' => 'package'), "admin_default", true);
  }

  public function editAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_packagesetting');

    // Get package
    if (null === ($packageIdentity = $this->_getParam('package_id')) ||
            !($package = Engine_Api::_()->getDbtable('packages', 'sesblogpackage')->find($packageIdentity)->current())) {
      throw new Engine_Exception('No package found');
    }
    $customFields = array();
    if (count($_POST) && isset($_POST['custom_fields']) && $_POST['custom_fields'] == 2) {
      foreach ($_POST as $key => $customValues) {
        $val = explode('_', $key);
        if (count($val) < 3 || !is_numeric($val[0]) || !is_numeric($val[1]) || !is_numeric($val[2])) {
          continue;
        }
        $customFields[] = $key;
      }
    } else {
      $fields = json_decode($package->custom_fields_params, true);
      if (count($fields)) {
        foreach ($fields as $customValues) {
          $val = explode('_', $customValues);
          if (count($val) < 3 || !is_numeric($val[0]) || !is_numeric($val[1]) || !is_numeric($val[2])) {
            continue;
          }
          $customFields[] = $customValues;
        }
      }
    }
    $this->view->customFields = $customFields;
    // Make form
    $this->view->form = $form = new Sesblogpackage_Form_Admin_Package_Edit(array('customFields' => $customFields));

    // Populate form
    $values = $package->toArray();

    $values['recurrence'] = array($values['recurrence'], $values['recurrence_type']);
    $values['duration'] = array($values['duration'], $values['duration_type']);

    unset($values['recurrence_type']);
    unset($values['duration_type']);

    $params = json_decode($values['params'], true);
    $values = array_merge($values, $params);
    $values['member_level'] = explode(',', $package->member_level);
    $otherValues = array(
        'price' => $values['price'],
        'recurrence' => $values['recurrence'],
        'duration' => $values['duration'],
    );

    $form->populate($values);

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Hack em up
    $form->populate($otherValues);

    // Process
    $values = $form->getValues();
    $values['custom_fields_params'] = json_encode($customFields);
    unset($values['price']);
    unset($values['recurrence']);
    unset($values['recurrence_type']);
    unset($values['duration']);
    unset($values['duration_type']);
    unset($values['is_renew_link']);
    unset($values['renew_link_days']);

    $packageTable = Engine_Api::_()->getDbtable('packages', 'sesblogpackage');
    $db = $packageTable->getAdapter();
    $db->beginTransaction();

    try {


      if (isset($values['upload_mainphoto']))
        $params['upload_mainphoto'] = $values['upload_mainphoto'];
//       if (isset($values['blog_choose_style']))
//         $params['blog_choose_style'] = $values['blog_choose_style'];
// 
//       if ($values['blog_choose_style'] && isset($values['blog_chooselayout']))
//         $params['blog_chooselayout'] = $values['blog_chooselayout'];
//       else
//         $params['blog_style_type'] = $values['blog_style_type'];

      if (isset($values['blog_approve']))
        $params['blog_approve'] = $values['blog_approve'];
      if (isset($values['blog_featured']))
        $params['blog_featured'] = $values['blog_featured'];
      if (isset($values['blog_sponsored']))
        $params['blog_sponsored'] = $values['blog_sponsored'];
      if (isset($values['blog_verified']))
        $params['blog_verified'] = $values['blog_verified'];

      if (isset($values['blog_seo']))
        $params['blog_seo'] = $values['blog_seo'];
      if (isset($values['blog_contactinfo']))
        $params['blog_contactinfo'] = $values['blog_contactinfo'];
        
      $params['custom_fields'] = $values['custom_fields'];
      $values['member_level'] = ',' . implode(',', $values['member_level']) . ',';

      $values['params'] = json_encode($params);

      $package->setFromArray($values);
      $package->save();

      // Update package
      $package->setFromArray($values);
      $package->save();

      // Create package in gateways?
      if (!$package->isFree()) {
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
          $gatewayPlugin = $gateway->getGateway();
          // Check billing cycle support
          if (!$package->isOneTime()) {
            $sbc = $gateway->getGateway()->getSupportedBillingCycles();
            if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
              continue;
            }
          }
          if (!method_exists($gatewayPlugin, 'createProduct') ||
                  !method_exists($gatewayPlugin, 'editProduct') ||
                  !method_exists($gatewayPlugin, 'detailVendorProduct')) {
            continue;
          }
          // If it throws an exception, or returns empty, assume it doesn't exist?
          try {
            $info = $gatewayPlugin->detailVendorProduct($package->getGatewayIdentity());
          } catch (Exception $e) {
            $info = false;
          }
          // Create
          if (!$info) {
            $gatewayPlugin->createProduct($package->getGatewayParams());
          }
          // Edit
          else {
            $gatewayPlugin->editProduct($package->getGatewayIdentity(), $package->getGatewayParams());
          }
        }
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // Redirect
    return $this->_helper->redirector->gotoRoute(array('module' => 'sesblogpackage', 'controller' => 'package'), "admin_default", true);
  }

  public function orderAction() {
    if (!$this->getRequest()->isPost())
      return;
    $packageTable = Engine_Api::_()->getDbtable('packages', 'sesblogpackage');
    $packages = $packageTable->fetchAll($packageTable->select());
    foreach ($packages as $package) {
      $order = $this->getRequest()->getParam('package_' . $package->package_id);
      if (!$order)
        $order = 999;
      $package->order = $order;
      $package->save();
    }
    return;
  }

  public function deleteAction() {
    // Get package
    if (null === ($packageIdentity = $this->_getParam('package_id')) ||
            !($package = Engine_Api::_()->getDbtable('packages', 'sesblogpackage')->find($packageIdentity)->current())) {
      throw new Engine_Exception('No package found');
    }
    $this->view->form = $form = new Sesblogpackage_Form_Admin_Package_Delete();
    // Check method/data
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    // Process
    $packageTable = Engine_Api::_()->getDbtable('packages', 'sesblogpackage');
    $db = $packageTable->getAdapter();
    $db->beginTransaction();
    try {
      // Delete package
      $package->delete();
      
      //Delete Other Entries
      $packageId = $package->getIdentity();

      Engine_Api::_()->getDbtable('blogs', 'sesblog')->update(array('is_approved' => 0), array('package_id =?' => $packageId));

      $transactionTable = Engine_Api::_()->getDbTable('transactions', 'sesblogpackage');
      $select = $transactionTable->select()
              ->from($transactionTable->info('name'))
              ->where('package_id =?', $packageId);
              //->where('owner_id =?', $viewerId);
      $transaction = $transactionTable->fetchRow($select);
      if (!empty($transaction->gateway_id) && !empty($transaction->gateway_profile_id) && empty($transaction->gateway_transaction_id)) {
        try {
          $gateway = Engine_Api::_()->getItem('sesblogpackage_gateway', $transaction->gateway_id);
          if ($gateway) {
            $gatewayPlugin = $gateway->getPlugin();
            if (method_exists($gatewayPlugin, 'cancelBlog')) {
              $gatewayPlugin->cancelBlog($transaction->gateway_profile_id);
            }
          }
        } catch (Exception $e) {
          // Silence?
        }
      }
      //Start Delete package Related Data
      Engine_Api::_()->getDbtable('orderspackages', 'sesblogpackage')->delete(array('package_id =?' => $packageId));
      Engine_Api::_()->getDbtable('transactions', 'sesblogpackage')->delete(array('package_id =?' => $packageId));
      //End Work of Package Related Data

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Package deleted successfully.');
    return $this->_forward('success', 'utility', 'core', array(
                'parentRefresh' => true,
                'messages' => Array($this->view->message)
    ));
  }

  //Approved Action
  public function highlightAction() {
    $package_id = $this->_getParam('package_id');
    if (!empty($package_id)) {
      $package = Engine_Api::_()->getItem('sesblogpackage_package', $package_id);
      $package->highlight = !$package->highlight;
      $package->save();
    }
    $this->_redirect($_SERVER['HTTP_REFERER']);
  }

  //Approved Action
  public function showUpgradeAction() {
    $package_id = $this->_getParam('package_id');
    if (!empty($package_id)) {
      $package = Engine_Api::_()->getItem('sesblogpackage_package', $package_id);
      $package->show_upgrade = !$package->show_upgrade;
      $package->save();
    }
    $this->_redirect($_SERVER['HTTP_REFERER']);
  }

  //Approved Action
  public function approvedAction() {
    $package_id = $this->_getParam('package_id');
    if (!empty($package_id)) {
      $package = Engine_Api::_()->getItem('sesblogpackage_package', $package_id);
      $package->enabled = !$package->enabled;
      $package->save();
    }
    $this->_redirect($_SERVER['HTTP_REFERER']);
  }

  public function manageTransactionAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_packagesetting');

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_packagesetting', array(), 'sesblogpackage_admin_main_transaction');

    $this->view->formFilter = $formFilter = new Sesblogpackage_Form_Admin_Filter();

    $values = array();
    if ($formFilter->isValid($this->_getAllParams()))
      $values = $formFilter->getValues();
    $values = array_merge(array(
        'order' => isset($_GET['order']) ? $_GET['order'] : '',
        'order_direction' => isset($_GET['order_direction']) ? $_GET['order_direction'] : '',
            ), $values);
    $this->view->assign($values);

    $tableTransaction = Engine_Api::_()->getItemTable('sesblogpackage_transaction');
    $tableTransactionName = $tableTransaction->info('name');
    $blogTable = Engine_Api::_()->getDbTable('blogs', 'sesblog');
    $blogTableName = $blogTable->info('name');
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    $select = $tableTransaction->select()
            ->setIntegrityCheck(false)
            ->from($tableTransactionName)
            ->joinLeft($tableUserName, "$tableTransactionName.owner_id = $tableUserName.user_id", 'username')
            ->where($tableUserName . '.user_id IS NOT NULL')
            ->joinLeft($blogTableName, "$tableTransactionName.transaction_id = $blogTableName.transaction_id", 'blog_id')
            ->where($blogTableName . '.blog_id IS NOT NULL')
            ->order((!empty($_GET['order']) ? $_GET['order'] : 'transaction_id' ) . ' ' . (!empty($_GET['order_direction']) ? $_GET['order_direction'] : 'DESC' ));

    if (!empty($_GET['title'])) {
      $select
              ->where('(' . $tableTransactionName . '.gateway_transaction_id LIKE ? || ' .
                      $tableTransactionName . '.gateway_profile_id LIKE ? || ' .
                      'title LIKE ? || ' .
                      'displayname LIKE ? || username LIKE ? || ' .
                      $tableUserName . '.email LIKE ?)', '%' . $_GET['title'] . '%');
    }

    if (!empty($_GET['gateway_id']))
      $select->where($tableTransactionName . '.gateway_id LIKE ?', '%' . $_GET['gateway_id'] . '%');

    if (!empty($_GET['gateway_type']))
      $select->where($tableTransactionName . '.gateway_type LIKE ?', '%' . $_GET['gateway_type'] . '%');


    $urlParams = array();
    foreach (Zend_Controller_Front::getInstance()->getRequest()->getParams() as $urlParamsKey => $urlParamsVal) {
      if ($urlParamsKey == 'module' || $urlParamsKey == 'controller' || $urlParamsKey == 'action' || $urlParamsKey == 'rewrite')
        continue;
      $urlParams['query'][$urlParamsKey] = $urlParamsVal;
    }
    $this->view->urlParams = $urlParams;
    $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator;
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function detailAction() {
    if (!($transaction_id = $this->_getParam('transaction_id')) ||
            !($transaction = Engine_Api::_()->getItem('sesblogpackage_transaction', $transaction_id))) {
      return;
    }

    $this->view->transaction = $transaction;
    $this->view->gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
    $this->view->order = Engine_Api::_()->getItem('payment_order', $transaction->order_id);
    $this->view->item = Engine_Api::_()->getItem('sesblog_blog', $this->_getParam('blog_id'));
    $this->view->user = Engine_Api::_()->getItem('user', $transaction->owner_id);
  }

}
