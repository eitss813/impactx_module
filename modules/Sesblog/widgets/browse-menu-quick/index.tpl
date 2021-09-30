<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php if( count($this->quickNavigation) > 0 ): ?>
  <div class="quicklinks sesbasic_create_btn">
    <?php echo $this->navigation()->menu()->setContainer($this->quickNavigation)->render(); ?>
  </div>
<?php endif; ?>
