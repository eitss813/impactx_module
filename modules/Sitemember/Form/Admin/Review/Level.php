<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Admin_Review_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {

    parent::init();

    $this->setTitle('Member Level Settings')
            ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

    if (!$this->isPublic()) {
        
    $review_create_element = "review_create_member";
    $this->addElement('Radio', "$review_create_element", array(
        'label' => 'Allow Writing of Reviews',
        'description' => 'Do you want to let members write reviews for members?',
        'multiOptions' => array(
            1 => 'Yes, allow members to write reviews.',
            0 => 'No, do not allow members to write reviews.',
        ),
        'value' => 1,
    ));        

      $review_reply_element = "review_reply_member";
      $this->addElement('Radio', "$review_reply_element", array(
          'label' => 'Allow Commenting on Reviews?',
          'description' => 'Do you want to let members to comment on Reviews?',
          'multiOptions' => array(
              1 => 'Yes, allow members to comment on reviews.',
              0 => 'No, do not allow members to comment on reviews.',
          ),
          'value' => 1,
      ));
      if (!$this->isModerator()) {
        unset($this->$review_reply_element->options[2]);
      }

      $review_update_element = "review_update_member";
      $this->addElement('Radio', "$review_update_element", array(
          'label' => 'Allow Updating of Reviews?',
          'description' => 'Do you want to let members to update their reviews?',
          'multiOptions' => array(
              1 => 'Yes, allow members to update their own reviews.',
              0 => 'No, do not allow members to update their reviews.',
          ),
          'value' => 1,
      ));

      $review_delete_element = "review_delete_member";
      $this->addElement('Radio', "$review_delete_element", array(
          'label' => 'Allow Deletion of Reviews?',
          'description' => 'Do you want to let members delete reviews?',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all reviews.',
              1 => 'Yes, allow members to delete their own reviews.',
              0 => 'No, do not allow members to delete their reviews.',
          ),
          'value' => ( $this->isModerator() ? 2 : 0 ),
      ));
      if (!$this->isModerator()) {
        unset($this->$review_delete_element->options[2]);
      }
    }
  }

}
