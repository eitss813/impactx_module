<?php
/**
 * SocialEngine
 *
 * @category   Module
 * @package    Consecutive Bytes Core
 * @copyright  Copyright 2015 - 2017 Consecutive Bytes
 * @license    http://www.consecutivebytes.com/license/
 * @version    4.9.0
 * @author     Consecutive Bytes
 */

?>
<div id='new_notification'>
  <span>
    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
                               $this->translate($this->locale()->toNumber($this->notificationCount)),
                               array('id' => 'core_menu_mini_menu_updates_count')) ?>
  </span>
  <span id="core_menu_mini_menu_updates_close">
    <a href="javascript:void(0);" onclick="en4.activity.hideNotifications();">x</a>
  </span>
</div>