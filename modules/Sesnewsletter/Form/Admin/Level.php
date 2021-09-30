<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Level.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

      // Element: style
      $this->addElement('Radio', 'allowsubs', array(
        'label' => 'Allow Newsletter Subscription',
        'description' => 'Do you want to enable members of this level to subscribe to the newsletter on your website?',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',
        ),
        'value' => 1,
      ));

  }
}
