<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Transactions.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Model_DbTable_Transactions extends Engine_Db_Table {

    protected $_rowClass = 'Sitegateway_Model_Transaction';

    public function getAllTransactions($params) {
        $transactionTableName = $this->info('name');
        $select = $this->select()->from($transactionTableName, '*');
        if (isset($params['project_id'])) {
            $backersTableName = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->info('name');

            $select = $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($backersTableName, "$backersTableName.backer_id = $transactionTableName.resource_id", null);

            $select = $select->where('resource_type = ?', $params['resource_type']);
            $select = $select->where('project_id = ?', $params['project_id']);
        }
        if (isset($params['gateway_id'])) {
            $select = $select->where("$transactionTableName.gateway_id = ?", $params['gateway_id']);
        }
        if (isset($params['gateway_payment_key'])) {
            $select = $select->where('gateway_payment_key is not null and gateway_payment_key<>?', '');
        }
        return $this->fetchAll($select);
    }

}
