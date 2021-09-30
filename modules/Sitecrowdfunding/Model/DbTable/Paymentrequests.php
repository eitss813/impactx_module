<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Paymentrequests.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Paymentrequests extends Engine_Db_Table {

    protected $_name = 'sitecrowdfunding_payment_requests';
    protected $_rowClass = 'Sitecrowdfunding_Model_Paymentrequest';

    /**
     * Return project payment request object
     *
     * @param array $params
     * @return object
     */
    public function getProjectPaymentRequestPaginator($params = array()) {

        $paginator = Zend_Paginator::factory($this->getProjectPaymentRequestSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getProjectPaymentRequestSelect($params) {
        $select = $this->select()->where('project_id =?', $params['project_id'])->group('request_id');

        if (isset($params['search'])) {
            if (!empty($params['request_date']))
                $select->where("CAST(request_date AS DATE) =?", trim($params['request_date']));

            if (!empty($params['response_date']))
                $select->where("CAST(response_date AS DATE) =?", trim($params['response_date']));

            $api = Engine_Api::_()->sitecrowdfunding();

            if (!empty($params['request_min_amount']))
                $select->where("request_amount >=?", $api->getPriceWithCurrency(trim($params['request_min_amount']), 1, 1)); 

            if (!empty($params['request_max_amount']))
                $select->where("request_amount <=?", $api->getPriceWithCurrency(trim($params['request_max_amount']), 1, 1));

            if (!empty($params['response_min_amount']))
                $select->where("response_amount >=?", $api->getPriceWithCurrency(trim($params['response_min_amount']), 1, 1)); 

            if (!empty($params['response_max_amount']))
                $select->where("response_amount <=?", $api->getPriceWithCurrency(trim($params['response_max_amount']), 1, 1)); 

            if (!empty($params['request_status'])) {
                $params['request_status'] --;
                $select->where('request_status =? ', $params['request_status']);
            }
        }

        $select->order('request_id DESC');
        return $select;
    }

    /**
     * Return response detail for a payment request id
     *
     * @param $request_id
     * @return object
     */
    public function getResponseDetail($request_id) {
        return $this->select()->from($this->info('name'), array('response_amount', 'project_id', 'response_message'))->where('request_id =?', $request_id)->query()->fetchAll();
    }

    /**
     * Return sum of requested amount of a project
     *
     * @param $request_id
     * @return float
     */
    public function getRequestedAmount($project_id) {
        return $this->select()
                        ->from($this->info('name'), array('SUM(request_amount)'))
                        ->where('project_id =? AND request_status = 0', $project_id)
                        ->query()
                        ->fetchColumn();
    }

    /**
     * Return all admin transactions detail for a project
     *
     * @param array $params
     * @return object
     */
    public function getAllAdminTransactionsPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getAllAdminTransactionsSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getAllAdminTransactionsSelect($params) {
        $transactionTableName = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->info('name');
        $paymentRequestTableName = $this->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($paymentRequestTableName, array("$paymentRequestTableName.request_id", "$paymentRequestTableName.project_id", "$paymentRequestTableName.response_amount", "$paymentRequestTableName.gateway_id", "$paymentRequestTableName.gateway_profile_id", "$paymentRequestTableName.response_date"))
                ->join($transactionTableName, "($transactionTableName.source_id = $paymentRequestTableName.request_id)", array("$transactionTableName.transaction_id", "$transactionTableName.type", "$transactionTableName.state"))
                ->where("$transactionTableName.sender_type = 1")
                ->where("$paymentRequestTableName.project_id =?", $params['project_id'])
                ->where("$transactionTableName.source_type = ?", 'sitecrowdfunding_paymentrequest')
                ->group($paymentRequestTableName . '.request_id');

        if (isset($params['search'])) {
            if (!empty($params['date']))
                $select->where("CAST($transactionTableName.timestamp AS DATE) =?", trim($params['date']));

            if (!empty($params['response_min_amount']))
                $select->where("$paymentRequestTableName.response_amount >=?", trim($params['response_min_amount']));

            if (!empty($params['response_max_amount']))
                $select->where("$paymentRequestTableName.response_amount <=?", trim($params['response_max_amount']));

            if (!empty($params['state'])) {
                switch ($params['state']) {
                    case 1:
                        $state = 'processing';
                        break;

                    case 2:
                        $state = 'pending';
                        break;
                }

                $params['state'] --;

                $select->where($transactionTableName . '.state LIKE ? ', '%' . $state . '%');
            }
        }

        $select->order('transaction_id DESC');

        return $select;
    }

}
