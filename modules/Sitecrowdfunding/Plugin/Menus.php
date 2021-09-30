<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Plugin_Menus {

    public function canCreate() {

        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        // Must be able to create projects
        if (!Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, 'create')) {
            return false;
        }

        return true;
    }

    public function canManage() {

        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, 'view')) {
            return false;
        }

        return true;
    }

    public function canView() {
        $viewer = Engine_Api::_()->user()->getViewer();

        // Must be able to view projects
        if (!Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, 'view')) {
            return false;
        }

        return true;
    }

    public function canViewBackersFaq() {
        $viewer = Engine_Api::_()->user()->getViewer();

        // Must be able to view projects
        if (!Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, 'view')) {
            return false;
        }
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.backersfaq.enabled', 1)) {
            return false;
        }
        return true;
    }

    public function canViewProjectOwnerFaq() {
        $viewer = Engine_Api::_()->user()->getViewer();
        
        // Must be able to view projects
        if (!Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, 'view')) {
            return false;
        }
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.projectownerfaq.enabled', 1)) {
            return false;
        }
        return true;
    }

    public function canViewLocation() {
        $viewer = Engine_Api::_()->user()->getViewer();

        // Must be able to view projects
        if (!Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, 'view')) {
            return false;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            return false;
        }
        return true;
    }

    public function onMenuInitialize_SitecrowdfundingProfileEdit() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
            //return false;
        }

        return array(
            'label' => $view->translate('Edit Project'),
            'icon' => '',
            'class' => 'seaocore_icon_edit',
            'route' => 'sitecrowdfunding_specific',
            'params' => array(
                'action' => 'edit',
                'project_id' => $subject->getIdentity()
            )
        );
    }

    public function onMenuInitialize_SitecrowdfundingProfileDelete() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;

        if (!$subject->getOwner()->isSelf($viewer)) {
           // return false;
        }

        if (!Engine_Api::_()->sitecrowdfunding()->canDeletePrivacy($subject->parent_type, $subject->parent_id, $subject))
            //return false;
        return array(
            'label' => $view->translate('Delete Project'),
            'icon' => '',
            'route' => 'sitecrowdfunding_specific',
            'class' => 'smoothbox seaocore_icon_delete',
            'params' => array(
                'action' => 'delete',
                'project_id' => $subject->getIdentity()
            )
        );
    }

    public function onMenuInitialize_SitecrowdfundingProfileGetlink() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $view = Zend_Registry::isRegistered('Zend_View') ?
                Zend_Registry::get('Zend_View') : null;


        if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'view')) {
           // return false;
        }

        return array(
            'label' => $view->translate('Get Link'),
            'icon' => '',
            'route' => 'sitecrowdfunding_project_general',
            'class' => 'smoothbox seao_icon_sharelink_square',
            'params' => array(
                'action' => 'get-link',
                'subject' => $subject->getGuid(),
            )
        );
    }

    public function showAdminPaymentRequestTab() {
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        if ($paymentMethod != 'normal') {
            return false;
        }
        if (empty($isPaymentToSiteEnable)) {
            return false;
        }
        return true;
    }

    public function showAdminCommissionTab() {
        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        // if ($paymentMethod != 'normal') {
        //     return false;
        // }
        if (empty($isPaymentToSiteEnable)) {
            return true;
        }
        return false;
    }

    public function showAdminTransactionsTab() { 
    //IF SETTING DISABLED THEN DONT DISPLAY THIS TAB 
        $packageEnabled = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable(); 

        if($packageEnabled){
          return array(
              'route' => "admin_default",
              'module' => 'sitecrowdfunding',
              'controller' => 'packages',
              'action' => 'package-transactions'
              );
        } else {
        //PAYMENT FLOW CHECK
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
            if($paymentToSiteadmin){
                return array(
                  'route' => "admin_default",
                  'module' => 'sitecrowdfunding',
                  'controller' => 'transaction',
                  );         
            }else{
                return array(
                  'route' => "admin_default",
                  'module' => 'sitecrowdfunding',
                  'controller' => 'transaction',
                  'action' => 'backer-commission-transaction'
                  );    
            }
        } 
    }

}
