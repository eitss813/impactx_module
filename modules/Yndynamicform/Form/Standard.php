<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Standard.php 9910 2013-02-14 19:22:15Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Yndynamicform_Form_Standard extends Fields_Form_Standard
{
    protected $_mode;
    
    public $ajaxValidation = true;
    
    public function setMode($mode)
    {
        $this -> _mode = $mode;
    }

    public function getMode()
    {
        return $this -> _mode;
    }
    // Override
    public function generate()
    {
        $struct = $this->getFieldStructure();
        $orderIndex = 1;
        // For Element Page Break
        $array_elements = array();
        $configs = null;
        $page_break_id = 0;
        // Check if we are editting this entry or form
        $modeAction = $this -> getMode();
        foreach ($struct as $fskey => $map) {
            $field = $map->getChild();

            // Skip fields hidden on signup
            if (isset($field->show) && !$field->show && $this->_isCreation) {
                continue;
            }

            /*
             * Check if this field is show for register or show for guest.
             * If viewer don't have permission to view this field. We'll not add to form
             */
            $viewer = Engine_Api::_() -> user() -> getViewer();
            if ($viewer -> getIdentity() == 0 && $field->config['show_guest'] == 0) {
                continue;
            } elseif ($viewer -> getIdentity() != 0 && $field->config['show_registered'] == 0) {
                continue;
            }
            // Get page break config
            if (!$configs) {
                $form = Engine_Api::_() -> yndynamicform() -> getFormByOptionId($map->option_id);
                if ($form instanceof Yndynamicform_Model_Form) {
                    $configs = $form->page_break_config;
                    if (strcmp($configs['next_button'], 'image')) {
                        $next_button_text = strcmp($configs['next_button'], 'image') ? $configs['next_button_text'] : '';
                    } else {
                        $next_button_text = '';
                    }

                    if (strcmp($configs['pre_button'], 'image')) {
                        $pre_button_text = strcmp($configs['pre_button'], 'image') ? $configs['pre_button_text'] : '';
                    } else {
                        $pre_button_text = '';
                    }
                }
            }

            // Add field and load options if necessary
            $params = $field->getElementParams($this->getItem());

            //$key = 'field_' . $field->field_id;
            $key = $map->getKey();

            // If value set in processed values, set in element
            if (!empty($this->_processedValues[$field->field_id])) {
                $params['options']['value'] = $this->_processedValues[$field->field_id];
            }

            if (!@is_array($params['options']['attribs'])) {
                $params['options']['attribs'] = array();
            }

            // Heading
            if ($params['type'] == 'Heading') {
                $params['options']['value'] = Zend_Registry::get('Zend_Translate')->_($params['options']['label']);
                unset($params['options']['label']);
            }
            // Order
            // @todo this might cause problems, however it will prevent multiple orders causing elements to not show up
            $params['options']['order'] = $orderIndex++;

            if ($modeAction != 'create') {
                $params['options']['disabled'] = 'disabled';
            }

            // Advanced fields type
            $advanced_fields = array('Recaptcha','FileUpload','TextEditor', 'HtmlEditor', 'SectionBreak', 'Agreement', 'StarRating');
            $date_fields = array('Date', 'Birthdate');
            $user_analytic_fields = array('UaLatitude', 'UaLongitude', 'UaCity', 'UaState', 'UaCountry', 'UaIpAddress', 'UaBrowser', 'UaBrowserVersion');
            $inflectedType = Engine_Api::_()->fields()->inflectFieldType($params['type']);
            unset($params['options']['alias']);
            unset($params['options']['publish']);

            if ($inflectedType == 'PageBreak') {
                if ($modeAction != 'create') {
                    continue;
                }
                if (!$page_break_id) $page_break_id = 1;
                // Add element prev here because pre always before next
                $this->addElement('Button', $key, array(
                    'label' => $next_button_text,
                    'order' => $orderIndex,
                    'id' => $key,
                    'class' => 'yndform_page_next_' . $configs['next_button'],
                    'onclick' => 'pageNext(' . ($page_break_id + 1) . ')',
                    'belong_to' => $page_break_id,
                    'decorators' => array(
                        'ViewHelper'
                    )
                ));
                $orderIndex++;

                if ($page_break_id != 1) {
                    $this->addElement('Button', $key . '_prev', array(
                        'label' => $pre_button_text,
                        'order' => $orderIndex,
                        'class' => 'yndform_page_prev_' . $configs['next_button'],
                        'onclick' => 'pagePrev(' . ($page_break_id - 1) . ')',
                        'belong_to' => $page_break_id,
                        'decorators' => array(
                            'ViewHelper'
                        )
                    ));
                    $orderIndex++;
                    array_push($array_elements, $key, $key . '_prev');
                } else {
                    array_push($array_elements, $key);
                }
                $this->addDisplayGroup($array_elements, 'page_' . $page_break_id, array(
                    'decorators' => array(
                        'FormElements',
                        'DivDivDivWrapper'
                    ),
                ));
                $array_elements = array();
                $page_break_id++;
                continue;
            } else {
                array_push($array_elements, $key);
            }

            if ($inflectedType == 'Website') {
                $params['options']['validators'] =  array(
                    array('Regex', true, array('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i')),
                );
                $this->addElement('Text', $key, $params['options']);
            } else if (in_array($inflectedType, $advanced_fields)) {
                $this->addElement('dummy', $key, array(
                    'order' => $orderIndex,
                    'decorators' => array(array(
                        'ViewScript',
                        array(
                            'viewScript' => 'advanced-fields/_' . $inflectedType . '.tpl',
                            'class' => 'form element',
                            'id' => $key,
                            'params' => $params['options'],
                        )
                    )),
                ));
                $orderIndex++;
            } elseif (in_array($inflectedType, $date_fields)) {
                $this->addElement('dummy', $key, array(
                    'order' => $orderIndex,
                    'decorators' => array(array(
                        'ViewScript',
                        array(
                            'viewScript' => 'advanced-fields/_Date.tpl',
                            'class' => 'form element',
                            'id' => $key,
                            'params' => $params['options'],
                        )
                    )),
                ));
                $orderIndex++;
            } elseif (in_array($inflectedType, $user_analytic_fields)) {
                unset($params['options']);
                $params['options']['id'] = lcfirst($inflectedType);
                $params['options']['description'] = '';
                $params['options']['order'] = $orderIndex;
                $this->addElement('Hidden', $key, $params['options']);
                $orderIndex++;
            } else {
                $params['options']['order'] = $orderIndex;
                $this->addElement($inflectedType, $key, $params['options']);
                $orderIndex++;
            }

            $element = $this->getElement($key);

            if (method_exists($element, 'setFieldMeta')) {
                $element->setFieldMeta($field);
            }

            // Set attributes for hiding/showing fields using javscript
            $classes = 'field_container field_' . $map->child_id . ' option_' . $map->option_id . ' parent_' . $map->field_id;
            $element->setAttrib('class', $classes);

            //
            if ($field->canHaveDependents()) {
                $element->setAttrib('onchange', 'changeFields(this)');
            }

            // Set custom error message
            if ($field->error) {
                $element->addErrorMessage($field->error);
            }

            if ($field->isHeading()) {
                $element->removeDecorator('Label')
                    ->removeDecorator('HtmlTag')
                    ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');
            }

            if ($field->type == 'currency') {
                $element -> addValidator(new Engine_Validate_AtLeast(0));
            }

            if ($field -> type != 'checkbox') {
                $element
                    ->addDecorator('Description', array('tag' => 'p', 'class' => 'description', 'placement' => 'APPEND'));
            }
        }

        if ($page_break_id) {
            $this->addElement('Button', 'submit_button', array(
                'label' => 'Submit',
                'order' => $orderIndex,
                'id' => 'submit_button',
                'class' => 'yndform_button_submit',
                'type' => 'submit',
                'belong_to' => $page_break_id,
                'decorators' => array(
                    'ViewHelper'
                )
            ));
            $orderIndex++;
            $this->addElement('Button', 'last_page_prev', array(
                'label' => $pre_button_text,
                'order' => $orderIndex,
                'class' => 'yndform_page_prev_'.$configs['pre_button'],
                'onclick' => 'pagePrev('.($page_break_id-1).')',
                'belong_to' => $page_break_id,
                'decorators' => array(
                    'ViewHelper'
                )
            ));
            array_push($array_elements, 'submit_button', 'last_page_prev');

            $this->addDisplayGroup($array_elements, 'page_'.$page_break_id, array(
                'decorators' => array(
                    'FormElements',
                    'DivDivDivWrapper'
                ),
            ));
        } else {
            $this->addElement('Button', 'submit_button', array(
                'label' => 'Submit',
                'type' => 'submit',
                'id' => 'submit_button',
                'class' => 'yndform_button_submit',
                'order' => 10000,
                'decorators' => array(
                    'ViewHelper'
                )
            ));
        }
    }
}
