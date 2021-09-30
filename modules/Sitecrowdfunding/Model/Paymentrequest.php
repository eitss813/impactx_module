<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Paymentrequest.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_Paymentrequest extends Core_Model_Item_Abstract {

    // Properties
    protected $_parent_type = 'project_backer';
    protected $_parent_is_owner = true;
    protected $_package;
    protected $_statusChanged;
    protected $_searchTriggers = false;

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'sitecrowdfunding_dashboard',
            'action' => 'project',
            'project_id' => $this->project_id,
//          'menuId' => 56,
            'method' => 'payment-to-me',
            'reset' => true,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);

        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    public function didStatusChange() {
        return (bool) $this->_statusChanged;
    }

    public function onPaymentSuccess() {
        $this->_statusChanged = false;

        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {

            // Change status
            if ($this->payment_status != 'active') {
                $this->payment_status = 'active';
                $this->_statusChanged = true;
            }

            $this->request_status = 2;  // PAYMENT REQUEST COMPLETE
            $remaining_amount = $this->request_amount - $this->response_amount;

            Engine_Api::_()->getDbtable('remainingamounts', 'sitecrowdfunding')->update(
                    array('remaining_amount' => new Zend_Db_Expr("remaining_amount + $remaining_amount")), array('project_id =? ' => $this->project_id));
        }
        $this->save();
        return $this;
    }

    public function onPaymentPending() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
            // Change status
            if ($this->payment_status != 'pending') {
                $this->payment_status = 'pending';
                $this->_statusChanged = true;
            }
            $this->payment_flag = 0;  // NOW USER CAN CHANGE REQUESTING AMOUNT
        }
        $this->save();
        return $this;
    }

    public function onPaymentFailure() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
            // Change status
            if ($this->payment_status != 'overdue') {
                $this->payment_status = 'overdue';
                $this->_statusChanged = true;
            }

            $session = new Zend_Session_Namespace('Payment_Userads');
            $session->unsetAll();

            $this->payment_flag = 0;  // NOW USER CAN CHANGE REQUESTING AMOUNT
        }
        $this->save();
        return $this;
    }

    public function onExpiration() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'expired'))) {
            // Change status
            if ($this->payment_status != 'expired') {
                $this->payment_status = 'expired';
                $this->approved = 0;
                $this->enable = 0;
                $this->status = 3;

                $this->_statusChanged = true;
            }

            $this->payment_flag = 0;  // NOW USER CAN CHANGE REQUESTING AMOUNT
        }
        $this->save();
        return $this;
    }

    public function onRefund() {
        $this->_statusChanged = false;
        if (in_array($this->payment_status, array('initial', 'trial', 'pending', 'active', 'refunded'))) {
            // Change status
            if ($this->payment_status != 'refunded') {
                $this->payment_status = 'refunded';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

}
