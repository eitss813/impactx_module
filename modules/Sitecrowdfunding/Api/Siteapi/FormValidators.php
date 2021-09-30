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
class Sitecrowdfunding_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Validations of Create OR Edit Form.
     * 
     * @param object $subject get object
     * @param array $formValidators array variable
     * @return array
     */
    public function getFormValidators($type = 0, $subject = array(), $formValidators = array()) {

        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );

   
            $formValidators['category_id'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('Int', true)
                )
            );
        
        $formValidators['description'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true)
            )
        );

   
            $formValidators['starttime'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
            
            $formValidators['endtime'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
            
            $formValidators['goal_amount'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
            

        return $formValidators;
    }

    /**
     * Validation: user signup field form
     * 
     * @return array
     */
    public function getFieldsFormValidations($values, $table) {
        $option_id = $values['profile_type'];

        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($table);
        $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);
        $fieldArray = array();
        $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
        foreach ($getRowsMatching as $map) {
            $meta = $map->getChild();
            $type = $meta->type;
            $profileLabel = $meta->label;
            if (!empty($type) && ($type == 'heading'))
                continue;

            $fieldForm = $getMultiOptions = array();
            $key = $map->getKey();

            if (!empty($meta->alias))
                $key = $key . '_' . 'alias_' . $meta->alias;
            else {
                $key = $key . '_' . 'field_' . $meta->field_id;
            }

            if (isset($meta->required) && !empty($meta->required))
                $fieldArray[$key] = array(
                    'required' => true,
                    'label' => $profileLabel,
                    'allowEmpty' => false
                );

            if (isset($mets->validators) && !empty($mets->validators)) {
                $fieldArray[$key]['validators'] = $mets->validators;
            }
        }

        return $fieldArray;
    }
    
    public function rewardFormValidations(){
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );

   
            $formValidators['pledge_amount'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('Int', true)
                )
            );
        
        $formValidators['description'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true)
            )
        );

   
//            $formValidators['photo'] = array(
//                'required' => true,
//                'allowEmpty' => false,
//                'validators' => array(
//                   array('NotEmpty', true)
//                )
//            );
            
            $formValidators['delivery_date'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
            
            $formValidators['shipping_method'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
            

        return $formValidators;
    }
    
    public function getPayPalFormValidators(){
	$formValidators =array();
	$formValidators['email'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	
	$formValidators['username'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	$formValidators['password'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	
	$formValidators['signature'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	return $formValidators;
    }
    
    
    public function getMangoPayFormValidators(){
	$formValidators =array();
	$formValidators['first_name'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	
	$formValidators['last_name'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	$formValidators['mango_pay_email'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	
	$formValidators['birthday'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	
	$formValidators['nationality'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	
	$formValidators['residence'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	
	$formValidators['account_type'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	$formValidators['owner_name'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	$formValidators['owner_address'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	$formValidators['city'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	$formValidators['region'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );$formValidators['postal_code'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );$formValidators['country'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                   array('NotEmpty', true)
                )
            );
	    
	   return $formValidators;
	
    }

}

