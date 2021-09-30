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
class SitePage_View_Helper_AdminChoiceFieldMetaNew extends Zend_View_Helper_Abstract
{

    public function adminChoiceFieldMetaNew($type){

        $key = 0;
        $contentClass = 'admin_field ' . $this->_generateClassNames($key, 'admin_field_');

        $extraOptionsClass = 'field_extraoptions ' . $this->_generateClassNames($key, 'field_extraoptions_');
        $optionContent = <<<EOF
            <div class="{$extraOptionsClass}" id="field_extraoptions_{$key}">
              <div id="label-label" class="form-label"><label for="label">Choices</label></div>
              <div class="field_extraoptions_contents_wrapper">
                <div class="field_extraoptions_contents">
                  <div class="field_extraoptions_custom_add">          
                    <input type="text" name="option_text" id="option_text" title="add new choice">
                    <button type="button" onclick="createOptionWithNewField('{$type}')">Add</button>
                  </div>
                </div>
            </div>
EOF;

        $content = <<<EOF
            <div id="admin_field_{$key}" class="{$contentClass}" type="{$type}">
                <span class='field'>
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