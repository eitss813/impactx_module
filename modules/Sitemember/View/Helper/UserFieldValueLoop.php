<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UserFieldValueLoop.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_View_Helper_UserFieldValueLoop extends Fields_View_Helper_FieldAbstract {

    public function userFieldValueLoop($subject, $partialStructure, $params = array()) {

        if (empty($partialStructure)) {
            return '';
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity()) {
            return '';
        }

        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return '';
        }

        // Calculate viewer-subject relationship
        $usePrivacy = ($subject instanceof User_Model_User);
        if ($usePrivacy) {
            $relationship = 'everyone';
            if ($viewer && $viewer->getIdentity()) {
                if ($viewer->getIdentity() == $subject->getIdentity()) {
                    $relationship = 'self';
                } else if ($viewer->membership()->isMember($subject, true)) {
                    $relationship = 'friends';
                } else {
                    $relationship = 'registered';
                }
            }
        }
        // Generate
        $content = '';
        $lastContents = '';
        $lastSocialContents = '';
        $lastHeadingTitle = null; //Zend_Registry::get('Zend_Translate')->_("Missing heading");
        $show_hidden = $viewer->getIdentity() ? ($subject->getOwner()->isSelf($viewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type) : false;
        $count = 0;
        $alreadyId = [];
        $alreadyHeading = [];
        foreach ($partialStructure as $map) {
            if (isset($params['customParams']) && $count == $params['customParams'])
                break;

            // Get field meta object
            $field = $map->getChild();
            $value = $field->getValue($subject);
            if (!$field || $field->type == 'profile_type')
                continue;

            // CONDITION FOR WHICH FIELD WE WANT TO SHOW IN WIDGETS.
            if (!$field->member)
                continue;

            if (!$field->display && !$show_hidden)
                continue;

            $isHidden = !$field->display;

            // Get first value object for reference
            $firstValue = $value;
            if (is_array($value) && isset($value[0])) {
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
                if (in_array($field->label, $alreadyHeading)) {
                  continue;
                }

                // Heading
                if (isset($params['custom_field_heading']) && !empty($params['custom_field_heading'])) {
                    if (!empty($lastContents)) {
                        $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle, $lastSocialContents);
                        $lastContents = '';
                        $lastSocialContents = '';
                    }
                    $lastHeadingTitle = $this->view->translate($field->label);
                    $alreadyHeading[] = $field->label;
                }
            } else {
                // Normal fields
                $tmp = $this->getFieldValueString($field, $value, $subject, $map, $partialStructure);

                if (!empty($firstValue->value) && !empty($tmp)) {
                    if (in_array($field->label, $alreadyId)) {
                      continue;
                    }

                    $notice = $isHidden && $show_hidden ? sprintf('<div class="tip"><span>%s</span></div>', $this->view->translate('This field is hidden and only visible to you and admins:')) : '';
                    $alreadyId[] = $field->field_id;
                    if (!$isHidden || $show_hidden) {
                        if (isset($params['custom_field_title']) && !empty($params['custom_field_title'])) {
                            $label = $this->view->translate($field->label);

                            if (!in_array($field->type, array('website', 'twitter', 'aim', 'facebook'))) {
                                $lastContents .= <<<EOF
  <li data-field-id={$field->field_id}>
    {$notice}
    <span>{$label}:</span>
    <span>
      {$tmp}
    </span>
  </li>
EOF;
                            } else {
                                $lastSocialContents .= <<<EOF
  <li class=sitemeber_social_$field->type data-field-id={$field->field_id}>
    {$notice}
    <span>
      {$tmp}
    </span>
  </li>
EOF;
                            }
                        } else {
                            if (!in_array($field->type, array('website', 'twitter', 'aim', 'facebook'))) {
                                $lastContents .= <<<EOF
  <li data-field-id={$field->field_id}>
    {$notice}
    <span>
      {$tmp}
    </span>
  </li>
EOF;
                            } else {
                                $lastSocialContents .= <<<EOF
  <li class=sitemeber_social_$field->type data-field-id={$field->field_id}>
    {$notice}
    <span>
      {$tmp}
    </span>
  </li>
EOF;
                            }
                        }

                        $count++;
                    }
                }
            }
        }
        //if (!empty($lastContents)) {
            $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle, $lastSocialContents);
     //   }
        return $content;
    }

    public function getFieldValueString($field, $value, $subject, $map = null, $partialStructure = null) {

        if ((!is_object($value) || !isset($value->value)) && !is_array($value)) {
            return null;
        }

        // @todo This is not good practice:
        // if($field->type =='textarea'||$field->type=='about_me') $value->value = nl2br($value->value);

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

        if ($field->type == 'birthdate') {
            if (!$value->value) {
                $tmp = $this->view->userFieldBirthdate($subject, $field, $value);
            } else {
                $tmp = $helper->$helperName($subject, $field, $value);
            }
        } else {
            $tmp = $helper->$helperName($subject, $field, $value);
        }
        unset($helper->structure);
        unset($helper->map);
        unset($helper->field);
        unset($helper->subject);
        return $tmp;
    }

    protected function _buildLastContents($content, $title, $lastSocialContents) {

        if (!$title) {

            if ($lastSocialContents) {
                return '<div class="siteuser_cover_profile_fields seaocore_txt_light"><ul class="sitemember_social_links">' . $lastSocialContents . '</ul><ul>' . $content . '</ul></div>';
            } else {
                return '<div class="siteuser_cover_profile_fields seaocore_txt_light"><ul>' . $content . '</ul></div>';
            }
        }
        if ($lastSocialContents) {
            return <<<EOF
          <div class="siteuser_cover_profile_fields">
          <ul class="sitemember_social_links">
            {$lastSocialContents}
          </ul>
          <ul>
            {$content}
          </ul>
         </div>
EOF;
        } else {
            return <<<EOF
          <div class="siteuser_cover_profile_fields">
          <p class="bold">
            <span>{$title}</span>
          </p>
          <ul>
            {$content}
          </ul>
         </div>
EOF;
        }
    }

}
