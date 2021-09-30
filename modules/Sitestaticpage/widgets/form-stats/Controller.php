<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Widget_FormStatsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Don't render this if not authorized
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $stat_tab_show_all = $this->_getParam('stats_tab_setting', 0);
    $static_forms = $this->_getParam('static_forms', 0);
    
    $user = Engine_Api::_()->core()->getSubject();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (empty($stat_tab_show_all) && ($user->user_id != $viewer_id)) {
      return $this->setNoRender();
    }

    $table_values = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'values');
    $table_options = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'options');
    $table_meta = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'meta');
    $table_map = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'maps');
    
    if ((is_array($static_forms) && in_array(0, $static_forms))  || empty($static_forms)) {
            $member_select = $table_values->select()
                    ->where('member_id =?', $user->user_id)
                    ->order('form_id');
        } else {
            $member_select = $table_values->select()
                    ->where('member_id =?', $user->user_id)
                    ->where('form_id IN (?)', $static_forms)
                    ->order('form_id');
        }

        $member_data = $table_values->fetchAll($member_select);

        if (count($member_data) == 0)
            return $this->setNoRender();

        $content = '';
    $lastContents = '';
    $lastHeadingTitle = null;
    $last_form_id = 0;
    $last_item_id = 0;
    $count = 0;
    $field_values = array();
    foreach ($member_data as $value) {
      $field_select = $table_meta->select()->from($table_meta->info('name'), array('label', 'type'))
              ->where('field_id =?', $value->field_id);
      $field = $table_meta->fetchRow($field_select);
      $field_values[$value->field_id] = $value->value;

      $map_select = $table_map->select()->from($table_map->info('name'))
              ->where('child_id =?', $value->field_id);
      $map = $table_map->fetchRow($map_select);
      if ($map->field_id != 1) {
        if ($map->option_id != $field_values[$map->field_id]) {
          continue;
        }
      }
      if (!empty($last_form_id) && $value->form_id != $last_form_id) {
        if ($user->user_id == $viewer_id)
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle, $last_form_id, $last_item_id);
        else
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
        $lastContents = '';
        $form_heading = $table_options->select()->from($table_options->info('name'), 'form_heading')
                        ->where('option_id =?', $value->form_id)
                        ->query()->fetchColumn();
        if (empty($form_heading))
          $form_heading = $table_options->select()->from($table_options->info('name'), 'label')
                          ->where('option_id =?', $value->form_id)
                          ->query()->fetchColumn();
        $last_form_id = $value->form_id;
        $last_item_id = $value->item_id;
        $lastHeadingTitle = $form_heading;
      } elseif ($value->form_id != $last_form_id) {
        $form_heading = $table_options->select()->from($table_options->info('name'), 'form_heading')
                        ->where('option_id =?', $value->form_id)
                        ->query()->fetchColumn();
        if (empty($form_heading))
          $form_heading = $table_options->select()->from($table_options->info('name'), 'label')
                          ->where('option_id =?', $value->form_id)
                          ->query()->fetchColumn();
        $last_form_id = $value->form_id;
        $last_item_id = $value->item_id;
        $lastHeadingTitle = $form_heading;
      }

      if ($field->type == 'heading') {
        // Heading
        if (!empty($lastContents)) {
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
          $lastContents = '';
        }
        $lastHeadingTitle = Zend_Registry::get('Zend_Translate')->_($field->label);
        ;
      } else {
        if (!empty($value->value)) {
          if ($field->type == 'select' || $field->type == 'multiselect' || $field->type == 'radio' || $field->type == 'multi_checkbox') {

            $field_value = $table_options->select()->from($table_options->info('name'), 'label')
                            ->where('option_id =?', $value->value)
                            ->query()->fetchColumn();
          } else {
            $field_value = $value->value;
          }
          // Normal fields
          //$label = $this->view->translate($field->label);
          $label = Zend_Registry::get('Zend_Translate')->_($field->label);
          $lastContents .= <<<EOF
  <li style="list-style-type:none" data-field-id={$value->field_id}>
    <span>
      {$label}
    </span>
    <span>
      {$field_value}
    </span>
  </li>
EOF;
        }
      }
    }

    if (!empty($lastContents)) {
      if (!empty($lastHeadingTitle)) {
        if ($user->user_id == $viewer_id)
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle, $last_form_id, $last_item_id);
        else
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
      }
      else {
        if ($user->user_id == $viewer_id)
          $content .= $this->_buildLastContents($lastContents, '', $last_form_id, $last_item_id);
        else
          $content .= $this->_buildLastContents($lastContents, '');
      }
    }
    $this->view->content = $content;
  }

  protected function _buildLastContents($content, $title = '', $form_id = 0, $item_id = 0) {
    if (!$title) {
      return <<<EOF
        <div class="profile_fields">
          <ul>
            {$content}
          </ul>
        </div>
EOF;
    }
    if (!empty($content)) {
      if (!empty($form_id) && !empty($item_id)) {
        return <<<EOF
        <div class="profile_fields">
          <h4>
            <div class="fleft mright10"><span>{$title}</span></div>
            <div class="fleft mleft5">
            <a class="buttonlink seaocore_icon_edit" style="height:16px;" href="javascript:void(0);" onclick="editData($form_id, $item_id);" title="edit"></a>
          <a class="buttonlink seaocore_icon_delete" style="height:16px;" href="javascript:void(0);" onclick="deleteData($form_id, $item_id);" title="delete"></a>
            </div>
          </h4>
            
          <ul>
            {$content}
          </ul>
        </div>
EOF;
      } else {
        return <<<EOF
        <div class="profile_fields">
          <h4>
            <span>{$title}</span>
          </h4>
          <ul>
            {$content}
          </ul>
        </div>
EOF;
      }
    } else {
      return <<<EOF
        <div class="profile_fields">
          <h4>
            <span>{$title}</span>
          </h4>
        </div>
       <br />
EOF;
    }
  }

}