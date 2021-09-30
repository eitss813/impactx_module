<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FormValidators.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagealbum_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {
    
    public function albumOfDayValidators()
    {
        $formValidators['startdate'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        
        $formValidators['enddate'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
    }
    
}