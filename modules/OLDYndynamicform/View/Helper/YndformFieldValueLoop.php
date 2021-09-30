<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/26/2016
 * Time: 6:01 PM
 */
class Yndynamicform_View_Helper_YndformFieldValueLoop extends Fields_View_Helper_FieldAbstract
{
    public function yndformFieldValueLoop($subject, $partialStructure, $isPrint = true)
    {
        if (empty($partialStructure)) {
            return '';
        }

        if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity()) {
            return '';
        }
        // Init user_analytic_fields
        $yndform = $subject->getForm();
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $user_analytic_fields = array('ua_ip_address',
            'ua_browser',
            'ua_browser_version',
            'ua_country',
            'ua_state',
            'ua_city',
            'ua_longitude',
            'ua_latitude',);
        $ua_content = '';

        // Calculate viewer-subject relationship

        $special_section = array('agreement', 'section_break', 'heading', 'html_editor');
        $not_print = array('text_editor', 'html_editor');
        // Generate
        $content = '';
        $lastContents = '';
        $lastHeadingTitle = null; //Zend_Registry::get('Zend_Translate')->_("Missing heading");
	$firstField = false;
        foreach ($partialStructure as $map) {
            // Get field meta object
            $field = $map->getChild();
            if ($subject -> getFormOptionID() !== $map -> option_id) {
                continue;
            }

            // Not return
            if (in_array($field->type, $not_print) && !$isPrint) continue;

            if (in_array($field->type, $user_analytic_fields))
                if (!$yndform -> isModerator($viewer) && !$viewer->isAdmin())
                    continue;
                else {
                    $value = $field->getValue($subject);
                    $label = $this->view->translate($field->label);
                    $label = str_replace("#540","'",$label);
                    
                    $default_color = '';
                    if( $field->type == 'metrics' ) {
                        $default_color = 'style="color: #f35f5f; font-weight: bold;"';
                        if( ($field->config['metric_aggregate_type'] == 'metric_sum') )
                            $label = $label . '<br /><span style="font-size: 11px;">(Calculated automatically)</span>';
                        else
                            $label = $label . '<br /><span style="font-size: 11px;">(Calculated automatically)<br />Formula: ' . $field->config['own_actual_formula'] . '</span>';

                        if( !empty($field->config['selected_metric_id']) ) {
                            $metricUrl = $this->view->url(array('action' => 'index','metric_id' => $field->config['selected_metric_id']), "sitepage_metrics");
                            $label = '<a href="'.$metricUrl.'" target="_blank" '.$default_color.'>'.$label.'</a>';
                        }
                        
                        $tmp = @number_format($tmp, 2);
                    }
                    
                    $tmp = $this->getFieldValueString($field, $value, $subject, $map, $partialStructure);
                    $ua_content .= <<<EOF
<div data-field-id={$field->field_id} class="form-wrapper">
<div class="form-label">
  <label {$default_color}>{$label}</label>
</div>
<div class="form-element">
  <span {$default_color}>{$tmp}</span>
</div>
</div>
EOF;
                    continue;
                }

            $value = $field->getValue($subject);
            if (!$field || $field->type == 'profile_type') continue;
            if (!$field->display) continue;
            $isHidden = !$field->display;

            // Render
            // Normal fields
            $tmp = $this->getFieldValueString($field, $value, $subject, $map, $partialStructure);
            if (!empty($tmp)) {
                if (!$isHidden) {
                    $label = $this->view->translate($field->label);
                    $label = str_replace("#540","'",$label);
                    
                    $default_color = '';
                    if( $field->type == 'metrics' ) {
                        $default_color = 'style="color: #f35f5f; font-weight: bold;"';
                        if( ($field->config['metric_aggregate_type'] == 'metric_sum') )
                            $label = $label . '<br /><span style="font-size: 11px;">(Calculated automatically)</span>';
                        else
                            $label = $label . '<br /><span style="font-size: 11px;">(Calculated automatically)<br />Formula: ' . $field->config['own_actual_formula'] . '</span>';

                        if( !empty($field->config['selected_metric_id']) ) {
                            $metricUrl = $this->view->url(array('action' => 'index','metric_id' => $field->config['selected_metric_id']), "sitepage_metrics");
                            $label = '<a href="'.$metricUrl.'" target="_blank" '.$default_color.'>'.$label.'</a>';
                        }
                        
                        $tmp = @number_format($tmp, 2);
                    }
                    
                    if (in_array($field -> type, $special_section)) {
                        $tmp = $tmp != ' ' ? "<div class='form-element'><span $default_color>$tmp</span></div>" : '';
                        $class = 'yndform_'.$field -> type;
                        $lastContents .= <<<EOF
<div data-field-id={$field->field_id} class="yndform_section_break {$class} clearfix">
<div class="form-label">
  <label {$default_color}>{$label}</label>
</div>
{$tmp}
</div>
EOF;
                    } else {
                        $label = str_replace("#540","'",$label);
                        $lastContents .= <<<EOF
<div data-field-id={$field->field_id} class="form-wrapper">
<div class="form-label">
  <label {$default_color}>{$label}</label>
</div>
<div class="form-element">
  <span {$default_color}>{$tmp}</span>
</div>
</div>
EOF;
                    }
                }
            }
        }
        if (!empty($ua_content)) {
            $lastContents .= $ua_content;
        }
        if (!empty($lastContents)) {
            $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
        }
        return $content;
    }

    public function getFieldValueString($field, $value, $subject, $map = null,
                                        $partialStructure = null)
    {
        if ((!is_object($value) || !isset($value->value)) && !is_array($value)) {
            return null;
        }

        // @todo This is not good practice:
        // if($field->type =='textarea'||$field->type=='about_me') $value->value = nl2br($value->value);
        if($field->type == "phone")
            $field->type = "text";

        $helperName = Engine_Api::_()->yndynamicform()->getFieldInfo($field->type, 'helper');
        if (!$helperName) {
            return null;
        }
        $helper = $this->view->getHelper($helperName);
        if (!$helper) {
            return null;
        }

        $helper->structure = $partialStructure;
        $helper->map = $map;
        $helper->field = $field;
        $helper->subject = $subject;
        $tmp = $helper->$helperName($subject, $field, $value);
        unset($helper->structure);
        unset($helper->map);
        unset($helper->field);
        unset($helper->subject);

        return $tmp;
    }

    protected function _buildLastContents($content, $title)
    {
        if (!$title) {
            return $content;
        }
        return <<<EOF
            {$content}
EOF;
    }
}
