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
class Sitepage_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Validation: user signup field form
     * 
     * @return array
     */
    public function getFieldsFormValidations($values) {

        $option_id = $values['profile_type'];
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitepage_page');
        $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);
        $fieldArray = array();
        $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
        foreach ($getRowsMatching as $map) {
            $meta = $map->getChild();
            $type = $meta->type;
            $label = $meta->label;
            if (!empty($type) && ($type == 'heading'))
                continue;

            //if (!isset($meta->show) || empty($meta->show))
            //continue;

            $fieldForm = $getMultiOptions = array();
            $key = $map->getKey();

            $key = $key . '_field_' . $meta->field_id;

            if (isset($meta->required) && !empty($meta->required))
                $fieldArray[$key] = array(
                    'required' => true,
                    'label' => $label,
                    'allowEmpty' => false
                );

            if (isset($mets->validators) && !empty($mets->validators)) {
                $fieldArray[$key]['validators'] = $mets->validators;
            }
        }
        return $fieldArray;
    }

    /**
     * Validations of Create OR Edit Form.
     * 
     * @param object $subject get object
     * @param array $formValidators array variable
     * @return array
     */
    public function getFormValidators($subject = array(), $formValidators = array()) {
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );

        $formValidators['body'] = array(
            'required' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.bodyrequired', 1) ? true : false,
            'allowEmpty' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.bodyrequired', 1) ? false : true,
        );
        
        $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);

        if (count($categories) != 0) {
            $formValidators['category_id'] = array(
                'required' => true,
                'allowEmpty' => false
            );
        }
        $formValidators['price'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        );
        return $formValidators;
    }

    public function getMessageOwnerFormValidators() {
        $formValidators = array();

        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );

        $formValidators['body'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        return $formValidators;
    }

    public function tellaFriendFormValidators() {
        $formValidators = array();

        $formValidators['sender_name'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        $formValidators['sender_email'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        $formValidators['receiver_emails'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        $formValidators['message'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        return $formValidators;
    }

}
