<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPackage.php 6590 2012-06-24 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_AdminPackageController extends Core_Controller_Action_Admin {

    public function createAction() {

        // Make form
        $this->view->form = $form = new Payment_Form_Admin_Package_Create();

        // Get supported billing cycles
        $gateways = array();
        $supportedBillingCycles = array();
        $partiallySupportedBillingCycles = array();
        $fullySupportedBillingCycles = null;
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');

//    foreach( $gatewaysTable->fetchAll(/*array('enabled = ?' => 1)*/) as $gateway ) {
//      $gateways[$gateway->gateway_id] = $gateway;
//      $supportedBillingCycles[$gateway->gateway_id] = $gateway->getGateway()->getSupportedBillingCycles();
//      $partiallySupportedBillingCycles = array_merge($partiallySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
//      if( null === $fullySupportedBillingCycles ) {
//        $fullySupportedBillingCycles = $supportedBillingCycles[$gateway->gateway_id];
//      } else {
//        $fullySupportedBillingCycles = array_intersect($fullySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
//      }
//    }
//    
//    $partiallySupportedBillingCycles = array_diff($partiallySupportedBillingCycles, $fullySupportedBillingCycles);
//
//    $multiOptions = /* array(
//      'Fully Supported' =>*/ array_combine(array_map('strtolower', $fullySupportedBillingCycles), $fullySupportedBillingCycles)/*,
//      'Partially Supported' => array_combine(array_map('strtolower', $partiallySupportedBillingCycles), $partiallySupportedBillingCycles),
//    )*/;
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
                ->setMultiOptions($multiOptions)
        //->setDescription('-')
        ;
        $form->getElement('recurrence')->options/* ['Fully Supported'] */['forever'] = 'One-time';

        $form->getElement('duration')
                ->setMultiOptions($multiOptions)
        //->setDescription('-')
        ;
        $form->getElement('duration')->options/* ['Fully Supported'] */['forever'] = 'Forever';

        /*
          $form->getElement('trial_duration')
          ->setMultiOptions($multiOptions)
          //->setDescription('-')
          ;
          $form->getElement('trial_duration')->options['Fully Supported']['forever'] = 'None';
          //$form->getElement('trial_duration')->setValue('0 forever');
         * 
         */

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }


        // Process
        $values = $form->getValues();

        $tmp = $values['recurrence'];
        unset($values['recurrence']);
        if (empty($tmp) || !is_array($tmp)) {
            $tmp = array(null, null);
        }
        $values['recurrence'] = (int) $tmp[0];
        $values['recurrence_type'] = $tmp[1];

        $tmp = $values['duration'];
        unset($values['duration']);
        if (empty($tmp) || !is_array($tmp)) {
            $tmp = array(null, null);
        }
        $values['duration'] = (int) $tmp[0];
        $values['duration_type'] = $tmp[1];

        /*
          $tmp = $values['trial_duration'];
          unset($values['trial_duration']);
          if( empty($tmp) || !is_array($tmp) ) {
          $tmp = array(null, null);
          }
          $values['trial_duration'] = (int) $tmp[0];
          $values['trial_duration_type'] = $tmp[1];
         * 
         */

        if (!empty($values['default']) && (float) $values['price'] > 0) {
            return $form->addError('Only a free plan may be the default plan.');
        }


        $packageTable = Engine_Api::_()->getDbtable('packages', 'payment');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {

            // Update default
            if (!empty($values['default'])) {
                $packageTable->update(array(
                    'default' => 0,
                        ), array(
                    '`default` = ?' => 1,
                ));
            }

            // Create package
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

        // Redirect
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

}
