<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPackageController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminPackageController extends Core_Controller_Action_Admin {

    public function init() {

        //TAB CREATION
        $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->_viewer = Engine_Api::_()->user()->getViewer();
        $this->_viewer_id = $this->_viewer->getIdentity();
    }

    //ACTION FOR MANAGE PACKAGE LISTINGS
    public function indexAction() {
        
        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_plans');
        
        $this->view->canCreate = $canCreate = 1;
        if (Engine_Api::_()->sitepage()->enablePaymentPlugin()) {

            //TEST CURL SUPPORT
            if (!function_exists('curl_version') ||
                    !($info = curl_version())) {
                $this->view->error = $this->view->translate('The PHP extension cURL' .
                        'does not appear to be installed, which is required' .
                        'for interaction with payment gateways. Please contact your' .
                        'hosting provider.');
            }
            //TEST CURL SSL SUPPORT
            else if (!($info['features'] & CURL_VERSION_SSL) ||
                    !in_array('https', $info['protocols'])) {
                $this->view->error = $this->view->translate('The installed version of' .
                        'the cURL PHP extension does not support HTTPS, which is required' .
                        'for interaction with payment gateways. Please contact your' .
                        'hosting provider.');
            }
            //CHECK FOR ENABLE PAYMENT GATEWAYS
            else if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
                $this->view->error = $this->view->translate('There are currently no enabled payment gateways. You must %1$senable payment gatways%2$s before creating a paid package.', '<a href="' .
                        $this->view->escape($this->view->url(array('module' => 'payment', 'controller' => 'gateway'))) .
                        '"  target="_blank" >', '</a>');
            }
        } else {
            $this->view->canCreate = $canCreate = 0;
            $this->view->error = $this->view->translate('You have not install or enable "Payment" module. Please install or enable "Payment" module to create or edit package.');
        }        
                            
        //INITILIZE SELECT
        $table = Engine_Api::_()->getDbtable('packages', 'sitepage');
        $pageName = Engine_Api::_()->getItemtable('sitepage_page')->info("name");
        $select = $table->select();

        //FILTER FORM
        $this->view->formFilter = $formFilter = new Sitepage_Form_Admin_Package_Filter();

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

        //GET DATA
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        //GET PAGES TOTALS FOR EACH PACKAGE
        $memberCounts = array();
        foreach ($paginator as $item) {
            $memberCounts[$item->package_id] = Engine_Api::_()->getDbtable('pages', 'sitepage')
                    ->select()
                    ->from('engine4_sitepage_pages', new Zend_Db_Expr('COUNT(*)'))
                    ->where('package_id = ?', $item->package_id)
                    ->query()
                    ->fetchColumn();
        }
        $this->view->memberCounts = $memberCounts;
    }

    //ACTION FOR PACKAGE CREATE
    public function createAction() {

        if (!Engine_Api::_()->sitepage()->enablePaymentPlugin()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Admin_Package_Create();


        //GET SUPPORTED BILLING CYCLES
        $gateways = array();
        $supportedBillingCycles = array();
        $partiallySupportedBillingCycles = array();
        $fullySupportedBillingCycles = null;
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');

//    foreach ($gatewaysTable->fetchAll() as $gateway){
//      $gateways[$gateway->gateway_id] = $gateway->title;
//      $gatewaySupportedBillingCycles = $gateway->getGateway()->getSupportedBillingCycles();
//      if(count($gatewaySupportedBillingCycles)==0)
//        continue;
//      $supportedBillingIndex[$gateway->title] = $gatewaySupportedBillingCycles;
//      $supportedBillingCycles[$gateway->gateway_id] = $gatewaySupportedBillingCycles; 
//      $partiallySupportedBillingCycles = array_merge($partiallySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
//      if (null == $fullySupportedBillingCycles) {
//        $fullySupportedBillingCycles = $supportedBillingCycles[$gateway->gateway_id];
//      } else {
//        $fullySupportedBillingCycles = array_intersect($fullySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
//      }
//    }
//  
//    $partiallySupportedBillingCycles = array_diff($partiallySupportedBillingCycles, $fullySupportedBillingCycles);
//
//     $multiOptions = array();
//     if(count($fullySupportedBillingCycles)>0){
//       $multiOptions = array_combine(array_map('strtolower', $fullySupportedBillingCycles), $fullySupportedBillingCycles);
//      }

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
        //$form->getElement('recurrence')->options['day'] = 'Day';

        $form->getElement('duration')
                ->setMultiOptions($multiOptions);
        $form->getElement('duration')->options/* ['Fully Supported'] */['forever'] = 'Forever';

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
            $form->getElement('ads');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) {
            $form->getElement('twitter');
        }

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
        $values['recurrence_type'] = $tmp[1];

        if (!isset($values['ads'])) {
            $values['ads'] = 0;
        }

        if (!isset($values['twitter'])) {
            $values['twitter'] = 0;
        }

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

        //for member level seting work
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
        $values['duration_type'] = $tmp[1];
        if (isset($values['modules']))
            $values['modules'] = serialize($values['modules']);
        else
            $values['modules'] = serialize(array());

        $profileFields = array();
        if ($values['profile'] == 2) {
            foreach ($_POST as $key => $value) {
                if (@strstr($key, '_profilecheck_') != null && $value) {
                    $tc = @explode("_profilecheck_", $key);
                    $profileFields[] = "1_" . $tc[0] . "_" . $value;
                }
            }
        }
        $values['profilefields'] = serialize($profileFields);
        $packageTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {
            $package = $packageTable->createRow();
            $package->setFromArray($values);
            $package->save();

            // Create package in gateways?
            if (!$package->isFree()) {
                $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
                foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                    $gatewayPlugin = $gateway->getGateway();
                    // Check billing cycle support
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

                //START This code use for coupon edit when Create a new package and select all those coupon which have select all option for this package type.
                $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecoupon');
                if (!empty($moduleEnabled)) {
                    Engine_Api::_()->getDbtable('coupons', 'sitecoupon')->editCouponsAfterCreateNewPackage($package->getType());
                }
                //END COUPON WORK.
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //REDIRECT
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    //ACTION FOR PACKAGE EDIT
    public function editAction() {

        if (!Engine_Api::_()->sitepage()->enablePaymentPlugin()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET PACKAGES
        if (null == ($packageIdentity = $this->_getParam('package_id')) ||
                !($package = Engine_Api::_()->getDbtable('packages', 'sitepage')->find($packageIdentity)->current())) {
            throw new Engine_Exception('No package found');
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Admin_Package_Edit();

        $values = $package->toArray();

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


        $oldValuesModules = $values['modules'] = unserialize($values['modules']);

        $form->populate($values);
        $profileFields = array();
        if ($values['profile'] == 2) {
            $profileFields = unserialize($values['profilefields']);
        }
        $session = new Zend_Session_Namespace('profileFields');
        $session->profileFields = $profileFields;

        //CHECK METHOD DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        if (isset($session->profileFields)) {
            unset($session->profileFields);
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

        unset($values['price']);
        unset($values['recurrence']);
        unset($values['recurrence_type']);
        unset($values['duration']);
        unset($values['duration_type']);
        unset($values['trial_duration']);
        unset($values['trial_duration_type']);

        if (isset($values['modules'])) {
            $newValuesModules = $values['modules'];
            $values['modules'] = serialize($values['modules']);
        } else {
            $newValuesModules = $values['modules'];
            $values['modules'] = serialize(array());
        }

        $profileFields = array();
        if ($values['profile'] == 2) {
            $i = 0;
            foreach ($_POST as $key => $value) {
                if (@strstr($key, '_profilecheck_') != null && $value) {
                    $tc = @explode("_profilecheck_", $key);
                    $profileFields[] = "1_" . $tc[0] . "_" . $value;
                }
            }
        }

        $values['profilefields'] = serialize($profileFields);

        $packageTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {

            if (isset($oldValuesModules) && in_array("sitepageevent", $oldValuesModules) && isset($newValuesModules) && !in_array("sitepageevent", $newValuesModules)) {
                $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
                $rName = $table->info('name');
                $select = $table->select()->from($rName, 'page_id')->where('package_id =?', $this->_getParam('package_id'));
                ;

                //START PAGE-EVENT CODE
                $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
                $siteeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
                foreach ($table->fetchAll($select) as $page) {
                    if ($sitepageeventEnabled) {
                        //FETCH Notes CORROSPONDING TO THAT Page ID
                        $sitepageeventtable = Engine_Api::_()->getItemTable('sitepageevent_event');
                        $select = $sitepageeventtable->select()
                                ->from($sitepageeventtable->info('name'), 'event_id')
                                ->where('page_id = ?', $page->page_id);
                        $rows = $sitepageeventtable->fetchAll($select)->toArray();
                        if (!empty($rows)) {
                            foreach ($rows as $key => $event_ids) {
                                $event_id = $event_ids['event_id'];
                                if (!empty($event_id)) {
                                    $sitepageeventtable->update(array(
                                        'search' => '0'
                                            ), array(
                                        'event_id =?' => $event_id
                                    ));
                                }
                            }
                        }
                    }

                    if ($siteeventEnabled) {
                        //FETCH Notes CORROSPONDING TO THAT Page ID
                        $siteeventtable = Engine_Api::_()->getItemTable('siteevent_event');
                        $select = $siteeventtable->select()
                                ->from($siteeventtable->info('name'), 'event_id')
                                ->where('parent_type = ?', 'sitepage_page')
                                ->where('parent_id = ?', $page->page_id);
                        $rows = $siteeventtable->fetchAll($select)->toArray();
                        if (!empty($rows)) {
                            foreach ($rows as $key => $event_ids) {
                                $event_id = $event_ids['event_id'];
                                if (!empty($event_id)) {
                                    $siteeventtable->update(array(
                                        'search' => '0'
                                            ), array(
                                        'event_id =?' => $event_id
                                    ));
                                }
                            }
                        }
                    }
                }
            }

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
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    //ACTION FOR SHOW THE PACKAGE DETAILS
    public function packgeDetailAction() {
        $id = $this->_getParam('id');
        if (empty($id)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $this->view->package = Engine_Api::_()->getItem('sitepage_package', $id);
    }

    //ACTION FOR PACKAGE UPDATION
    public function updateAction() {

        //CHECK POST
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            $values = $_POST;
            try {
                foreach ($values['order'] as $key => $value) {

                    $package = Engine_Api::_()->getItem('sitepage_package', (int) $value);
                    if (!empty($package)) {
                        $package->order = $key + 1;
                        $package->save();
                    }
                }
                $db->commit();
                $this->_helper->redirector->gotoRoute(array('action' => 'index'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    //ACTION FOR MAKE PACKAGES ENABLE/DISABLE
    public function enabledAction() {
        $id = $this->_getParam('id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $package = Engine_Api::_()->getItem('sitepage_package', $id);
        if ($package->enabled == 0) {
            try {
                $package->enabled = 1;
                $package->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_redirect('admin/sitepage/package');
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
      // Action for admin side subscription-templates page. Displays various default and admin-created templates to choose from.
	public function subscriptionTemplatesAction()
	{

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_templates');

        $this->view->templates = $templates = Engine_Api::_()->getDbtable('templates','sitepage')->getTemplates();
	}
            // Action for admin side add-template page. Lets admin create a new template using any basic layout templates.
    public function addTemplateAction()
    {
                
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_templates');
        
        $layout = ($this->_getParam('layout_id') !== null) ? $this->_getParam('layout_id') : $this->_getParam('layout');

        $isAjaxRequest = $this->_getParam('ajax');
        $this->view->form = $form = new Sitepage_Form_Admin_Addtemplate(array('layoutId' => $layout, ));

        if (!empty($isAjaxRequest)) {
            $this->view->form = $form = new Sitepage_Form_Admin_Addtemplate(array('layoutId' => $layout, ));
            return;
        }

        if( !$this->getRequest()->isPost() ) {
          return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $filledData = $form->getValues();
            $form->populate($filledData);
            foreach ($filledData as $key => $value) {
              if ($form->$key->getDecorator('ViewScript'))
                $form->$key->getDecorator('ViewScript')->setOption('value',str_replace("#", "", $value));
            }
            return;
        }


        $formData = $form->getValues();

        // Set image url in form fields
        if (isset($formData['tick_image']) && $formData['tick_image'] != '0') {
            $logo_id = $this->setPhoto($form->tick_image);
            $formData['tick_image'] = Engine_Api::_()->storage()->get($logo_id)->getPhotoUrl();
        } else
            $formData['tick_image'] = 'application/modules/Sitepage/externals/images/tick_image.png';

        if (isset($formData['cross_image']) && $formData['cross_image'] != '0') {
            $logo_id = $this->setPhoto($form->cross_image);
            $formData['cross_image'] = Engine_Api::_()->storage()->get($logo_id)->getPhotoUrl();
        } else
            $formData['cross_image'] = 'application/modules/Sitepage/externals/images/cross_image.png';

        if (isset($formData['bestchoice_image']) && $formData['bestchoice_image'] != '0') {
            $logo_id = $this->setPhoto($form->bestchoice_image);
            $formData['bestchoice_image'] = Engine_Api::_()->storage()->get($logo_id)->getPhotoUrl();
        } else
            $formData['bestchoice_image'] = 'application/modules/Sitepage/externals/images/bestchoice_image.png';

        $params['template_name'] = $formData['template_name'];
        $params['layout'] = $formData['layout'];

        $fieldsJSON = Engine_Api::_()->getDbtable('templates','sitepage')->getFieldsJSON($params['layout']);

        $fieldsJSON = json_decode($fieldsJSON,true);

        foreach ($fieldsJSON as $key => $value) {
            $fieldsJSON[$key]['value'] = str_replace("#", "", $formData[$key]);
        }

        $params['template_values'] = json_encode($fieldsJSON);
        Engine_Api::_()->getDbtable('templates','sitepage')->createTemplate($params);

        $this->_helper->redirector->gotoRoute(array('module' => 'sitepage', 'controller' => 'package', 'action' => 'subscription-templates'), '', true);
    }
    // Action for activating a selected template. Nested in admin-side sitepage-templates page.
    public function activateTemplateAction()
    {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->template_id =  $this->_getParam('template_id');
        if( !$this->getRequest()->isPost() ) {
          return;
        }

        $id =  $this->_getParam('template_id');
        
        // Activate the choosen template
        $fieldTable = Engine_Api::_()->getDbtable('templates','sitepage')->activateTemplate($id);

        return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
        ));
    }
    // Action for admin side edit-template page. Lets admin edit the created templates.
    public function editTemplateAction()
    {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_templates');
                
        $id = $this->_getParam('template_id');
        // Make form
        $this->view->form = $form = new Sitepage_Form_Admin_Addtemplate(array('templateId' => $id));
        $this->view->template_id = $id;

        if( !$this->getRequest()->isPost() ) {
          return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $filledData = $form->getValues();
            $form->populate($filledData);
            foreach ($filledData as $key => $value) {
              if ($form->$key->getDecorator('ViewScript'))
                $form->$key->getDecorator('ViewScript')->setOption('value',str_replace("#", "", $value));
            }
            return;
        }

        $formData = $form->getValues();

        // Set image url in form fields
        if (isset($formData['tick_image']) && $formData['tick_image'] != '0') {
            $logo_id = $this->setPhoto($form->tick_image);
            $formData['tick_image'] = Engine_Api::_()->storage()->get($logo_id)->getPhotoUrl();
        } else
            $formData['tick_image'] = 'application/modules/Sitepage/externals/images/tick_image.png';

        if (isset($formData['cross_image']) && $formData['cross_image'] != '0') {
            $logo_id = $this->setPhoto($form->cross_image);
            $formData['cross_image'] = Engine_Api::_()->storage()->get($logo_id)->getPhotoUrl();
        } else
            $formData['cross_image'] = 'application/modules/Sitepage/externals/images/cross_image.png';

        if (isset($formData['bestchoice_image']) && $formData['bestchoice_image'] != '0') {
            $logo_id = $this->setPhoto($form->bestchoice_image);
            $formData['bestchoice_image'] = Engine_Api::_()->storage()->get($logo_id)->getPhotoUrl();
        } else
            $formData['bestchoice_image'] = 'application/modules/Sitepage/externals/images/bestchoice_image.png';

        $fieldsJSON = Engine_Api::_()->getDbtable('templates','Sitepage')->getFieldsJSON($id,0);
        $fieldsJSON = json_decode($fieldsJSON,true);

        foreach ($fieldsJSON as $key => $value) 
            $fieldsJSON[$key]['value'] = str_replace("#", "", $formData[$key]);

        $params['template_id'] = $id;
        $params['template_values'] = json_encode($fieldsJSON);
        Engine_Api::_()->getDbtable('templates','sitepage')->setValues($params);

        // Repopulate form with updated values
        $templateData = Engine_Api::_()->getDbtable('templates','Sitepage')->getTemplate($id);
        $fieldsJSON = json_decode($templateData['template_values'],true);
        foreach ($fieldsJSON as $key => $value) {
          $form->$key->setValue[$value['value']];
          if ($form->$key->getDecorator('ViewScript'))
            $form->$key->getDecorator('ViewScript')->setOption('value',$value['value']);
        }
        $this->view->form = $form;
    }
    // Action for deleting templates created by admin. Nested in admin-side sitepage-templates page.
    public function deleteTemplateAction()
    {
        $parent = $this->view->parent_temp;
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->template_id =  $this->_getParam('template_id');

        if( !$this->getRequest()->isPost() ) {
          return;
        }

        $id =  $this->_getParam('template_id');
        $parent_id = 5;
        $fieldTable = Engine_Api::_()->getDbtable('templates','sitepage')->activateTemplate($parent_id);
        // Delete Entries from template table
        $fieldTable = Engine_Api::_()->getDbtable('templates','sitepage');
        $field = $fieldTable->find($id)->current();

        $db = $fieldTable->getAdapter();
        $db->beginTransaction();

        try {
            $field->delete();
            $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
        ));
    }
    // Action for previewing the current edited data-fields or styles. Nested in admin-side subscription-fields, edit-templates, create-template page.
    public function previewAction()
    {
        // Make form
        $this->view->form = $form = new Sitepage_Form_Admin_Subscription();

        $this->view->formError = 0;

        $previewType = $this->getParam('previewtype');
        $template_id = $this->getParam('template_id');
        
        $baseFolder = APPLICATION_PATH . '/public/sitepage/';
        if(!is_writable($baseFolder)){

            mkdir($baseFolder);
        }

        $fileUrl = $baseFolder.trim($previewType).'-data.json';

        if (!is_writable($fileUrl)) {
            $this->view->formError = 1;
            return;
        }

        $content = json_decode(file_get_contents($fileUrl),true);

        if ($previewType == 'fields' && !empty($content)) {

          $flag = $content['structure'];
          $templateData = Engine_Api::_()->getApi('core','sitepage')->getTemplateData();
          unset($content['structure']);
          $newContent = $this->getSortedValues($content);

          foreach ($newContent as $key => $value) {
            $temp1 = explode("_", $key);
            if ($temp1[2] === 'name') {
              $temp2[$temp1[0]]['field_id'] = $temp1[0];
              $temp2[$temp1[0]]['label'] = $value;
            } else if ($temp1[2] === 'selectbox' && $flag == '1') {
              if ($value === 'textbox')
                $fieldValues[$temp1[0]][$temp1[1]] = $newContent[$temp1[0]."_".$temp1[1]."_textbox"];
              else
                $fieldValues[$temp1[0]][$temp1[1]] = $value;
            } else if ($temp1[2] === 'textbox' && $flag == '0' ) {
                $fieldValues[$temp1[0]][$temp1[1]] = $value;
            }
          }

          if ($templateData['structureType'] === 'type2' && $temp2 == null) {
            foreach ($fieldValues as $key => $value) 
              $temp2[$key]= array('field_id' => $key, 'label' => null);
          }

          foreach ($temp2 as $value) 
            $features[] = $value;

          $templateData['features']  = $features;
          $templateData['fieldValues'] = $fieldValues;
          
        } else if ($previewType == 'styles' && !empty($content)) {
          $row = Engine_Api::_()->getDbtable('templates','sitepage')->getTemplate($template_id);
          if (empty($row))
            return 'Cannot display preview for this template';

          $templateData = Engine_Api::_()->getApi('core','sitepage')->getTemplateData($template_id);
          foreach ($content as $key => $value) 
            $temp[$key]['value'] = $value;
          $templateData['templateStyle'] = $temp;
          $templateData['layout_id'] = $row['layout'];
        }

        $this->view->form->setTitle('');
        $this->view->form->setAction('');
        $this->view->form->setDescription('');
        $this->view->templateData = $templateData;
        $this->view->layout_id = $templateData['layout_id'];
        $this->view->check = $check = '1';

        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 5,
          'parentRefresh' => false,
          'format'=> 'smoothbox',
        ));
    }

    // Action for writing the Json file. Called via ajax-request on preview.
    public function writePreviewFileAction() 
    {
        $content = $this->getParam('content');
        $content = str_replace("#", "", $content);
        $previewType = $this->getParam('previewtype');
        $data = array('return' => 0, 'message' => 'Writing file failed.');
        $baseFolder = APPLICATION_PATH . '/public/sitepage/';
        if(!is_writable($baseFolder)){

            mkdir($baseFolder);
        }

        $fileUrl = $baseFolder.trim($previewType).'-data.json';

        $handle = fopen($fileUrl, 'w');
        $status = fwrite($handle,$content);
        fclose($handle);

        if (!$handle) 
            $data['message'] = 'Error occured while fetching file. Check file permissions of '.APPLICATION_PATH.'/public folder .';
        else if (!$status) 
            $data['message'] = 'Error occured while writing file.Check file permissions of '.APPLICATION_PATH.'/public folder.';
        else
            $data = array('return' => 1, 'message' => 'Preview file successfuly written.');

        return $this->_helper->json($data);
    }
    // Action for admin side subscription-layouts page. Displays various default and admin-created layouts to choose from.
    public function manageLayoutsAction()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_layouts');

        $this->view->layouts = $layouts = Engine_Api::_()->getDbtable('layouts','sitepage')->getLayouts();
    }
    // Action for admin side add-layout page. Lets admin create a new layout using any basic layout templates.
    public function addLayoutAction()
    {
	$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_layouts');


        $layout = $this->_getParam('layout_id') ? $this->_getParam('layout_id') : $this->_getParam('layout');

        $this->view->form = $form = new Sitepage_Form_Admin_Addlayout();

        if( !$this->getRequest()->isPost() ) {
          return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $filledData = $form->getValues();
            $form->populate($filledData);
            foreach ($filledData as $key => $value) {
              if ($form->$key->getDecorator('ViewScript'))
                $form->$key->getDecorator('ViewScript')->setOption('value',$value);
            }
            return;
        }

        $formValues = $form->getValues();

        $baseLayout = Engine_Api::_()->getDbtable('layouts','sitepage')->getLayout($formValues['layout']);

        $newLayout['layout_name'] = $formValues['layout_name'];
        $newLayout['structure_type'] = $baseLayout['structure_type'];

        // insert data in layouts table
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();
        try {
            
            // Create template file
            $folderPath = APPLICATION_PATH."/application/modules/Sitepage/views/scripts/layouts/";
            if (!is_writable($folderPath)) {
                $form->addNotice('Unable to create layout. '.$folderPath.' is not writable. Assign permissions to folder and then continue.');
                return;
            }
            $newLayoutId = Engine_Api::_()->getDbtable('layouts','sitepage')->createLayout($newLayout);
            // Get file data
            $templateFileData = file_get_contents($folderPath.'_plansTemplate_'.$formValues['layout'].'.tpl');

            // Save file
            $handle = fopen($folderPath.'_plansTemplate_'.trim($newLayoutId).'.tpl','w+');
            fwrite($handle,$templateFileData);
            fclose($handle);
            chmod($folderPath.'_plansTemplate_'.trim($newLayoutId).'.tpl', 0777);

            // insert data in templates table
            $baseLayoutTemplate = Engine_Api::_()->getDbtable('templates','sitepage')->getBaseTemplate($formValues['layout']);

            $newLayoutTemplate['template_name'] = $formValues['layout_name'].' - Template';
            $newLayoutTemplate['template_values'] = $baseLayoutTemplate['template_values'];
            $newLayoutTemplate['layout'] = $newLayoutId;
            $newLayoutTemplate['default'] = 1;

            Engine_Api::_()->getDbtable('templates','sitepage')->createTemplate($newLayoutTemplate);

            $form->addNotice('Layout Created Successfully.<br> File Path: /application/modules/sitepage/views/scripts/layouts/_plansTemplate_'.trim($newLayoutId).'.tpl');
            $form->reset();
        
        } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
// Action for deleting layouts created by admin. Nested in admin-side sitepage-layouts page.
    public function deleteLayoutAction()
    {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->layout_id =  $this->_getParam('layout_id');

        if( !$this->getRequest()->isPost() ) {
          return;
        }

        $id = $this->_getParam('layout_id');
        $field = Engine_Api::_()->getDbtable('templates','sitepage')->customActiveTemplate($id);
        if(!empty($field)) {
            $parent_id = 5;
            $fieldTable = Engine_Api::_()->getDbtable('templates','sitepage')->activateTemplate($parent_id);
        }
        // Delete Entries from layouts table
        $layoutsTable = Engine_Api::_()->getDbtable('layouts','sitepage');
        $db = $layoutsTable->getAdapter();
        $db->beginTransaction();

        try {
            $layoutsTable->delete(array('layout_id = ?' => $id));
            $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }

        // Delete entries from templates table
        $templatesTable = Engine_Api::_()->getDbtable('templates','sitepage');
        $db = $templatesTable->getAdapter();
        $db->beginTransaction();

        try {
            $templatesTable->delete(array('layout = ?' => $id));
            $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }

        // Remove files
        $filePath = APPLICATION_PATH."/application/modules/Sitepage/views/scripts/layouts/_plansTemplate_".$id.".tpl";
        if (!is_writable($filePath)) {
            echo "<div class='tip'><span><?php echo $this->translate('There was an error in file deletion. Required permissions are not assigned to layout file.'); ?></span></div>";
        } else
            unlink($filePath);
        

        return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
        ));
    } 
    // Action for admin side subscription-fields page. Sets data values for template form.
	public function subscriptionFieldsAction()
	{
        		
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_field');

        $templateData = Engine_Api::_()->getApi('core','sitepage')->getTemplateData();
                
        foreach ($templateData['featuresRowData'] as $value) {
          $features[$value['field_id']]['order'] = $value['feature_order'];
          $features[$value['field_id']]['label'] = $value['feature_label'];
        }

        // unset disabled packages from package order
        foreach ($templateData['packageOrder'] as $package_id => $order) {
            if (!$templateData['packages'][$package_id]['enabled']) {
                unset($templateData['packageOrder'][$package_id]);
            }
        }

        $this->view->isFeatureEnabled = $templateData['isFeatureEnabled'];
        $this->view->structureType = $templateData['structureType'];
        $this->view->packages = $templateData['packages'];
        $this->view->packageOrder = $templateData['packageOrder'];
        $this->view->features = $features;
        $this->view->fieldValues = $templateData['fieldValues']; 

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $params = $_POST;
        $status = $this->setFieldValues($params);

        return $this->_helper->redirector->gotoRoute(array('action' => 'subscription-fields'));
	}
        // Action for deleting feature data rows. Nested in admin-side subscription-fields page. 
    public function deleteFieldAction()
    {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->field_id =  $this->_getParam('field_id');

        if( !$this->getRequest()->isPost() ) {
          return;
        }

        $id =  $this->_getParam('field_id');

        // Delete Entries from fields table
        $fieldTable = Engine_Api::_()->getDbtable('fields','sitepage');
        $field = $fieldTable->find($id)->current();

        $db = $fieldTable->getAdapter();
        $db->beginTransaction();

        try {
            $field->delete();
            $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }

        // Delete entries from values table
        Engine_Api::_()->getDbtable('values','sitepage')->deleteFieldValues($id);

        return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
        ));
    }

    public function getSortedValues($content = null)
    {
      if ($content == null) 
        return;

      $pseudo_ids = $this->getSortedIds(array_keys($content));

      if ($pseudo_ids == null) 
        return $content;

      foreach ($content as $key => $value) {
        $temp = explode('_', $key);

        if (strpos($temp[0], 'new') === 0)
          $temp[0] = $pseudo_ids[$temp[0]];

        $new_key = implode('_', $temp);
        $new_content[$new_key] = $value;
      }

      return $new_content;
    }

    public function getSortedIds($array = null) {
      if (empty($array)) 
        return null;

      foreach ($array as $id) {
        $temp = explode('_', $id);

        if (strpos($temp[0], 'new') !== 0)
          $id_array[] = $temp[0];
        else if (strpos($temp[0], 'new') === 0)
          $newIdsArray[] = $temp[0];
      }

      $max_id = max($id_array);
      $uniqueNewIds = array_unique($newIdsArray);

      foreach ($uniqueNewIds as $value) {
        $pseudo_ids[$value] = ++$max_id;
      }
      
      return $pseudo_ids;
    }
    public function sortFormValues($params)
    {
      $values_arr = array();

      foreach ($params as $key => $value) {
        $arr = explode("_", $key);

        if (strpos($key, 'new') === 0) {
          if( $arr[1] == 'field' )
            $new_fields_arr[$arr[0]]['name'] = $value; 
          else if ( $value == 'textbox' )
            $new_fields_arr[$arr[0]][$arr[1]] = $this->convertFromMarkdownFormat($params[$arr[0].'_'.$arr[1].'_textbox']);
          else
            $new_fields_arr[$arr[0]][$arr[1]] = $this->convertFromMarkdownFormat($value);
          continue;
        }

        if ( $arr[1] == 'field' )
          $fields_arr[$arr[0]] = $value;
        else if ( $value == 'textbox' )
          $values_arr[$arr[0]][$arr[1]] = $this->convertFromMarkdownFormat($params[$arr[0].'_'.$arr[1].'_textbox']);
        else
          $values_arr[$arr[0]][$arr[1]] = $this->convertFromMarkdownFormat($value); 
      }

      $return_arr['fields'] = $fields_arr;
      $return_arr['values'] = $values_arr;
      $return_arr['new'] = $new_fields_arr;
     
      return $return_arr;
    }

    public function convertFromMarkdownFormat($string = null)
    {
      if (empty($string)) 
        return "";

      $markdown_array = array("**" => "\*\*","~~" => "\~\~","__" => "__");

      foreach ($markdown_array as $markdown => $replacement) {
        $count = substr_count($string,$markdown);
        if ($count > 0) 
          for ($i=1; $i <= $count ; $i++) { 
            $string = preg_replace("/".$replacement."/", $this->getTag($markdown,$i), $string ,1);
          }
      }
      return $string;
    }

    public function getTag($markdown,$pos)
    {
      switch ($markdown) {
        case '**':
          $tag = "b>";
          break;

        case '~~':
          $tag = "i>";
          break;

        case '__':
          $tag = "strike>";
          break;
        
        default:
          break;
      }

      if ( ((int)$pos%2) == 0)
        $tag = '</'.$tag;
      else
        $tag = '<'.$tag;

      return $tag;
    }


    public function convertToMarkdownFormat($string = null)
    {
      if (empty($string)) 
        return "";

      $tags_array = array("**"=> array("<b>","<\/b>"),"~~" => array("<i>","<\/i>"), "__" => array("<strike>","<\/strike>"));
      foreach ($tags_array as $key => $value) {
        $string = preg_replace("/(".$value[0]."|".$value[1].")/i", $key, $string);
      }

      return $string;
    }
    public function setFieldValues($params)
    {
      $structure_type = ($params['structure'] == 0) ? '2' : '1';
      unset($params['structure']);
      unset($params['save']);

      $sorted_arr = $this->sortFormValues($params);

      $field_arr = $sorted_arr['fields'];
      $values_arr = $sorted_arr['values'];
      $new_fields_arr = $sorted_arr['new'];

      $status1 = Engine_Api::_()->getDbTable('fields','sitepage')->updateFields($field_arr);
      $status2= Engine_Api::_()->getDbTable('values','sitepage')->updateValues($values_arr);
      $status3 = $this->addNewFeature($new_fields_arr,$structure_type);

      return true;
    }
    public function addNewFeature($new_fields_arr,$structure_type) 
    {
     if (empty($new_fields_arr)) 
       return;

     foreach ($new_fields_arr as $field => $values) {
       $field_id = Engine_Api::_()->getDbTable('fields','sitepage')->addNewField(array(
         'feature_label' =>  $values['name'],
         'structure_type' => 'type'.$structure_type,
         ));

       unset($new_fields_arr[$field]['name']);
       $setValue[$field_id] = $new_fields_arr[$field];
       $status = Engine_Api::_()->getDbTable('values','sitepage')->updateValues($setValue);
     }
    }
    // Action for admin-side synchronous page.
    public function synchronousAction()
    {
	$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_synchronous');
    }
    
    public function synchronousConfirmationAction() 
    {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_synchronous');
        if ($this->getRequest()->getPost()) {

          if ((!empty($_POST['result']) && $_POST['result'] == 1)) {
                
                $template = Engine_Api::_()->getDbtable('templates','Sitepage')->getActivatedTemplate();
                //var_dump($template['layout']);die;
                $layout = Engine_Api::_()->getDbtable('layouts','Sitepage')->getLayoutStructureType($template['layout']);
                //var_dump($layout);die;
                try {
                    $prefield = Engine_Api::_()->getDbtable('fields','Sitepage')->removeField($layout);

                    $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.information');
                    $packageInfoArray = array('price' => 'Price', 'billing_cycle' => 'Billing Cycle', 'duration' => 'Package Expires In', 'featured' => 'Featured Package', 'sponsored' => 'Sponsored Package', 'tellafriend' => 'Tell a friend', 'print' => 'Print Page Information', 'overview' => 'Rich Overview', 'map' => 'Map', 'insights' => 'Insights', 'contactdetails' => 'Contact Details', 'sendanupdate' => 'Send Updates to Page Users', 'apps' => 'Apps available', 'description' => 'Description');
                    
                    foreach ($package as $key => $value) {
                        if ($value == 'price') {
                            continue;
                        }
                        if ($value == 'apps') {
                            continue;
                        }
                        foreach ($packageInfoArray as $key => $field) {
                            if ( $value == $key) {
                                if ($value == 'billing_cycle') {
                                    $value = 'recurrence';
                                }
                                if ($value == 'sendanupdate') {
                                    $value = 'sendupdate';
                                }
                                if ($value == 'contactdetails') {
                                    $value = 'contact_details';
                                }
                                $fieldname = $field;
                                break;
                            }
                            else {
                                continue;
                            }    
                        }

                        $labels = Engine_Api::_()->getDbTable('packages', 'sitepage')->getFilledLabel($value);
                        $count = 0;

                        foreach ($labels as $values) {
                            if($count == 0) {
                               $fieldOption = Engine_Api::_()->getDbTable('fields', 'sitepage')->addCustomField($fieldname,$layout);
                               $count++;
                            }
            
                           $getlable = Engine_Api::_()->getDbTable('layouts', 'sitepage')->changeLable($fieldname,$values[$value],$layout);
                           $resultset = Engine_Api::_()->getDbTable('values', 'sitepage')->addCustomValues($fieldOption,$values['package_id'],$getlable);
                           
                        }
                    }//die;
                    $showname = Engine_Api::_()->getDbTable('fields', 'sitepage')->changeOrder($layout);
                } catch (Exception $e) {
                $db->rollBack();
                throw $e;
                }
              $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,           
              'format' => 'smoothbox',
              'parentRedirect' => $this->view->url(array('module' => 'sitepage', 'controller' => 'package', 'action' => 'synchronous'), 'admin_default', true),
              'parentRedirectTime' => 15,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The data for your Page has been successfully synchronous.'))
            ));
          }
          
        }
    }
    // Action for admin-side FAQ page.
    public function helpAction()
    {
	$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_package');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main_package', array(), 'sitepage_admin_main_help');
    }
}

?>