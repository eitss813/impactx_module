<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Packages.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblogpackage_Model_DbTable_Packages extends Engine_Db_Table {

  protected $_rowClass = 'Sesblogpackage_Model_Package';

  public function getEnabledPackageCount() {
    return $this->select()
                    ->from($this, new Zend_Db_Expr('COUNT(*)'))
                    ->where('enabled = ?', 1)
                    ->where('`default` =?', 0)
                    ->query()
                    ->fetchColumn()
    ;
  }

  public function getDefaultPackage() {
    return $this->select()
                    ->from($this, 'package_id')
                    ->where('`default` =?', 1)
                    ->query()
                    ->fetchColumn()
    ;
  }

  public function getPackage($params = array()) {
    $tablename = $this->info('name');
    $select = $this->select()->from($tablename);
    if (empty($params['default']))
      $select->where('`default` =?', 0);
    if (empty($params['enabled']))
      $select->where('enabled =?', 1);
    if (isset($params['member_level']))
      $select->where('member_level LIKE "%,0,%" || member_level LIKE "%,' . $params['member_level'] . ',%" ');
    if (isset($params['show_upgrade']))
      $select->where('show_upgrade =?', 1);
    if (isset($params['not_in_id']))
      $select->where('package_id !=?', $params['not_in_id']);
    if (isset($params['package_id']))
      $select->where('package_id =?', $params['package_id']);
    if (isset($params['price']))
      $select->where('price >?', $params['price']);
    $select->order('order ASC');
    $select->where('enabled =?', 1);
    return $this->fetchAll($select);
  }

  public function getEnabledNonFreePackageCount() {
    return $this->select()
                    ->from($this, new Zend_Db_Expr('COUNT(*)'))
                    ->where('enabled = ?', 1)
                    ->where('price > ?', 0)
                    ->where('`default` =?', 0)
                    ->query()
                    ->fetchColumn()
    ;
  }

  public function cancelSubscription($params = array()) {
    $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $packageId = $params['package_id'];
    //Start Delete package Related Data
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query("DELETE FROM engine4_sesblogpackage_orderspackages WHERE package_id = $packageId && owner_id = " . $viewerId);
    $db->query("DELETE FROM engine4_sesblogpackage_transactions WHERE package_id = $packageId && owner_id = " . $viewerId);
    //End Work of Package Related Data
    
    Engine_Api::_()->getDbtable('blogs', 'sesblog')->update(array('is_approved' => 0), array('package_id =?' => $packageId, 'owner_id =?' => $viewerId));

    $transactionTable = Engine_Api::_()->getDbTable('transactions', 'sesblogpackage');
    $select = $transactionTable->select()
            ->from($transactionTable->info('name'))
            ->where('package_id =?', $packageId)
            ->where('owner_id =?', $viewerId);
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
  }

}
