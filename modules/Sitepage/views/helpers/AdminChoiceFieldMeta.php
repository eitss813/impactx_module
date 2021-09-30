<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminFieldMeta.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class SitePage_View_Helper_AdminChoiceFieldMeta extends Zend_View_Helper_Abstract
{
    public function adminChoiceFieldMeta($map)
    {
        $meta = $map->getChild();

        $noEditButton = array(
            'recaptcha',
            'ua_ip_address',
            'ua_browser',
            'ua_browser_version',
            'ua_country',
            'ua_state',
            'ua_city',
            'ua_longitude',
            'ua_latitude',
        );

        if (!($meta instanceof Fields_Model_Meta)) {
            return '';
        }

        // Prepare translations
        $translate = Zend_Registry::get('Zend_Translate');

        // Prepare params
        if ($meta->type == 'heading') {
            $containerClass = 'heading';
        } else {
            $containerClass = 'field';
        }

        $key = $map->getKey();
        $label = $this->view->translate($meta->label);
        $type = $meta->type;

        $typeLabel = Engine_Api::_()->yndynamicform()->getFieldInfo($type, 'label');
        $typeLabel = $this->view->translate($typeLabel);

        // Options data
        $optionContent = '';
        $dependentFieldContent = '';

        if ($meta->canHaveDependents()) {
            $extraOptionsClass = 'field_extraoptions ' . $this->_generateClassNames($key, 'field_extraoptions_');
            $optionContent .= <<<EOF
    <div class="{$extraOptionsClass}" id="field_extraoptions_{$key}">
      <div id="label-label" class="form-label"><label for="label">Choices</label></div>
      <div class="field_extraoptions_contents_wrapper">
        <div class="field_extraoptions_contents">
          <div class="field_extraoptions_custom_add">          
            <input type="text" name="option_text" id="option_text" title="add new choice">
            <button type="button" onclick="createOption()">Add</button>
          </div>
EOF;


            $options = $meta->getOptions();

            if (!empty($options)) {
                $extraOptionsChoicesClass = 'field_extraoptions_choices ' . $this->_generateClassNames($key, 'field_extraoptions_choices_');
                $optionContent .= <<<EOF
      <ul class="{$extraOptionsChoicesClass}" id="admin_field_extraoptions_choices_{$key}">
EOF;
                foreach ($options as $option) {
                    $optionId = $option->option_id;
                    $optionLabel = $this->view->translate($option->label);
                    $dependentFieldCount = count(Engine_Api::_()->fields()->getFieldsMaps($option->getFieldType())->getRowsMatching('option_id', $optionId));
                    $dependentFieldCountString = ($dependentFieldCount <= 0 ? '' : ' (' . $dependentFieldCount . ')');

                    $optionClass = 'field_option_select field_option_select_' . $optionId . ' ' . $this->_generateClassNames($key, 'field_option_select_');
                    $optionContent .= <<<EOF
        <li id="field_option_select_{$key}_{$optionId}" class="{$optionClass}" style=" border-bottom: 1px solid #eee;">
            <div id="field_option_select_container_{$optionId}">
                <span class="field_extraoptions_choices_options">
                    <a href="javascript:void(0);" onclick="enableEdit({$optionId},'{$optionLabel}');">{$translate->_('Edit')}</a>
                    | <a href="javascript:void(0);" onclick="deleteOption({$optionId})">Delete</a>
                </span>
                <span class="field_extraoptions_choices_label" onclick="void(0);">
                    {$optionLabel} {$dependentFieldCountString}
                </span>
            </div>
            <div class="field_extraoptions_custom_edit" id="field_extraoptions_custom_edit_{$optionId}" style="display: none">
                <br>          
                <input type="text" name="option_edit_text_{$optionId}" id="option_edit_text_{$optionId}" title="add new choice">
                <button type="button" onclick="editOption({$optionId})">Save</button>
                <button type="button" onclick="cancelEdit({$optionId})">Cancel</button>
            </div>
        </li>
EOF;
                }

                $optionContent .= <<<EOF
      </ul>
EOF;
                foreach ($options as $option) {
                    $dependentFieldContent .= $this->view->adminFieldOption($option, $map);
                }
            }

            $optionContent .= <<<EOF
    </div>
  </div>
 
</div><br>

EOF;
        }

        // Generate
        $contentClass = 'admin_field ' . $this->_generateClassNames($key, 'admin_field_');
        $content = <<<EOF
            <div id="admin_field_{$key}" class="{$contentClass}" type="{$type}">
                <span class='{$containerClass}'>
                {$optionContent}
            </span>
    </div>
EOF;

        return $content;
    }

    protected function _generateClassNames($key, $prefix = '')
    {
        list($parent_id, $option_id, $child_id) = explode('_', $key);
        return
            $prefix . 'parent_' . $parent_id . ' ' .
            $prefix . 'option_' . $option_id . ' ' .
            $prefix . 'child_' . $child_id;
    }
}