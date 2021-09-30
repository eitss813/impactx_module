<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Abstract.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
abstract class Sitegateway_Plugin_Gateway_Abstract extends Engine_Payment_Plugin_Abstract {

    /**
     * Get the service API
     *
     * @return Engine_Service_Gateway
     */
    public function getService() {
        return $this->getGateway()->getService();
    }

    /**
     * Create a transaction object from specified parameters
     *
     * @return Engine_Payment_Transaction
     */
    public function createTransaction(array $params) {
        $transaction = new Engine_Payment_Transaction($params);
        $transaction->process($this->getGateway());
        return $transaction;
    }

    /**
     * Create an ipn object from specified parameters
     *
     * @return Engine_Payment_Ipn
     */
    public function createIpn(array $params) {   
        $sitegatewayStripIPN = Zend_Registry::isRegistered('sitegatewayStripIPN') ? Zend_Registry::get('sitegatewayStripIPN') : null;
        if (!empty($sitegatewayStripIPN)) {
            $ipn = new Engine_Payment_Ipn($params);
            $ipn->process($this->getGateway());

            return $ipn;
        }
    }

    /**
     * Create a transaction for a subscription
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $subscription
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createSubscriptionTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $subscription, Payment_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $subscription, $package, $params);
    }

    /**
     * Create a transaction for a event package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $event
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createEventTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $event, Siteeventpaid_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $event, $package, $params);
    }

    /**
     * Create a transaction for a project package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $project
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createProjectTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $project, Sitecrowdfunding_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $project, $package, $params);
    }

    /**
     * Create a transaction for a listing package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $listing
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createListingTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $listing, Sitereviewpaidlisting_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $listing, $package, $params);
    }

    /**
     * Create a transaction for a page package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $page
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createPageTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $page, Sitepage_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $page, $package, $params);
    }

    /**
     * Create a transaction for a business package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $business
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createBusinessTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $business, Sitebusiness_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $business, $package, $params);
    }

    /**
     * Create a transaction for a group package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $group
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createGroupTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $group, Sitegroup_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $group, $package, $params);
    }

    /**
     * Create a transaction for a store package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $store
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createStoreTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $store, Sitestore_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $store, $package, $params);
    } 

    /**
     * Create a transaction for a communityads package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $userad
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createUseradTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $userad, Communityad_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $userad, $package, $params);
    }

    /**
     * Create a transaction for a siteads package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $userad
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createUserSiteadTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $userad, Sitead_Model_Package $package, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceTransaction($user, $userad, $package, $params);
    } 

    /**
     * Create a transaction object from specified parameters
     *
     * @return Engine_Payment_Transaction
     */
    public function createProjectBillTransaction($project_id, $bill_id, $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->createResourceBillTransaction($project_id, $bill_id, $params);
    } 

    /**
     * Process return of page/event/listing transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onPageTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceTransactionReturn($order, $params);
    }

    /**
     * Process return of business transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onBusinessTransactionReturn(Payment_Model_Order $order, array $params = array()) {
        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceTransactionReturn($order, $params);
    }

    /**
     * Process return of group transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onGroupTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceTransactionReturn($order, $params);
    }

    /**
     * Process return of store transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onStoreTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceTransactionReturn($order, $params);
    }

    /**
     * Process return of communityads transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onUseradTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceTransactionReturn($order, $params);
    }

    /**
     * Process return of subscription transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onSubscriptionTransactionReturn(Payment_Model_Order $order, array $params = array()) {
        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceTransactionReturn($order, $params);
    }

    /**
     * Process return of site admin commission/bill transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onEventBillTransactionReturn(Payment_Model_Order $order, array $params = array()) {
        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceBillTransactionReturn($order, $params);
    }

    /**
     * Process return of site sadmin commission/bill transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onStoreBillTransactionReturn(Payment_Model_Order $order, array $params = array()) {
        $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceBillTransactionReturn($order, $params);
    }

       /**
     * Method to process payment transaction after payment request made by sellers (if you have enabled the "Direct Payment to Sellers" flow). You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onUserRequestTransactionReturn(Payment_Model_Order $order, array $params = array()) {
  
       $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceBillTransactionReturn($order, $params); 
    }

       /**
     * Process return of an user after order transaction. 
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onUserOrderTransactionReturn(Payment_Model_Order $order, array $params = array()) {
   
         $sitegatewayStripTransaction = Zend_Registry::isRegistered('sitegatewayStripTransaction') ? Zend_Registry::get('sitegatewayStripTransaction') : null;
        if (!empty($sitegatewayStripTransaction))
            return $this->onResourceBillTransactionReturn($order, $params); 
    }

    /**
     * Process ipn of page/event/listing transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onPageTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

        $sitegatewayStripIPN = Zend_Registry::isRegistered('sitegatewayStripIPN') ? Zend_Registry::get('sitegatewayStripIPN') : null;
        if (!empty($sitegatewayStripIPN))
            return $this->onResourceTransactionIpn($order, $ipn);
    }
    
    /**
     * Process ipn of crowdfunding transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onProjectTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

        $sitegatewayStripIPN = Zend_Registry::isRegistered('sitegatewayStripIPN') ? Zend_Registry::get('sitegatewayStripIPN') : null;
        if (!empty($sitegatewayStripIPN))
            return $this->onResourceTransactionIpn($order, $ipn);
    }

    /**
     * Process ipn of business transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onBusinessTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

        $sitegatewayStripIPN = Zend_Registry::isRegistered('sitegatewayStripIPN') ? Zend_Registry::get('sitegatewayStripIPN') : null;
        if (!empty($sitegatewayStripIPN))
            return $this->onResourceTransactionIpn($order, $ipn);
    }

    /**
     * Process ipn of group transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onGroupTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

        $sitegatewayStripIPN = Zend_Registry::isRegistered('sitegatewayStripIPN') ? Zend_Registry::get('sitegatewayStripIPN') : null;
        if (!empty($sitegatewayStripIPN))
            return $this->onResourceTransactionIpn($order, $ipn);
    }

    /**
     * Process ipn of store transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onStoreTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

        $sitegatewayStripIPN = Zend_Registry::isRegistered('sitegatewayStripIPN') ? Zend_Registry::get('sitegatewayStripIPN') : null;
        if (!empty($sitegatewayStripIPN))
            return $this->onResourceTransactionIpn($order, $ipn);
    }

    /**
     * Process ipn of communityads transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onUseradTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

        $sitegatewayStripIPN = Zend_Registry::isRegistered('sitegatewayStripIPN') ? Zend_Registry::get('sitegatewayStripIPN') : null;
        if (!empty($sitegatewayStripIPN))
            return $this->onResourceTransactionIpn($order, $ipn);
    }

    /**
     * Process ipn of subscription transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onSubscriptionTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
        $sitegatewayStripIPN = Zend_Registry::isRegistered('sitegatewayStripIPN') ? Zend_Registry::get('sitegatewayStripIPN') : null;
        if (!empty($sitegatewayStripIPN))
            return $this->onResourceTransactionIpn($order, $ipn);
    }

    /**
     * Cancel a event package subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelEvent($transactionId, $note = null) {

        $sitegatewayStripCancel = Zend_Registry::isRegistered('sitegatewayStripCancel') ? Zend_Registry::get('sitegatewayStripCancel') : null;
        if (!empty($sitegatewayStripCancel))
            $this->cancelResourcePackage($transactionId, $note = null);

        return $this;
    }

    /**
     * Cancel a project package subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelProject($transactionId, $note = null) {

        $sitegatewayStripCancel = Zend_Registry::isRegistered('sitegatewayStripCancel') ? Zend_Registry::get('sitegatewayStripCancel') : null;
        if (!empty($sitegatewayStripCancel))
            $this->cancelResourcePackage($transactionId, $note = null);

        return $this;
    }

    /**
     * Cancel a page package subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelPage($transactionId, $note = null) {

        $sitegatewayStripCancel = Zend_Registry::isRegistered('sitegatewayStripCancel') ? Zend_Registry::get('sitegatewayStripCancel') : null;
        if (!empty($sitegatewayStripCancel))
            $this->cancelResourcePackage($transactionId, $note = null);

        return $this;
    }

    /**
     * Cancel a business package subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelBusiness($transactionId, $note = null) {

        $sitegatewayStripCancel = Zend_Registry::isRegistered('sitegatewayStripCancel') ? Zend_Registry::get('sitegatewayStripCancel') : null;
        if (!empty($sitegatewayStripCancel))
            $this->cancelResourcePackage($transactionId, $note = null);

        return $this;
    }

    /**
     * Cancel a group package subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelGroup($transactionId, $note = null) {

        $sitegatewayStripCancel = Zend_Registry::isRegistered('sitegatewayStripCancel') ? Zend_Registry::get('sitegatewayStripCancel') : null;
        if (!empty($sitegatewayStripCancel))
            $this->cancelResourcePackage($transactionId, $note = null);

        return $this;
    }

    /**
     * Cancel a store package subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelStore($transactionId, $note = null) {

        $sitegatewayStripCancel = Zend_Registry::isRegistered('sitegatewayStripCancel') ? Zend_Registry::get('sitegatewayStripCancel') : null;
        if (!empty($sitegatewayStripCancel))
            $this->cancelResourcePackage($transactionId, $note = null);

        return $this;
    }

    /**
     * Cancel a listing package subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelListing($transactionId, $note = null) {

        $sitegatewayStripCancel = Zend_Registry::isRegistered('sitegatewayStripCancel') ? Zend_Registry::get('sitegatewayStripCancel') : null;
        if (!empty($sitegatewayStripCancel))
            $this->cancelResourcePackage($transactionId, $note = null);

        return $this;
    }

    /**
     * Cancel a subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelSubscription($transactionId, $note = null) {

        $sitegatewayStripCancel = Zend_Registry::isRegistered('sitegatewayStripCancel') ? Zend_Registry::get('sitegatewayStripCancel') : null;
        if (!empty($sitegatewayStripCancel))
            $this->cancelResourcePackage($transactionId, $note = null);

        return $this;
    }

    public function onResourceIpn($order, $ipn = null) {

        // Subscription IPN
        if ($order->source_type == 'siteevent_event' || $order->source_type == 'sitereview_listing' || $order->source_type == 'sitepage_page') {

            $this->onPageTransactionIpn($order, $ipn);

            return $this;
        } elseif ($order->source_type == 'sitebusiness_business') {

            $this->onBusinessTransactionIpn($order, $ipn);

            return $this;
        } elseif ($order->source_type == 'sitegroup_group') {

            $this->onGroupTransactionIpn($order, $ipn);

            return $this;
        } elseif ($order->source_type == 'sitestore_store') {

            $this->onStoreTransactionIpn($order, $ipn);

            return $this;
        } elseif ($order->source_type == 'payment_subscription') {

            $this->onSubscriptionTransactionIpn($order, $ipn);

            return $this;
        } elseif ($order->source_type == 'siteeventticket_order' || $order->source_type == 'sitestoreproduct_order') {

            $this->onUserOrderTransactionIpn($order, $ipn);

            return $this;
        } elseif ($order->source_type == 'sitecrowdfunding_project' ) {

            $this->onProjectTransactionIpn($order, $ipn);

            return $this;
        }
        // Unknown IPN
        else {
            $error_msg16 = Zend_Registry::get('Zend_Translate')->_('Unknown order type for IPN');
            throw new Engine_Payment_Plugin_Exception($error_msg16);
        }
    }

    /**
     * Common function for create a transaction for a package
     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $resourceObject
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    abstract protected function createResourceTransaction($user, $resourceObject, $package, $params = array());

    /**
     * Create a transaction object from specified parameters
     *
     * @return Engine_Payment_Transaction
     */
    abstract protected function createResourceBillTransaction($object_id, $bill_id, $params = array());

    /**
     * Common function for process return of subscription transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    abstract protected function onResourceTransactionReturn(Payment_Model_Order $order, array $params = array());

    /**
     * Common function for process return of site admin commission/bill transaction
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    abstract protected function onResourceBillTransactionReturn(Payment_Model_Order $order, array $params = array());

    /**
     * Common function for processing ipn of package transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    abstract protected function onResourceTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn);

    /**
     * Common function for canceling a package subscription (i.e. disable the recurring payment profile)
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    abstract protected function cancelResourcePackage($transactionId, $note = null);
}
