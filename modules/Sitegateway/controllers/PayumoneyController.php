<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PaymentController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_PayumoneyController extends Core_Controller_Action_Standard {

    public function init() {

       $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $gatewayMethod = _GETGATEWAYMETHOD;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        if (empty($gatewayMethod))
            return $this->_helper->redirector->gotoRoute(array(), "default", true);

        if (((isset($params['source_type']) && $params['source_type'] != 'payment_subscription') || (isset($params['product_type']) && $params['product_type'] != 'payment_package')) && (!$viewer_id)) {
            return $this->_helper->redirector->gotoRoute(array(), "default", true);
        }
    }

    public function processAction() {

        $this->view->allParams = $allParams = $this->_getAllParams();
    }

    

}
