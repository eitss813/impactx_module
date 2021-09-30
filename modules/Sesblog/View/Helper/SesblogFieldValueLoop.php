<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: SesblogFieldValueLoop.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_View_Helper_SesblogFieldValueLoop extends Fields_View_Helper_FieldAbstract {

  public function sesblogFieldValueLoop($subject, $partialStructure) {

    if (empty($partialStructure))
      return '';

    if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity())
      return '';

    // Calculate viewer-subject relationship
    $viewer = Engine_Api::_()->user()->getViewer();
    $usePrivacy = ($subject instanceof User_Model_User);
    if ($usePrivacy) {
      $relationship = 'everyone';
      if ($viewer && $viewer->getIdentity()) {
        if ($viewer->getIdentity() == $subject->getIdentity())
          $relationship = 'self';
        else if ($viewer->membership()->isMember($subject, true))
          $relationship = 'friends';
        else
          $relationship = 'registered';
      }
    }

    // Generate
    $content = '';
    $lastContents = '';
    $lastHeadingTitle = null;

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();

    if ($module == 'sesblog' && $controller == 'admin-manage' && $action == 'view')
      $show_hidden = false;
    else
      $show_hidden = $viewer->getIdentity() ? ($subject->getOwner()->isSelf($viewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type) : false;


    foreach ($partialStructure as $map) {

      // Get field meta object
      $field = $map->getChild();
      $value = $field->getValue($subject);
      if (!$field || $field->type == 'profile_type')
        continue;
      if (!$field->display && !$show_hidden)
        continue;
      $isHidden = !$field->display;

      // Get first value object for reference
      $firstValue = $value;
      if (is_array($value) && !empty($value)) {
        $firstValue = $value[0];
      }

      // Evaluate privacy
      if ($usePrivacy && !empty($firstValue->privacy) && $relationship != 'self') {
        if ($firstValue->privacy == 'self' && $relationship != 'self') {
          $isHidden = true; //continue;
        } else if ($firstValue->privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self')) {
          $isHidden = true; //continue;
        } else if ($firstValue->privacy == 'registered' && $relationship == 'everyone') {
          $isHidden = true; //continue;
        }
      }

      // Render
      if ($field->type == 'heading') {
        // Heading
        if (!empty($lastContents)) {
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
          $lastContents = '';
        }
        $lastHeadingTitle = $this->view->translate($field->label);
      } else {
        // Normal fields
        $tmp = $this->getFieldValueString($field, $value, $subject, $map, $partialStructure);
        if (!empty($firstValue->value) && !empty($tmp)) {

          $notice = $isHidden && $show_hidden ? sprintf('<div class="tip"><span>%s</span></div>', $this->view->translate('This field is hidden and only visible to you and admins:')) : '';
          if (!$isHidden || $show_hidden) {
            $label = $this->view->translate($field->label);
            $lastContents .= <<<EOF
  <span data-field-id={$field->field_id}>{$notice}<span>{$label}: </span><span>{$tmp}</span></span>
EOF;
          }
        }
      }
    }

    if (!empty($lastContents)) {
      $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
    }

    return $content;
  }

  public function getFieldValueString($field, $value, $subject, $map = null, $partialStructure = null) {
    if ((!is_object($value) || !isset($value->value)) && !is_array($value)) {
      return null;
    }

    $helperName = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');
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

  protected function _buildLastContents($content, $title) {
    if (!$title) {
      return $content;
    }
    return <<<EOF
          <h4><span>{$title}: </span></h4>{$content}</div>
EOF;
  }

}
