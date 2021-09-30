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
class Sitevideo_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

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

        $formValidators['description'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true)
            )
        );

        if ($type == 0)
            $formValidators['type'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('Int', true)
                )
            );

        return $formValidators;
    }

    public function getChannelFormValidators($type = 0, $subject = array(), $formValidators = array()) {

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

        if ($type == 0)
            $formValidators['channel_uri'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    array('StringLength', false, array(3, 255))
                )
            );

        return $formValidators;
    }

    public function getPlaylistFormValidators() {
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );

        $formValidators['description'] = array(
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

}
