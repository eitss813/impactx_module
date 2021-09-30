<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9910 2013-02-14 19:22:15Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Yndynamicform
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Yndynamicform_Api_Core extends Core_Api_Abstract
{


    /**
     * @var array Contains information about the various field types
     */
    protected $_fieldTypeInfo;

    public function getFieldInfo($type = null, $value = null)
    {
        if (null === $this->_fieldTypeInfo) {
            $this->_fieldTypeInfo = include APPLICATION_PATH . '/application/modules/Yndynamicform/settings/fields.php';
        }

        switch ($type) {
            case null:
                return $this->_fieldTypeInfo;
                break;
            case 'categories':
                return $this->_fieldTypeInfo['categories'];
                break;
            case 'fields':
                return $this->_fieldTypeInfo['fields'];
                break;
            case 'dependents':
                return $this->_fieldTypeInfo['dependents'];
                break;
            case 'advanced_fields':
                return $this->_fieldTypeInfo['advanced_fields'];
                break;
            case 'user_analytics_fields':
                return $this->_fieldTypeInfo['user_analytics_fields'];
                break;
        }

        // Get base field info
        if (isset($this->_fieldTypeInfo['fields'][$type])) {
            $info = $this->_fieldTypeInfo['fields'][$type];
            if (!empty($info['base']) && !empty($this->_fieldTypeInfo['fields'][$info['base']])) {
                $info = array_merge($this->_fieldTypeInfo['fields'][$info['base']], $info);
            }
            if (null !== $value) {
                if (isset($info[$value])) {
                    return $info[$value];
                }
            } else {
                return $info;
            }
        }

        return null;
    }

    public function getItemTable($type)
    {
        if ($type == 'yndynamicform_form') {
            return Engine_Loader::getInstance()->load('Yndynamicform_Model_DbTable_Forms');
        } else
            if ($type == 'yndynamicform_category') {
                return Engine_Loader::getInstance()->load('Yndynamicform_Model_DbTable_Categories');
            } else {
                $class = Engine_Api::_()->getItemTableClass($type);
                return Engine_Api::_()->loadClass($class);
            }
    }

    public function typeCreate($label)
    {
        $field = Engine_Api::_()->fields()->getField('1', 'yndynamicform_entry');
        // Create new blank option
        $option = Engine_Api::_()->fields()->createOption('yndynamicform_entry', $field, array(
            'field_id' => $field->field_id,
            'label' => $label,
        ));
        // Get data
        $mapData = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry');
        $metaData = Engine_Api::_()->fields()->getFieldsMeta('yndynamicform_entry');
        $optionData = Engine_Api::_()->fields()->getFieldsOptions('yndynamicform_entry');
        // Flush cache
        $mapData->getTable()->flushCache();
        $metaData->getTable()->flushCache();
        $optionData->getTable()->flushCache();

        return $option->option_id;
    }

    public function getFormByOptionId($option_id)
    {
        if ($option_id && is_numeric($option_id)) {
            $table = Engine_Api::_()->getDbTable('forms', 'yndynamicform');
            $select = $table->select()->where('option_id = ?', $option_id)->limit(1);
            return $table->fetchRow($select);
        } else {
            return null;
        }
    }

    public function getConditionalMultiOptions($type = null)
    {
        if (!$type)
            return array();
        return $this->{'getConditionalMultiOptionsFor' . ucfirst($type)}();
    }

    public function getConditionalMultiOptionsForCountry()
    {
        $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
        asort($territories);
        //if( !$this->isRequired() ) {
        $territories = array_merge(array(
            '' => '',
        ), $territories);
        //}
        return $territories;
    }

    public function getParamsConditionalLogic($yndform, $isCreate)
    {
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
        $arrConditionalLogic = array();
        $arrErrorMessage = array();
        $arrFieldIds = array();
        $secondLevelMaps = $mapData->getRowsMatching('option_id', $yndform->option_id);
        $pageBreak = 0;
        if (!empty($secondLevelMaps)) {
            foreach ($secondLevelMaps as $map) {
                $secondLevelFields[$map->child_id] = $map->getChild();
                $fieldId = '1_' . $yndform->option_id . '_' . $map->child_id;
                if ($secondLevelFields[$map->child_id]->type == 'page_break')
                    $pageBreak++;
                if (!empty($secondLevelFields[$map->child_id]->config['conditional_logic'])) {
                    $arrConditionalLogic[$fieldId]['conditional_logic'] = $secondLevelFields[$map->child_id]->config['conditional_logic'];
                    $arrConditionalLogic[$fieldId]['conditional_show'] = $secondLevelFields[$map->child_id]->config['conditional_show'];
                    $arrConditionalLogic[$fieldId]['conditional_scope'] = $secondLevelFields[$map->child_id]->config['conditional_scope'];
                    $arrConditionalLogic[$fieldId]['conditional_enabled'] = $secondLevelFields[$map->child_id]->config['conditional_enabled'];
                    if ($secondLevelFields[$map->child_id] -> error) $arrErrorMessage[$fieldId] = $secondLevelFields[$map->child_id] -> error;
                }
                $arrFieldIds[$map->order] = $fieldId;
            }
        }

        if ($isCreate) {
            $arrFieldIds[] = 'submit_button';
            $arrConditionalLogic['submit_button']['conditional_logic'] = json_decode($yndform->conditional_logic);
            $arrConditionalLogic['submit_button']['conditional_show'] = $yndform->conditional_show;
            $arrConditionalLogic['submit_button']['conditional_scope'] = $yndform->conditional_scope;
            $arrConditionalLogic['submit_button']['conditional_enabled'] = $yndform->conditional_enabled;
        }

        return array(
            'arrConditionalLogic' => $arrConditionalLogic,
            'arrFieldIds' => $arrFieldIds,
            'pageBreak' => $pageBreak,
            'arrErrorMessage' => $arrErrorMessage,
        );
    }

    public function getConditionalLogicConfirmations($yndform_id)
    {
        // Save conditional logic for confirmation
        $confirmations = Engine_Api::_()->getDbTable('confirmations', 'yndynamicform')->getConfirmations(array('status' => 1, 'form_id' => $yndform_id));
        $confConditionalLogic = array();
        $confOrder = array();
        if (count($confirmations) > 0) {
            foreach ($confirmations as $item) {
                $conf_id = $item->getIdentity();
                $confConditionalLogic[$conf_id]['conditional_logic'] = json_decode($item->conditional_logic);
                $confConditionalLogic[$conf_id]['conditional_enabled'] = $item->conditional_enabled;
                $confConditionalLogic[$conf_id]['conditional_show'] = $item->conditional_show;
                $confConditionalLogic[$conf_id]['conditional_scope'] = $item->conditional_scope;
                $confOrder[] = $conf_id;
            }
        }
        return array(
            'confOrder' => $confOrder,
            'confConditionalLogic' => $confConditionalLogic,
        );
    }

    public function getConditionalLogicNotifications($yndform_id)
    {
        // Save conditional logic for confirmation
        $notifications = Engine_Api::_()->getDbTable('notifications', 'yndynamicform')->getNotifications(array('status' => 1, 'form_id' => $yndform_id));
        $notiConditionalLogic = array();
        $notiOrder = array();
        if (count($notifications) > 0) {
            foreach ($notifications as $item) {
                $noti_id = $item->getIdentity();
                $notiConditionalLogic[$noti_id]['conditional_logic'] = json_decode($item->conditional_logic);
                $notiConditionalLogic[$noti_id]['conditional_enabled'] = $item->conditional_enabled;
                $notiConditionalLogic[$noti_id]['conditional_show'] = $item->conditional_show;
                $notiConditionalLogic[$noti_id]['conditional_scope'] = $item->conditional_scope;
                $notiOrder[] = $noti_id;
            }
        }
        return array(
            'notiOrder' => $notiOrder,
            'notiConditionalLogic' => $notiConditionalLogic,
        );
    }

    public function getAllFieldsToArray($option_id)
    {
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
        $secondLevelFields = array();
        if( !empty($option_id) ) {
            $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
            if( !empty($secondLevelMaps) ) {
                foreach( $secondLevelMaps as $map ) {
                    $field = $map->getChild();
                    $values = $field->toArray();
                    $values['config'] = json_encode($values['config']);
                    $field_options = $field->getOptions();
                    if (!empty($field_options) && count($field_options)) {
                        foreach ($field_options as $item)
                        {
                            $values['options'][] = $item->label;
                        }
                    }
                    $secondLevelFields[$map->child_id] = $values;
                }
            }
        }
        return $secondLevelFields;
    }
}

