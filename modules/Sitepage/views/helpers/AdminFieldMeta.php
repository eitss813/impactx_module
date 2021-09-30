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
class SitePage_View_Helper_AdminFieldMeta extends Zend_View_Helper_Abstract
{
  public function adminFieldMeta($map)
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

    if( !($meta instanceof Fields_Model_Meta) ) {
      return '';
    }

    // Prepare translations
    $translate = Zend_Registry::get('Zend_Translate');

    // Prepare params
    if( $meta->type == 'heading' ) {
      $containerClass = 'heading';
    } else {
      $containerClass = 'field';
    }

    $key = $map->getKey();
      $label = $this->view->translate($meta->label);
      $label = str_replace("#540","'",$label);

    $type = $meta->type;
    
    $typeLabel = Engine_Api::_()->yndynamicform()->getFieldInfo($type, 'label');
    $typeLabel = $this->view->translate($typeLabel);

    // Options data
    $optionContent = '';
    $dependentFieldContent = '';
    
    if( $meta->canHaveDependents() ) {
      $extraOptionsClass = 'field_extraoptions ' . $this->_generateClassNames($key, 'field_extraoptions_');
      $optionContent .= <<<EOF
    <div class="{$extraOptionsClass}" id="field_extraoptions_{$key}">
      <div class="field_extraoptions_contents_wrapper">
        <div class="field_extraoptions_contents">
          <div class="field_extraoptions_add">
            {$this->view->formText('text', '', array('title' => $this->view->translate('add new choice'), 'onkeypress' => 'void(0);',  'onmousedown' => "void(0);"))}
          </div>
EOF;

      $options = $meta->getOptions();
      
      if( !empty($options) ) {
        $extraOptionsChoicesClass = 'field_extraoptions_choices ' . $this->_generateClassNames($key, 'field_extraoptions_choices_');
        $optionContent .= <<<EOF
      <ul class="{$extraOptionsChoicesClass}" id="admin_field_extraoptions_choices_{$key}">
EOF;
        foreach( $options as $option ) {
          $optionId = $option->option_id;
          $optionLabel = $this->view->translate($option->label);
          $dependentFieldCount = count(Engine_Api::_()->fields()->getFieldsMaps($option->getFieldType())->getRowsMatching('option_id', $optionId));
          $dependentFieldCountString = ( $dependentFieldCount <= 0 ? '' : ' (' . $dependentFieldCount . ')' );

          $optionClass = 'field_option_select field_option_select_' . $optionId . ' ' . $this->_generateClassNames($key, 'field_option_select_');
          $optionContent .= <<<EOF
        <li id="field_option_select_{$key}_{$optionId}" class="{$optionClass}">
          <span class="field_extraoptions_choices_options">
            <a href="javascript:void(0);" onclick="void(0);">{$translate->_('edit')}</a>
            | <a href="javascript:void(0);" onclick="void(0);">X</a>
          </span>
          <span class="field_extraoptions_choices_label" onclick="void(0);">
            {$optionLabel} {$dependentFieldCountString}
          </span>
        </li>
EOF;
        }
        
        $optionContent .= <<<EOF
      </ul>
EOF;
        foreach( $options as $option ) {
          $dependentFieldContent .= $this->view->adminFieldOption($option, $map);
        }
      }

      $optionContent .= <<<EOF
    </div>
  </div>
 
  <!--
  <a class="edit_choice" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
    {$translate->_('edit choices')}
  </a>
  -->
</div>

EOF;
    }

    // Generate
    $contentClass = 'admin_field ' . $this->_generateClassNames($key, 'admin_field_');
      if (in_array($type, $noEditButton)) {

          $label = str_replace("#540","'",$label);
    $content = <<<EOF
  <li id="admin_field_{$key}" class="{$contentClass}" type="{$type}">
    <span class='{$containerClass}'>
      
      <div class='item_options'>
        <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('delete')}</a>
      </div>
      <div class='item_title'>
        {$label}
        <span>({$typeLabel})</span>
      </div>
      {$optionContent}
    </span>
    {$dependentFieldContent}
  </li>
EOF;
      } else {
          $label = str_replace("#540","'",$label);
$content = <<<EOF
  <li id="admin_field_{$key}" class="{$contentClass}" type="{$type}">
    <span class='{$containerClass}'>
      
      <div class='item_options'>
        <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('edit')}</a>
        | <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('delete')}</a>
      </div>
      <div class='item_title'>
          <i class="fa fa-arrows" aria-hidden="true"></i>
        {$label}
        <span>({$typeLabel})</span>
      </div>
      {$optionContent}
    </span>
    {$dependentFieldContent}
  </li>
EOF;
      }

    return $content;
  }

  public function adminFieldMetas($map)
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

        if( !($meta instanceof Fields_Model_Meta) ) {
            return '';
        }

        // Prepare translations
        $translate = Zend_Registry::get('Zend_Translate');

        // Prepare params
        if( $meta->type == 'heading' ) {
            $containerClass = 'heading';
        } else {
            $containerClass = 'field';
        }

        $key = $map->getKey();
        $label = $this->view->translate($meta->label);
        $label = str_replace("#540","'",$label);

        $type = $meta->type;

        $typeLabel = Engine_Api::_()->yndynamicform()->getFieldInfo($type, 'label');
        $typeLabel = $this->view->translate($typeLabel);

        // Options data
        $optionContent = '';
        $dependentFieldContent = '';

        if( $meta->canHaveDependents() ) {
            $extraOptionsClass = 'field_extraoptions ' . $this->_generateClassNames($key, 'field_extraoptions_');
            $optionContent .= <<<EOF
    <div class="{$extraOptionsClass}" id="field_extraoptions_{$key}">
      <div class="field_extraoptions_contents_wrapper">
        <div class="field_extraoptions_contents">
          <div class="field_extraoptions_add">
            {$this->view->formText('text', '', array('title' => $this->view->translate('add new choice'), 'onkeypress' => 'void(0);',  'onmousedown' => "void(0);"))}
          </div>
EOF;

            $options = $meta->getOptions();

            if( !empty($options) ) {
                $extraOptionsChoicesClass = 'field_extraoptions_choices ' . $this->_generateClassNames($key, 'field_extraoptions_choices_');
                $optionContent .= <<<EOF
      <ul class="{$extraOptionsChoicesClass}" id="admin_field_extraoptions_choices_{$key}">
EOF;
                foreach( $options as $option ) {
                    $optionId = $option->option_id;
                    $optionLabel = $this->view->translate($option->label);
                    $dependentFieldCount = count(Engine_Api::_()->fields()->getFieldsMaps($option->getFieldType())->getRowsMatching('option_id', $optionId));
                    $dependentFieldCountString = ( $dependentFieldCount <= 0 ? '' : ' (' . $dependentFieldCount . ')' );

                    $optionClass = 'field_option_select field_option_select_' . $optionId . ' ' . $this->_generateClassNames($key, 'field_option_select_');
                    $optionContent .= <<<EOF
        <li id="field_option_select_{$key}_{$optionId}" class="{$optionClass}">
          <span class="field_extraoptions_choices_options">
            <a href="javascript:void(0);" onclick="void(0);">{$translate->_('edit')}</a>
            | <a href="javascript:void(0);" onclick="void(0);">X</a>
          </span>
          <span class="field_extraoptions_choices_label" onclick="void(0);">
            {$optionLabel} {$dependentFieldCountString}
          </span>
        </li>
EOF;
                }

                $optionContent .= <<<EOF
      </ul>
EOF;
                foreach( $options as $option ) {
                    $dependentFieldContent .= $this->view->adminFieldOption($option, $map);
                }
            }

            $optionContent .= <<<EOF
    </div>
  </div>
 
  <!--
  <a class="edit_choice" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
    {$translate->_('edit choices')}
  </a>
  -->
</div>

EOF;
        }

        // Generate
        $contentClass = 'admin_field ' . $this->_generateClassNames($key, 'admin_field_');
        if (in_array($type, $noEditButton)) {

            $label = str_replace("#540","'",$label);
            $content = <<<EOF
  <li id="admin_field_{$key}" class="{$contentClass}" type="{$type}">
    <span class='{$containerClass}'>
      
      <div class='item_options'>
        <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('delete')}</a>
      </div>
      <div class='item_title'>
        {$label}
        <span>({$typeLabel})</span>
      </div>
      {$optionContent}
    </span>
    {$dependentFieldContent}
  </li>
EOF;
        } else {
            $label = str_replace("#540","'",$label);
            $content = <<<EOF
  <li id="admin_field_{$key}" class="{$contentClass}" type="{$type}">
    <span class='{$containerClass}'>
      
      <div class='item_options'>
        <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('edit')}</a>
        | <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('delete')}</a>
      </div>
      <div class='item_title'>
          <i class="fa fa-arrows" aria-hidden="true"></i>
        {$label}
        <span>({$typeLabel})</span>
      </div>
      {$optionContent}
    </span>
    {$dependentFieldContent}
  </li>
EOF;
        }

        return $content;
    }

  protected function _generateClassNames($key, $prefix = '')
  {
    list($parent_id, $option_id, $child_id) = explode('_', $key);
    return
      $prefix . 'parent_' . $parent_id . ' ' .
      $prefix . 'option_' . $option_id . ' ' .
      $prefix . 'child_' . $child_id
      ;
  }
}