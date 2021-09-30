<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {
    /*
     * Create video form validators
     * 
     * @return array
     */

    public function createformvalidators() {
        
        $formValidators = array();
        
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('string', true)
            )
        );
        
        $formValidators['type'] = array(
            'required' => true,
        );
        
        return $formValidators;
        
    }

    /*
     * Comment validation form
     *
     * @return array
     */

    public function getcommentValidation() {

        $formValidators['body'] = array(
            'required' => true,
        );

        return $formValidators;
    }

    public function editformvalidators() {
        
        $formValidators = array();
        
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('string', true)
            )
        );
        
        return $formValidators;

    }

}
