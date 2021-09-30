<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberLevel.php 2017-02-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Admin_MemberLevel extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

    $this->addElement('Radio', 'compliment', array(
      'label' => 'Allow for compliment?',
      'description' => 'Do you want to allow member to give a compliment for other members?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => 1
    ));
  }

}
