<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPackagesController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AdminPackagesController extends Core_Controller_Action_Admin {

    //ACTION FOR PACKAGE SETTINGS
    public function indexAction() {

        //TAB CREATION
        $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_package');

        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main_package', array(), 'sitecrowdfunding_admin_main_packagesettings');

        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Packages_Package();
        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            //UNSET ALL CHECKBOX VALUES BEFORE WE SET NEW VALUES.
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.package.setting', 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.package.information')) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitecrowdfunding.package.information');
            }
            include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
            $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
        }
    }

    //ACTION FOR MANAGE PACKAGE PROJECTS
    public function manageAction() {

        //TAB CREATION
        $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_package');

        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main_package', array(), 'sitecrowdfunding_admin_main_packagemanage');

        $this->view->canCreate = $canCreate = 1;

        if (Engine_Api::_()->hasModuleBootstrap('payment')) {
            $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
            $this->view->isEnabled2Checkout = $gatewayTable->select()
                    ->from($gatewayTable, 'enabled')
                    ->where('plugin = ?', 'Payment_Plugin_Gateway_2Checkout')
                    ->query()
                    ->fetchColumn();

            //TEST CURL SUPPORT
            if (!function_exists('curl_version') ||
                    !($info = curl_version())) {
                $this->view->error = 'The PHP extension cURL' .
                        'does not appear to be installed, which is required' .
                        'for interaction with payment gateways. Please contact your' .
                        'hosting provider.';
            }
            //TEST CURL SSL SUPPORT
            else if (!($info['features'] & CURL_VERSION_SSL) ||
                    !in_array('https', $info['protocols'])) {
                $this->view->error = 'The installed version of' .
                        'the cURL PHP extension does not support HTTPS, which is required' .
                        'for interaction with payment gateways. Please contact your' .
                        'hosting provider.';
            }
            //CHECK FOR ENABLE PAYMENT GATEWAYS
            else if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
                $this->view->error = $this->view->translate('There are currently no enabled payment gateways. You must %1$senable payment gatways%2$s before creating a paid package.', '<a href="' .
                        $this->view->escape($this->view->url(array('module' => 'payment', 'controller' => 'gateway', 'action' => 'index'))) .
                        '"  target="_blank" >', '</a>');
            }
        } else {
            $this->view->canCreate = $canCreate = 0;
            $this->view->error = 'You have not install or enable "Payment" module. Please install or enable "Payment" module to create or edit package.';
        }

        //INITILIZE SELECT
        $table = Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding');
        $select = $table->select();

        //FILTER FORM
        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_Packages_Filter();

        //PROCESS FORM
        if ($formFilter->isValid($this->_getAllParams())) {
            $filterValues = $formFilter->getValues();
        }
        if (empty($filterValues['order'])) {
            $select->order("order");
            $filterValues['order'] = 'package_id';
        }
        if (empty($filterValues['direction'])) {

            $filterValues['direction'] = 'DESC';
        }
        $this->view->filterValues = $filterValues;
        $this->view->order = $filterValues['order'];
        $this->view->direction = $filterValues['direction'];

        //ADD FILTER VALUES
        if (!empty($filterValues['query'])) {
            $select->where('title LIKE ?', '%' . $filterValues['query'] . '%');
        }

        if (isset($filterValues['enabled']) && '' != $filterValues['enabled']) {
            $select->where('enabled = ?', $filterValues['enabled']);
        }

        if (!empty($filterValues['order'])) {
            if (empty($filterValues['direction'])) {
                $filterValues['direction'] = 'ASC';
            }
            $select->order($filterValues['order'] . ' ' . $filterValues['direction']);
        }
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        //GET PROJECTS TOTALS FOR EACH PACKAGE
        $memberCounts = array();
        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        foreach ($paginator as $item) {

            $memberCounts[$item->package_id] = $projectTable->select()
                    ->from($projectTable->info('name'), new Zend_Db_Expr('COUNT(*)'))
                    ->where('package_id = ?', $item->package_id)
                    ->query()
                    ->fetchColumn();
        }
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
    }

    public function createAction() {

        if (!Engine_Api::_()->hasModuleBootstrap('payment')) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_package');

        //FORM GENERATION
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Packages_Create();
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
        if (empty($shouldCreate)) {
            return;
        }
        //GET SUPPORTED BILLING CYCLES
        $gateways = array();
        $supportedBillingCycles = array();
        $partiallySupportedBillingCycles = array();
        $fullySupportedBillingCycles = null;
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gatewaySupportedBillingCycles = $gateway->getGateway()->getSupportedBillingCycles();
            $gateways[$gateway->gateway_id] = $gateway->title;
            $supportedBillingIndex[$gateway->title] = $gatewaySupportedBillingCycles;
            $supportedBillingCycles[$gateway->gateway_id] = $gatewaySupportedBillingCycles;
            $partiallySupportedBillingCycles = array_merge($partiallySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
        }
        $partiallySupportedBillingCycles = array_unique($partiallySupportedBillingCycles);
        $multiOptions = array_combine(array_map('strtolower', $partiallySupportedBillingCycles), $partiallySupportedBillingCycles);
        unset($multiOptions['one-time']);
        $this->view->gateways = $gateways;
        $this->view->supportedBillingIndex = $supportedBillingIndex;

        $form->getElement('recurrence')
                ->setMultiOptions($multiOptions);
        $form->getElement('recurrence')->options['forever'] = 'One-time';
        $form->getElement('duration')
                ->setMultiOptions($multiOptions);
        $form->getElement('duration')->options['forever'] = 'Forever';

        //FORM VALDIATION
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $values = $form->getValues();
        $tmp = $values['recurrence'];
        unset($values['recurrence']);
        if (empty($tmp) || !is_array($tmp)) {
            $tmp = array(null, null);
        }

        $values['recurrence'] = (int) $tmp[0];
        $values['recurrence_type'] = ($tmp[1] == 'daily') ? 'day' : $tmp[1];
        if ($values['price'] > 0) {

            //FOR NOT ENABLE GATEWAYS
            if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
                $form->getDecorator('errors')->setOption('escape', false);

                $error = $this->view->translate('You have not enabled a payment gateway yet. Please %1$senable payment gateways%2$s  before creating a paid package.', '<a href="' . $this->view->baseUrl() . '/admin/payment/gateway" ' . " target='_blank'" . '">', '</a>');
                $this->view->status = false;
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                return $form->addError($error);
            }
        }
        //MEMBER LEVEL SETTING WORK
        if (@in_array('0', $values['level_id'])) {
            $values['level_id'] = 0;
        } else {
            $values['level_id'] = implode(',', $values['level_id']);
        }

        $tmp = $values['duration'];
        unset($values['duration']);
        if (empty($tmp) || !is_array($tmp)) {
            $tmp = array(null, null);
        }
        $values['duration'] = (int) $tmp[0];
        $values['duration_type'] = ($tmp[1] == 'daily') ? 'day' : $tmp[1];
        $commissionValues = array();
        $commissionValues['commission_handling'] = $values['commission_handling'];
        $commissionValues['commission_fee'] = $values['commission_fee'];
        $commissionValues['commission_rate'] = $values['commission_rate'];
        $values['commission_settings'] = @serialize($commissionValues);
        unset($values['commission_handling']);
        unset($values['commission_fee']);
        unset($values['commission_rate']);
        $packageTable = Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {
            $package = $packageTable->createrow();
            $package->setFromArray($values);
            $package->save();
            //CREATE PACKAGE IN GATEWAYS?
            if (!$package->isFree()) {
                $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
                foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                    $gatewayPlugin = $gateway->getGateway();
                    //CHECK BILLING CYCLE SUPPORT
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

        //REDIRECT
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }

    //ACTION FOR PACKAGE EDIT
    public function editAction() {

        if (!Engine_Api::_()->hasModuleBootstrap('payment')) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_package');
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
        if (empty($tempEdit)) {
            return;
        }
        //GET PACKAGES
        if (null == ($packageIdentity = $this->_getParam('package_id')) ||
                !($package = Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding')->find($packageIdentity)->current())) {
            throw new Engine_Exception('No package found');
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Packages_Edit();
        $this->view->package = $package;
        $values = $package->toArray();

        //COMMISSION SETTINGS POPULATE
        if (!empty($values['commission_settings'])) {
            $getCommissionSettings = @unserialize($values['commission_settings']);
            unset($values['commission_settings']);
            $values = @array_merge($values, $getCommissionSettings);
        }

        $values['recurrence'] = array($values['recurrence'], $values['recurrence_type']);

        $values['duration'] = array($values['duration'], $values['duration_type']);
        unset($values['recurrence_type']);

        unset($values['duration_type']);
        $values['level_id'] = explode(',', $values['level_id']);

        $otherValues = array(
            'price' => $values['price'],
            'recurrence' => $values['recurrence'],
            'duration' => $values['duration'],
        );
        $form->populate($values);

        //GET SUPPORTED GATEWAYS AND BILLING CYCLES
        $gateways = array();
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gatewaySupportedBillingCycles = $gateway->getGateway()->getSupportedBillingCycles();
            $gateways[$gateway->gateway_id] = $gateway->title;
            $supportedBillingIndex[$gateway->title] = $gatewaySupportedBillingCycles;
        }
        $this->view->gateways = $gateways;
        $this->view->supportedBillingIndex = $supportedBillingIndex;

        //CHECK METHOD DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //HACK EM UP
        $form->populate($otherValues);

        //PROCESS
        $values = $form->getValues();

        //for member level seting work
        if (@in_array('0', $values['level_id'])) {
            $values['level_id'] = 0;
        } else {
            $values['level_id'] = implode(',', $values['level_id']);
        }
        //for project type seting work


        unset($values['price']);
        unset($values['recurrence']);
        unset($values['recurrence_type']);
        unset($values['duration']);
        unset($values['duration_type']);
        unset($values['trial_duration']);
        unset($values['trial_duration_type']);
        $commissionValues = array();
        $commissionValues['commission_handling'] = $values['commission_handling'];
        $commissionValues['commission_fee'] = $values['commission_fee'];
        $commissionValues['commission_rate'] = $values['commission_rate'];
        $values['commission_settings'] = @serialize($commissionValues);
        unset($values['commission_handling']);
        unset($values['commission_fee']);
        unset($values['commission_rate']);
        $packageTable = Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {
            $package->setFromArray($values);
            $package->save();

            //CREATE PACKAGE IN GATEWAYS
            if (!$package->isFree()) {
                $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
                foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                    $gatewayPlugin = $gateway->getGateway();

                    //CHECK BILLING CYCLE SUPPORT
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

                    //IF IT THROWS AN EXCEPTION, OR RETURNS EMPTY, ASSUME IT DOESN'T EXIST?
                    try {
                        $info = $gatewayPlugin->detailVendorProduct($package->getGatewayIdentity());
                    } catch (Exception $e) {
                        $info = false;
                    }
                    //CREATE
                    if (!$info) {
                        $gatewayPlugin->createProduct($package->getGatewayParams());
                    }
                    //EDIT
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

        //REDIRECT
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }

    //ACTION FOR MAKE PACKAGES ENABLE/DISABLE
    public function enabledAction() {
        $id = $this->_getParam('id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $id);
        if ($package->enabled == 0) {
            try {
                $package->enabled = 1;
                $package->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_redirect('admin/sitecrowdfunding/packages/manage');
        } else {
            if ($this->getRequest()->isPost()) {
                try {
                    $package->enabled = 0;
                    $package->save();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
                ));
            }
        }
    }

    //ACTION FOR DELETE THE PACKAGE
    public function deleteAction() {

        $this->_helper->layout->setLayout('admin-simple');

        // Get package
        if (null === ($packageIdentity = $this->_getParam('id')) ||
                !($package = Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding')->find($packageIdentity)->current())) {
            throw new Engine_Exception('No package found');
        }

        //Default package or package containing projects
        $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $this->_getParam('id'));
        if ($package->defaultpackage || $package->hasProjects())
            return;

        if ($this->getRequest()->isPost()) {

            $packageTable = Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding');
            $db = $packageTable->getAdapter();
            $db->beginTransaction();
            try {
                // Delete package in gateways?
                $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
                foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                    $gatewayPlugin = $gateway->getGateway();
                    if (method_exists($gatewayPlugin, 'deleteProduct')) {
                        try {
                            $gatewayPlugin->deleteProduct($package->getGatewayIdentity());
                        } catch (Exception $e) {
                            
                        } // Silence?
                    }
                }

                // Delete package
                $package->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Deleted Succesfully.')
            ));
        }
        $this->renderScript('admin-packages/delete.tpl');
    }

    //ACTION FOR SHOW THE PACKAGE DETAILS
    public function packageDetailAction() {

        $id = $this->_getParam('id');
        if (empty($id)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        $this->view->package = $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $id);
        $this->view->overview = $coreSettingsApi->getSetting('sitecrowdfunding.overview', 1);
        $this->view->viewer = Engine_Api::_()->user()->getViewer();
    }

    //ACTION FOR PACKAGE ORDER UPDATION
    public function updateAction() {

        //CHECK POST
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            $values = $_POST;
            try {
                foreach ($values['order'] as $key => $value) {

                    $package = Engine_Api::_()->getItem('sitecrowdfunding_package', (int) $value);
                    if (!empty($package)) {
                        $package->order = $key + 1;
                        $package->save();
                    }
                }
                $db->commit();
                $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    public function packageTransactionsAction() {

        $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_transactions'); 

        if (!Engine_Api::_()->hasModuleBootstrap('payment')) {
            $this->view->error = 'You have not install or enable "Payment" module. Please install or enable "Payment" module to create or edit package.';
            return;
        }

        $this->view->paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);

        //TEST CURL SUPPORT
        if (!function_exists('curl_version') ||
                !($info = curl_version())) {
            $this->view->error = 'The PHP extension cURL does not appear to be installed, which is required for interaction with payment gateways. Please contact your hosting provider.';
        }
        //TEST CURL SSL SUPPORT
        else if (!($info['features'] & CURL_VERSION_SSL) ||
                !in_array('https', $info['protocols'])) {
            $this->view->error = 'The installed version of the cURL PHP extension does not support HTTPS, which is required for interaction with payment gateways. Please contact your hosting provider.';
        }
        //CHECK FOR ENABLED PAYMENT GATEWAYS
        else if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
            $this->view->error = $this->view->translate('You have not enabled a payment gateway yet. Please %1$senable payment gateways%2$s  for transactions to occur on your site.', '<a href="' .
                    $this->view->baseUrl() . '/admin/payment/gateway" ' .
                    " target='_blank'" . '">', '</a>');
        }

        //MAKE FORM
        $this->view->formFilter = $formFilter = new Sitecrowdfunding_Form_Admin_Transaction_Filter();

        //PROCESS FORM
        if ($formFilter->isValid($this->_getAllParams())) {
            $filterValues = $formFilter->getValues();
        } else {
            $filterValues = array();
        }
        if (empty($filterValues['order'])) {
            $filterValues['order'] = 'transaction_id';
        }
        if (empty($filterValues['direction'])) {
            $filterValues['direction'] = 'DESC';
        }
        $this->view->filterValues = $filterValues;
        $this->view->order = $filterValues['order'];
        $this->view->direction = $filterValues['direction'];

        //INITIALIZE SELECT

        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
        $transactionsName = $transactionsTable->info('name');

        $projectTableName = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->info('name');

        $gatewayTableName = Engine_Api::_()->getDbtable('gateways', 'payment')->info('name');

        $transactionSelect = $transactionsTable->select()
                ->from($transactionsName, array('transaction_id', 'user_id', 'timestamp', 'source_type', 'type', 'state', 'amount', 'currency'))
                ->setIntegrityCheck(false)
                ->joinLeft($tableUserName, "$tableUserName.user_id = $transactionsName.user_id", 'username')
                ->joinRight($projectTableName, "$projectTableName.project_id = $transactionsName.source_id", array('title', 'project_id'))
                ->joinLeft($gatewayTableName, "$transactionsName.gateway_id = $gatewayTableName.gateway_id", 'title as gateway_title')
                ->group("$transactionsName.transaction_id");

        //FETCH ONLY SITECROWDFUNDING_PROJECT TYPE ROWS
        $transactionSelect->where($transactionsName . '.source_type = ?', 'sitecrowdfunding_project');



        //ADD FILTER VALUES
        if (!empty($filterValues['gateway_id'])) {
            $transactionSelect->where($transactionsName . '.gateway_id = ?', $filterValues['gateway_id']);
        }
        if (!empty($filterValues['type'])) {
            $transactionSelect->where($transactionsName . '.type = ?', $filterValues['type']);
        }
        if (!empty($filterValues['state'])) {
            $transactionSelect->where($transactionsName . '.state = ?', $filterValues['state']);
        }
        if (!empty($filterValues['query'])) {
            $transactionSelect
                    ->join($tableUserName, "$tableUserName.user_id = $transactionsName.user_id")
                    ->where('(' . $transactionsName . '.gateway_transaction_id LIKE ? || ' .
                            $transactionsName . '.gateway_parent_transaction_id LIKE ? || ' .
                            $transactionsName . '.gateway_order_id LIKE ? || ' .
                            $projectTableName . '.title LIKE ? || ' .
                            $tableUserName . '.displayname LIKE ? || ' .
                            $tableUserName . '.username LIKE ? || ' .
                            'engine4_users.email LIKE ?)', '%' . $filterValues['query'] . '%');
        }

        if (!empty($filterValues['order'])) {
            if (empty($filterValues['direction'])) {
                $filterValues['direction'] = 'DESC';
            }
            $transactionSelect->order($filterValues['order'] . ' ' . $filterValues['direction']);
        }


        $this->view->paginator = Zend_Paginator::factory($transactionSelect);
        $this->view->paginator->setItemCountPerPage(100);
    }

    public function detailAction() {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('payment')) {
            die;
        }
        // Missing transaction
        if (!($transaction_id = $this->_getParam('transaction_id')) ||
                !($transaction = Engine_Api::_()->getItem('sitecrowdfunding_transaction', $transaction_id))) {
            return;
        }

        $this->view->transaction = $transaction;
        $this->view->gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
        $this->view->order = Engine_Api::_()->getItem('payment_order', $transaction->payment_order_id);
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $this->view->order->source_id);
        $this->view->title = $project->title;
        $this->view->user = Engine_Api::_()->getItem('user', $transaction->user_id);
    }

    //get link of transaction
    public function detailTransactionAction() {

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('payment')) {
            die;
        }
        $transaction_id = $this->_getParam('transaction_id');
        $transaction = Engine_Api::_()->getItem('sitecrowdfunding_transaction', $transaction_id);
        $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);

        $link = null;
        if ($this->_getParam('show-parent')) {
            if (!empty($transaction->gateway_parent_transaction_id)) {
                $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_parent_transaction_id);
            }
        } else {
            if (!empty($transaction->gateway_transaction_id)) {
                $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_transaction_id);
            }
        }
        if ($link) {
            return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
        } else {
            die();
        }
    }

}
