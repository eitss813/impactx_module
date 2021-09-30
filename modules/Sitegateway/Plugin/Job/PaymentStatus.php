<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Encode.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Plugin_Job_PaymentStatus extends Core_Plugin_Job_Abstract {

    protected function _execute() {
        // Get job and params
        $job = $this->getJob();
        set_time_limit(0);
        // No video id?
        if (!($payout_id = $this->getParam('payout_id'))) {
            $this->_setState('failed', 'No payout identity provided.');
            $this->_setWasIdle();
            return;
        }
        if (!($resource_type = $this->getParam('resource_type'))) {
            $this->_setState('failed', 'No resource type provided.');
            $this->_setWasIdle();
            return;
        }
        if (!($resource_id = $this->getParam('resource_id'))) {
            $this->_setState('failed', 'No resource id provided.');
            $this->_setWasIdle();
            return;
        }

        // Get Resource object
        $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
        if (!$resource) {
            $this->_setState('failed', 'Resource is missing.');
            $this->_setWasIdle();
            return;
        }
        // Process
        try {
            $sitegatewayApi = Engine_Api::_()->sitegateway();
            $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
            if ($resource_type == 'sitecrowdfunding_project') {
                $payout_status = $this->_processAll($resource, $payout_id);
                $transactionsTableName = Engine_Api::_()->getDbtable('transactions', 'sitegateway')->info('name');
                $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
                $backersTableName = $backersTable->info('name');
                $db = $backersTable->getAdapter();
                $query = "update {$transactionsTableName} set payout_status='{$payout_status}' where gateway_id ={$adminGateway->gateway_id} and resource_type='sitecrowdfunding_backer' and resource_id in (select backer_id from {$backersTableName} where project_id = $resource_id and gateway_id ={$adminGateway->gateway_id} and payment_status='active');";
                $db->query($query);
                $query = "update $backersTableName set payout_status = '{$payout_status}' where project_id = $resource_id and gateway_id ={$adminGateway->gateway_id} and payment_status='active'";
                $db->query($query);
            } else {
                $transaction = Engine_Api::_()->sitegateway()->getTransaction($resource_type, $resource_id);
                $this->_process($resource, $transaction, $payout_id);
            }
            $this->_setIsComplete(true);
        } catch (Exception $e) {
            $this->_setState('failed', 'Exception: ' . $e->getMessage());
        }
    }

    public function _processAll($resource, $payout_id) {
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $message = "";
        $payoutDetail = $adminGateway->getService()->viewPayout($payout_id);
        if ($payoutDetail->Status == 'CREATED') {
            //CREATED
            $payout_status = 'pending';
            $message = "MangoPay Payment status is inprocess. MangoPay team verify then payout will be made.";
        } else if ($payoutDetail->Status == 'SUCCEEDED') {
            //SUCCEEDED
            $payout_status = 'success';
        } else {
            //FAILED
            $payout_status = 'failed';
            $message = $payoutDetail->ResultMessage;
        }
        if (isset($resource->message)) {
            $resource->message = $message;
            $resource->save();
        }
        return $payout_status;
    }

    public function _process($resource, $transaction, $payout_id) {
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');

        $payoutDetail = $adminGateway->getService()->viewPayout($payout_id);
        if ($payoutDetail->Status == 'CREATED') {
            //CREATED
            if (isset($resource->payout_status)) {
                $resource->payout_status = $payout_status = 'pending';
            }
        } else if ($payoutDetail->Status == 'SUCCEEDED') {
            //SUCCEEDED
            if (isset($resource->payout_status)) {
                $resource->payout_status = $payout_status = 'success';
            }
        } else {
            //FAILED
            if (isset($resource->payout_status)) {
                $resource->payout_status = $payout_status = 'failed';
            }
        }
        $transaction->payout_status = $payout_status;
        $transaction->save();
        $resource->save();
    }

}
