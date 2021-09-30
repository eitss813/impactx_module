<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Backer.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_Backer extends Core_Model_Item_Abstract {

    // Properties
    protected $_parent_is_owner = true;
    protected $_package;
    protected $_statusChanged;
    protected $_product;
    protected $_searchTriggers = false;

    public function onPaymentSuccess() {
        $this->_statusChanged = false;

        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired', 'authorised'))) {
            // Change status
            if ($this->payment_status != 'active') {
                $this->payment_status = 'active';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function onPaymentPending() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired', 'authorised'))) {
            // Change status
            if ($this->payment_status != 'pending') {
                $this->payment_status = 'pending';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function onPaymentFailure() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired', 'authorised'))) {
            // Change status
            if ($this->payment_status != 'overdue') {
                $this->payment_status = 'overdue';
                $this->_statusChanged = true;
            }

            $session = new Zend_Session_Namespace('Payment_Sitecrowdfunding');
            $session->unsetAll();
        }
        $this->save();
        return $this;
    }

    public function onRefund() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'refunded', 'authorised'))) {
            // Change status
            if ($this->payment_status != 'refunded') {
                $this->payment_status = 'refunded';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function payoutStatus() {

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $this->project_id);
        $gateway = Engine_Api::_()->getItem('sitecrowdfunding_gateway', $this->gateway_id);
        if (!Engine_Api::_()->sitegateway()->isEscrowGateway($gateway->plugin))
            return false;

        if (empty($project->payout_status)) {
            return false;
        }

        if ($this->payout_status == 'failed') {
            if ($gateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {
                return false;
            }
            return $project->payout_status;
        }
        if ($this->refund_status == 'failed') {
            return $project->payout_status;
        }
        return false;
    }

    public function paymentStatus() {
        $status = '';
        if ($this->payment_status == 'active') {
            $status = 'Okay';
        } else if ($this->payment_status == 'authorised') {
            $status = 'Authorized';
        } else if ($this->payment_status == 'refunded') {
            $status = 'Refunded';
        } else if ($this->payment_status == 'pending') {
            $status = 'Pending';
        } else if ($this->payment_status == 'failed') {
            $status = 'Failed';
        }
        return $status;
    }

}
