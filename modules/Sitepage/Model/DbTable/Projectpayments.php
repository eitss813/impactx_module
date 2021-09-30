<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectGateways.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Projectpayments extends Engine_Db_Table {

    protected $_rowClass = 'Sitepage_Model_Projectpayment';

    public function getProjectPaymentRow($page_id){
        $select = $this->select();
        $select->where('page_id = ?', $page_id);
        return $this->fetchRow($select);
    }

    public function getPaypalProjectPaymentRow($page_id){
        $select = $this->select();
        $select->where('page_id = ?', $page_id);
        $select->where('payment_type = ?', 'PAYPAL');
        return $this->fetchRow($select);
    }

    public function getStripeProjectPaymentRow($page_id){
        $select = $this->select();
        $select->where('page_id = ?', $page_id);
        $select->where('payment_type = ?', 'STRIPE');
        return $this->fetchRow($select);
    }

}
