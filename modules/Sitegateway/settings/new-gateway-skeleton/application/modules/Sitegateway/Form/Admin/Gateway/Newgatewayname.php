<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Newgatewayname.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Form_Admin_Gateway_Newgatewayname extends Payment_Form_Admin_Gateway_Abstract {

    public function init() {
        parent::init();

        $this->setTitle('Payment Gateway: Newgatewayname');

        $description = $this->getTranslator()->translate('Add steps to get the gateway credentials.');
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        /*
         * Add the code to create a form for gateway credentials input.
         */
    }

}
